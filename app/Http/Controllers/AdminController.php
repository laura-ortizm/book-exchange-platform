<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function categories()
    {
        $categories = Category::withCount('books')->orderBy('name')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        Category::create([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'description' => $data['description'],
        ]);

        return back()->with('success', 'Category "' . $data['name'] . '" created.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $category->update([
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']),
            'description' => $data['description'],
        ]);

        return back()->with('success', 'Category "' . $data['name'] . '" updated.');
    }

    public function destroyCategory(Category $category)
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Cannot delete "' . $category->name . '" — it still has books assigned to it.');
        }

        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

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
