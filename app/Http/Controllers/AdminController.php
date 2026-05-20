<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function disputes()
    {
        $disputes = Dispute::with(['exchange.book', 'exchange.offeredBook', 'reporter'])
            ->latest()
            ->get();

        return view('admin.disputes', compact('disputes'));
    }

    public function acceptDispute(Dispute $dispute)
    {
        if ($dispute->status !== 'open') {
            return back()->with('error', 'This dispute has already been resolved.');
        }

        DB::transaction(function () use ($dispute) {
            $exchange = $dispute->exchange()->with(['book', 'offeredBook'])->first();

            $dispute->update([
                'status'     => 'resolved',
                'admin_id'   => auth()->id(),
                'resolution' => 'accepted',
            ]);

            $exchange->update(['status' => 'cancelled']);
            $exchange->book->update(['status' => 'available']);

            if ($exchange->offeredBook) {
                $exchange->offeredBook->update(['status' => 'available']);
            }
        });

        return back()->with('success', 'Dispute accepted. Exchange cancelled and books returned to available.');
    }

    public function rejectDispute(Dispute $dispute)
    {
        if ($dispute->status !== 'open') {
            return back()->with('error', 'This dispute has already been resolved.');
        }

        DB::transaction(function () use ($dispute) {
            $exchange = $dispute->exchange()->with(['book', 'offeredBook'])->first();

            $dispute->update([
                'status'     => 'resolved',
                'admin_id'   => auth()->id(),
                'resolution' => 'rejected',
            ]);

            $exchange->update(['status' => 'accepted']);
            $exchange->book->update(['status' => 'exchanged']);

            if ($exchange->offeredBook) {
                $exchange->offeredBook->update(['status' => 'exchanged']);
            }
        });

        return back()->with('success', 'Dispute rejected. Exchange completed.');
    }
}
