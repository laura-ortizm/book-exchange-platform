@extends('layouts.app')

@section('title', 'Choose Exchange Book')

@section('content')

<div class="page-header">
    <h2 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Choose a Book</h2>
</div>


<div class="card mb-4">
    <div class="card-body">
        <p class="mb-1">
            <strong>{{ $exchange->requester->username }}</strong>
            wants your book:
        </p>
        <h5 class="mb-0">{{ $exchange->book->title }}</h5>
        <small class="text-muted">{{ $exchange->book->author }}</small>
    </div>
</div>

<h5 class="mb-3">Available books from {{ $exchange->requester->username }}</h5>

@forelse($requesterBooks as $book)
    @if($loop->first)<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">@endif

    <div class="col">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h6 class="card-title mb-1">{{ $book->title }}</h6>
                <p class="card-text text-muted mb-2">{{ $book->author }}</p>
                <div class="d-flex gap-2 mb-3">
                    <span class="badge bg-secondary">{{ $book->category->name }}</span>
                    <span class="badge
                        {{ $book->condition === 'new'  ? 'bg-success' : '' }}
                        {{ $book->condition === 'good' ? 'bg-primary' : '' }}
                        {{ $book->condition === 'fair' ? 'bg-warning text-dark' : '' }}
                        {{ $book->condition === 'poor' ? 'bg-danger'  : '' }}
                    ">{{ ucfirst($book->condition) }}</span>
                </div>

                @if($book->description)
                    <p class="text-muted small">{{ Str::limit($book->description, 120) }}</p>
                @endif

                <form method="POST" action="{{ route('exchanges.accept', $exchange) }}" class="mt-auto">
                    @csrf
                    <input type="hidden" name="offered_book_id" value="{{ $book->id }}">
                    <button class="btn btn-success btn-sm w-100" type="submit">
                        <i class="bi bi-check-lg me-1"></i>Choose this book
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if($loop->last)</div>@endif
@empty
    <div class="alert alert-warning">
        {{ $exchange->requester->username }} has no available books to exchange right now.
    </div>
@endforelse

<div class="mt-4 d-flex gap-2">
    <a class="btn btn-be-outline" href="{{ route('profile.index') }}#inbox">
        <i class="bi bi-arrow-left me-1"></i>Back to Inbox
    </a>

    <form method="POST" action="{{ route('exchanges.reject', $exchange) }}">
        @csrf
        <button class="btn btn-outline-danger" type="submit">
            <i class="bi bi-x-lg me-1"></i>Reject Request
        </button>
    </form>
</div>

@endsection
