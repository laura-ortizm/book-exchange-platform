<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Dispute;
use App\Models\Exchange;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    // Show the inbox - incoming and outgoing exchanges
    public function inbox()
    {
        $user     = auth()->user();
        $incoming = $user->incomingExchanges()->with(['book', 'requester'])->latest()->get();
        $outgoing = $user->outgoingExchanges()->with(['book', 'owner'])->latest()->get();

        return view('exchanges.inbox', compact('incoming', 'outgoing'));
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

        // Mark book as pending so nobody else can request it
        $book->update(['status' => 'pending']);

        return back()->with('success', 'Exchange requested successfully!');
    }

    // Accept an exchange request
    public function accept(Exchange $exchange)
    {
        // Only the book owner can accept
        if ($exchange->owner_id !== auth()->id()) {
            abort(403);
        }

        $exchange->update(['status' => 'accepted']);

        // Delete the book from the platform
        $exchange->book->delete();

        return redirect()->route('exchanges.inbox')
            ->with('success', 'Exchange accepted! The book has been removed.');
    }

    // Reject an exchange request
    public function reject(Exchange $exchange)
    {
        // Only the book owner can reject
        if ($exchange->owner_id !== auth()->id()) {
            abort(403);
        }

        $exchange->update(['status' => 'rejected']);

        // Put the book back to available
        $exchange->book->update(['status' => 'available']);

        return redirect()->route('exchanges.inbox')
            ->with('success', 'Exchange rejected.');
    }

    // Open a dispute
    public function dispute(Request $request, Exchange $exchange)
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
        ]);

        // Check no dispute already exists for this exchange
        if ($exchange->dispute) {
            return back()->with('error', 'A dispute already exists for this exchange.');
        }

        Dispute::create([
            'exchange_id' => $exchange->id,
            'reporter_id' => auth()->id(),
            'description' => $data['description'],
            'status'      => 'open',
        ]);

        return redirect()->route('exchanges.inbox')
            ->with('success', 'Dispute opened. An admin will review it shortly.');
    }
}