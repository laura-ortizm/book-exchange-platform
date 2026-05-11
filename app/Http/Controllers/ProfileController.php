<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $books    = $user->books()->with('category')->latest()->get();
        $incoming = $user->incomingExchanges()->with(['book', 'requester'])->latest()->get();
        $outgoing = $user->outgoingExchanges()->with(['book', 'owner'])->latest()->get();

        return view('profile.index', compact('user', 'books', 'incoming', 'outgoing'));
    }
}
