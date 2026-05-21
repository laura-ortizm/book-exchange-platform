<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $books    = $user->books()->with('category')->latest()->get();

        $allIncoming = $user->incomingExchanges()->with(['book', 'requester', 'offeredBook'])->latest()->get();
        $allOutgoing = $user->outgoingExchanges()->with(['book', 'owner', 'offeredBook'])->latest()->get();

        $doneStatuses = ['accepted', 'rejected', 'cancelled'];
        $incoming     = $allIncoming->whereNotIn('status', $doneStatuses);
        $outgoing     = $allOutgoing->whereNotIn('status', $doneStatuses);
        $completed    = $allIncoming->whereIn('status', $doneStatuses)
                            ->merge($allOutgoing->whereIn('status', $doneStatuses))
                            ->sortByDesc('updated_at');

        return view('profile.index', compact('user', 'books', 'incoming', 'outgoing', 'completed'));
    }
}
