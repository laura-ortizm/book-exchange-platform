@extends('layouts.app')

@section('title', $book->title)

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Catalog</a></li>
        <li class="breadcrumb-item active">{{ $book->title }}</li>
    </ol>
</nav>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="row g-0 align-items-stretch">

        {{-- Cover column --}}
        <div class="col-md-5 col-lg-4">
            @if($book->cover_image)
                <img src="{{ Storage::url($book->cover_image) }}"
                     alt="{{ $book->title }}"
                     class="book-detail-cover">
            @else
                <div class="book-cover cover-{{ ($book->id % 6) + 1 }} book-detail-cover-placeholder">
                    <i class="bi bi-book"></i>
                </div>
            @endif

        </div>

        {{-- Info column --}}
        <div class="col-md-7 col-lg-8 d-flex flex-column">
            <div class="card-body p-4 p-lg-5 flex-grow-1">

                {{-- Title & author --}}
                <h1 class="book-detail-title">{{ $book->title }}</h1>
                <p class="book-detail-author">{{ $book->author }}</p>

                {{-- Meta row --}}
                <div class="book-detail-meta">
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="bi bi-star me-1"></i>Condition</span>
                        <span class="badge
                            {{ $book->condition === 'new'  ? 'bg-success' : '' }}
                            {{ $book->condition === 'good' ? 'bg-primary' : '' }}
                            {{ $book->condition === 'fair' ? 'bg-warning text-dark' : '' }}
                            {{ $book->condition === 'poor' ? 'bg-danger'  : '' }}
                        ">{{ ucfirst($book->condition) }}</span>
                    </div>
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="bi bi-tag me-1"></i>Category</span>
                        <a href="{{ route('catalog.index', ['category' => $book->category->slug]) }}"
                           class="badge bg-secondary text-decoration-none">
                            {{ $book->category->name }}
                        </a>
                    </div>
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="bi bi-circle-fill me-1"></i>Status</span>
                        <span class="badge
                            {{ $book->status === 'available' ? 'bg-success' : '' }}
                            {{ $book->status === 'pending'   ? 'bg-warning text-dark' : '' }}
                            {{ $book->status === 'exchanged' ? 'bg-secondary' : '' }}
                        ">{{ ucfirst($book->status) }}</span>
                    </div>
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="bi bi-person me-1"></i>Owner</span>
                        <span>{{ $book->owner->username }}</span>
                    </div>
                    @if($book->isbn)
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="bi bi-upc me-1"></i>ISBN</span>
                        <span class="text-muted">{{ $book->isbn }}</span>
                    </div>
                    @endif
                    <div class="book-meta-item">
                        <span class="book-meta-label"><i class="bi bi-calendar me-1"></i>Listed on</span>
                        <span class="text-muted">{{ $book->created_at->format('F j, Y') }}</span>
                    </div>
                </div>

                {{-- Description --}}
                @if($book->description)
                <div class="book-detail-description">
                    <p class="mb-0">{{ $book->description }}</p>
                </div>
                @endif

            </div>

            {{-- Action footer --}}
            <div class="book-detail-actions">
                @auth
                    @if(auth()->user()->id !== $book->user_id && $book->status === 'available')
                        <button class="btn btn-be btn-lg">
                            <i class="bi bi-arrow-left-right me-2"></i>Request Exchange
                        </button>
                    @elseif(auth()->user()->id === $book->user_id)
                        <span class="text-muted fst-italic">
                            <i class="bi bi-info-circle me-1"></i>This is your book
                        </span>
                    @endif
                @else
                    <a class="btn btn-be btn-lg" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Log in to request exchange
                    </a>
                @endauth

                <a class="btn btn-be-outline" href="{{ route('catalog.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>Back to Catalog
                </a>
            </div>
        </div>

    </div>
</div>

@endsection
