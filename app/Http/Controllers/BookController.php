<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

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
}