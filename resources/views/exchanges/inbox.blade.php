@extends('layouts.app')

@section('title', 'Inbox')

@section('content')

<div class="page-header">
    <h2 class="mb-0"><i class="bi bi-inbox me-2"></i>Inbox</h2>
</div>

{{-- Success/error messages --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- INCOMING requests (someone wants your book) --}}
<h5 class="mb-3">Incoming Requests</h5>

@forelse($incoming as $exchange)
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <strong>{{ $exchange->requester->username }}</strong>
                wants your book
                <strong>{{ $exchange->book->title }}</strong>
                @if($exchange->message)
                    <p class="text-muted mt-1 mb-0"><em>"{{ $exchange->message }}"</em></p>
                @endif
            </div>
            <span class="badge
                {{ $exchange->status === 'pending'  ? 'bg-warning text-dark' : '' }}
                {{ $exchange->status === 'accepted' ? 'bg-success' : '' }}
                {{ $exchange->status === 'rejected' ? 'bg-danger'  : '' }}
            ">{{ ucfirst($exchange->status) }}</span>
        </div>

        {{-- Actions only for pending exchanges --}}
        @if($exchange->status === 'pending')
        <div class="d-flex gap-2 mt-3">
            {{-- Accept --}}
            <form method="POST" action="{{ route('exchanges.accept', $exchange) }}">
                @csrf
                <button class="btn btn-success btn-sm" type="submit">
                    <i class="bi bi-check-lg me-1"></i>Accept
                </button>
            </form>

            {{-- Reject --}}
            <form method="POST" action="{{ route('exchanges.reject', $exchange) }}">
                @csrf
                <button class="btn btn-danger btn-sm" type="submit">
                    <i class="bi bi-x-lg me-1"></i>Reject
                </button>
            </form>

            {{-- Dispute --}}
            <button class="btn btn-outline-secondary btn-sm"
                    data-bs-toggle="collapse"
                    data-bs-target="#dispute-{{ $exchange->id }}">
                <i class="bi bi-flag me-1"></i>Open Dispute
            </button>
        </div>

        {{-- Dispute form (hidden until button clicked) --}}
        <div class="collapse mt-3" id="dispute-{{ $exchange->id }}">
            <form method="POST" action="{{ route('exchanges.dispute', $exchange) }}">
                @csrf
                <textarea class="form-control mb-2" name="description" rows="2"
                    placeholder="Describe the issue..." required></textarea>
                <button class="btn btn-warning btn-sm" type="submit">
                    <i class="bi bi-flag me-1"></i>Submit Dispute
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@empty
    <p class="text-muted">No incoming requests yet.</p>
@endforelse

<hr class="my-4">

{{-- OUTGOING requests (books you requested) --}}
<h5 class="mb-3">My Requests</h5>

@forelse($outgoing as $exchange)
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            You requested <strong>{{ $exchange->book->title }}</strong>
            from <strong>{{ $exchange->owner->username }}</strong>
            @if($exchange->message)
                <p class="text-muted mt-1 mb-0"><em>"{{ $exchange->message }}"</em></p>
            @endif
        </div>
        <span class="badge
            {{ $exchange->status === 'pending'  ? 'bg-warning text-dark' : '' }}
            {{ $exchange->status === 'accepted' ? 'bg-success' : '' }}
            {{ $exchange->status === 'rejected' ? 'bg-danger'  : '' }}
        ">{{ ucfirst($exchange->status) }}</span>
    </div>
</div>
@empty
    <p class="text-muted">You haven't requested any books yet.</p>
@endforelse

@endsection