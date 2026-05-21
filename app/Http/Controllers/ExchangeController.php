<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Dispute;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    // Show the inbox - incoming and outgoing exchanges
    public function inbox()
    {
        $user     = auth()->user();
        $incoming = $user->incomingExchanges()->with(['book', 'requester', 'offeredBook', 'dispute'])->latest()->get();
        $outgoing = $user->outgoingExchanges()->with(['book', 'owner', 'offeredBook', 'dispute'])->latest()->get();

        return view('exchanges.inbox', compact('incoming', 'outgoing'));
    }

    // Show one exchange with its books, users and actions
    public function show(Exchange $exchange)
    {
        if (! in_array(auth()->id(), [$exchange->requester_id, $exchange->owner_id], true)) {
            abort(403);
        }

        $exchange->load(['book', 'offeredBook', 'requester', 'owner', 'dispute']);

        return view('exchanges.show', compact('exchange'));
    }

    // Store a new exchange request
    public function store(Request $request, Book $book)
    {
        // Make sure the requester isn't the book owner
        if ($book->user_id === auth()->id()) {
            return back()->with('error', 'You cannot request your own book.');
        }

        // Make sure the book is still available
        if ($book->status !== 'available') {
            return back()->with('error', 'This book is no longer available.');
        }

        // Make sure the user hasn't already requested this book
        $exists = Exchange::where('book_id', $book->id)
            ->where('requester_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have a pending request for this book.');
        }

        Exchange::create([
            'requester_id' => auth()->id(),
            'book_id'      => $book->id,
            'owner_id'     => $book->user_id,
            'message'      => $request->input('message'),
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Exchange requested successfully!');
    }

    // Show requester books so the owner can choose what they want in return
    public function chooseBook(Exchange $exchange)
    {
        if ($exchange->owner_id !== auth()->id()) {
            abort(403);
        }

        if ($exchange->status !== 'pending') {
            return redirect()->route('exchanges.inbox')
                ->with('error', 'Only pending exchanges can be accepted.');
        }

        $exchange->load(['book', 'requester']);

        $requesterBooks = Book::with('category')
            ->where('user_id', $exchange->requester_id)
            ->where('status', 'available')
            ->latest()
            ->get();

        return view('exchanges.choose-book', compact('exchange', 'requesterBooks'));
    }

    // Accept an exchange request after choosing the requester's book
    public function accept(Request $request, Exchange $exchange)
    {
        // Only the book owner can accept
        if ($exchange->owner_id !== auth()->id()) {
            abort(403);
        }

        if ($exchange->status !== 'pending') {
            return redirect()->route('exchanges.inbox')
                ->with('error', 'Only pending exchanges can be accepted.');
        }

        $data = $request->validate([
            'offered_book_id' => ['required', 'exists:books,id'],
        ]);

        $offeredBook = Book::where('id', $data['offered_book_id'])
            ->where('user_id', $exchange->requester_id)
            ->where('status', 'available')
            ->first();

        if (! $offeredBook) {
            return back()->with('error', 'Please choose an available book from the requester.');
        }

        DB::transaction(function () use ($exchange, $offeredBook) {
            $exchange->update([
                'offered_book_id' => $offeredBook->id,
                'status'          => 'in_progress',
            ]);

            // Reject all other pending requests for the same book
            Exchange::where('book_id', $exchange->book_id)
                ->where('id', '!=', $exchange->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            $exchange->book->update(['status' => 'pending']);
            $offeredBook->update(['status' => 'pending']);
        });

        return redirect()->route('exchanges.inbox')
            ->with('success', 'Exchange accepted! The exchange is now in progress.');
    }

    // Reject an exchange request
    public function reject(Exchange $exchange)
    {
        // Only the book owner can reject
        if ($exchange->owner_id !== auth()->id()) {
            abort(403);
        }

        if ($exchange->status !== 'pending') {
            return redirect()->route('exchanges.inbox')
                ->with('error', 'Only pending exchanges can be rejected.');
        }

        $exchange->update(['status' => 'rejected']);

        return redirect()->route('exchanges.inbox')
            ->with('success', 'Exchange rejected.');
    }

    // Mark the current user as satisfied with the exchange
    public function confirm(Exchange $exchange)
    {
        if (! in_array(auth()->id(), [$exchange->requester_id, $exchange->owner_id], true)) {
            abort(403);
        }

        if ($exchange->status !== 'in_progress') {
            return back()->with('error', 'Only exchanges in progress can be confirmed.');
        }

        DB::transaction(function () use ($exchange) {
            $exchange->load(['book', 'offeredBook']);

            if (auth()->id() === $exchange->requester_id && ! $exchange->requester_confirmed_at) {
                $exchange->requester_confirmed_at = now();
            }

            if (auth()->id() === $exchange->owner_id && ! $exchange->owner_confirmed_at) {
                $exchange->owner_confirmed_at = now();
            }

            if ($exchange->requester_confirmed_at && $exchange->owner_confirmed_at && $exchange->offeredBook) {
                $exchange->status = 'accepted';
                $exchange->book->update(['status' => 'exchanged']);
                $exchange->offeredBook->update(['status' => 'exchanged']);
            }

            $exchange->save();
        });

        return back()->with('success', 'Your confirmation has been saved.');
    }

    //Open a dispute
    public function dispute(Request $request, Exchange $exchange)
    {
        if (! in_array(auth()->id(), [$exchange->requester_id, $exchange->owner_id], true)) {
            abort(403);
        }

        if ($exchange->status !== 'in_progress') {
            return back()->with('error', 'Disputes can only be opened for exchanges in progress.');
        }

        $data = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
        ]);

        //Check no dispute already exists for this exchange
        if ($exchange->dispute) {
            return back()->with('error', 'A dispute already exists for this exchange.');
        }

        Dispute::create([
            'exchange_id' => $exchange->id,
            'reporter_id' => auth()->id(),
            'description' => $data['description'],
            'status'      => 'open',
        ]);

        return back()->with('success', 'Dispute opened. An admin will review it shortly.');
    }
}
