<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'author'      => ['required', 'string', 'max:200'],
            'isbn'        => ['nullable', 'string', 'max:20'],
            'category_id' => ['required', 'exists:categories,id'],
            'condition'   => ['required', 'in:new,good,fair,poor'],
            'description' => ['required', 'string', 'max:1000'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        $data['user_id'] = auth()->id();
        $data['status']  = 'available';

        Book::create($data);

        return redirect()->route('profile.index')
            ->with('success', 'Book published successfully!');
    }

    public function destroy(Book $book)
    {
        abort_unless($book->user_id === auth()->id(), 403);

        $activeExchanges = Exchange::where(function ($q) use ($book) {
            $q->where('book_id', $book->id)
              ->orWhere('offered_book_id', $book->id);
        })->whereIn('status', ['pending', 'in_progress'])->exists();

        if ($activeExchanges) {
            return redirect()->route('profile.index')
                ->with('error', 'Cannot remove a book with active exchange requests.');
        }

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('profile.index')
            ->with('success', 'Book removed successfully.');
    }
}