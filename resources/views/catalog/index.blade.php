@extends('layouts.app')

@section('title', 'Book Catalog')

@section('content')

<div class="d-flex justify-content-between align-items-center page-header">
    <h2 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Book Catalog</h2>
    <small class="text-muted">{{ $books->total() }} {{ Str::plural('book', $books->total()) }} found</small>
</div>

{{-- Active filters --}}
@if(request('q') || request('category'))
    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
        <span class="text-muted small">Filtering by:</span>
        @if(request('q'))
            <span class="badge bg-secondary">
                "{{ request('q') }}"
                <a href="{{ route('catalog.index', array_filter(['category' => request('category')])) }}"
                   class="text-white ms-1 text-decoration-none">&times;</a>
            </span>
        @endif
        @if(request('category'))
            <span class="badge bg-secondary">
                {{ $categories->firstWhere('slug', request('category'))?->name }}
                <a href="{{ route('catalog.index', array_filter(['q' => request('q')])) }}"
                   class="text-white ms-1 text-decoration-none">&times;</a>
            </span>
        @endif
        <a class="small" href="{{ route('catalog.index') }}">Clear all</a>
    </div>
@endif

{{-- Book grid --}}
@forelse($books as $book)
    @if($loop->first)<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">@endif

    <div class="col">
        <div class="card book-card">
            @if($book->cover_image)
                <img src="{{ Storage::url($book->cover_image) }}"
                     class="card-img-top" style="aspect-ratio:1/1; width:100%; object-fit:cover;"
                     alt="{{ $book->title }}">
            @else
                <div class="book-cover cover-{{ ($book->id % 6) + 1 }}">
                    <i class="bi bi-book"></i>
                </div>
            @endif
            <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-semibold mb-1">{{ $book->title }}</h6>
                <p class="card-text text-muted small mb-2">{{ $book->author }}</p>
                <div class="d-flex gap-2 mb-3">
                    <span class="badge bg-secondary">{{ $book->category->name }}</span>
                    <span class="badge
                        {{ $book->condition === 'new'  ? 'bg-success' : '' }}
                        {{ $book->condition === 'good' ? 'bg-primary' : '' }}
                        {{ $book->condition === 'fair' ? 'bg-warning text-dark' : '' }}
                        {{ $book->condition === 'poor' ? 'bg-danger'  : '' }}
                    ">{{ ucfirst($book->condition) }}</span>
                </div>
                <a href="{{ route('books.show', $book) }}"
                   class="btn btn-be btn-sm mt-auto">
                    <i class="bi bi-eye me-1"></i>View Details
                </a>
            </div>
        </div>
    </div>

    @if($loop->last)</div>@endif

@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-search" style="font-size:3rem;"></i>
        <p class="mt-3 mb-1 fs-5">No books found</p>
        <p class="small">Try a different search term or category.</p>
        <a class="btn btn-be-outline mt-2" href="{{ route('catalog.index') }}">
            View all books
        </a>
    </div>
@endforelse

{{-- Pagination --}}
@if($books->hasPages())
    <div class="mt-5 d-flex flex-column align-items-center gap-2">
        {{ $books->links() }}
        <small class="text-muted">
            Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }} results
        </small>
    </div>
@endif

@endsection
