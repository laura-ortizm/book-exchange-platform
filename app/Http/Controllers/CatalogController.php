<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['category', 'owner'])
            ->where('status', 'available');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("title COLLATE utf8mb4_uca1400_ai_ci LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("author COLLATE utf8mb4_uca1400_ai_ci LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $books      = $query->latest()->paginate(9)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('catalog.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load(['category', 'owner']);

        return view('books.show', compact('book'));
    }
}
