@extends('layouts.app')

@section('title', 'Inbox')

@section('content')

<div class="page-header">
    <h2 class="mb-0"><i class="bi bi-inbox me-2"></i>Inbox</h2>
</div>

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
                {{ $exchange->status === 'pending'     ? 'bg-warning text-dark' : '' }}
                {{ $exchange->status === 'in_progress' ? 'bg-info text-dark'    : '' }}
            ">{{ ucfirst(str_replace('_', ' ', $exchange->status)) }}</span>
        </div>

        @if($exchange->status === 'pending')
        <div class="d-flex gap-2 mt-3">
            <a class="btn btn-success btn-sm" href="{{ route('exchanges.choose-book', $exchange) }}">
                <i class="bi bi-check-lg me-1"></i>Accept
            </a>
            <form method="POST" action="{{ route('exchanges.reject', $exchange) }}">
                @csrf
                <button class="btn btn-danger btn-sm" type="submit">
                    <i class="bi bi-x-lg me-1"></i>Reject
                </button>
            </form>
        </div>
        @endif

        @if($exchange->status === 'in_progress' && ! $exchange->dispute)
        <div class="mt-3">
            <button class="btn btn-outline-secondary btn-sm"
                    data-bs-toggle="collapse"
                    data-bs-target="#dispute-incoming-{{ $exchange->id }}">
                <i class="bi bi-flag me-1"></i>Open Dispute
            </button>
            <div class="collapse mt-3" id="dispute-incoming-{{ $exchange->id }}">
                <form method="POST" action="{{ route('exchanges.dispute', $exchange) }}">
                    @csrf
                    <textarea class="form-control mb-2" name="description" rows="2"
                        placeholder="Describe the issue..." required></textarea>
                    <button class="btn btn-warning btn-sm" type="submit">
                        <i class="bi bi-flag me-1"></i>Submit Dispute
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($exchange->status === 'in_progress' && $exchange->offeredBook)
            <p class="text-muted small mt-3 mb-0">
                <i class="bi bi-arrow-left-right me-1"></i>
                You chose <strong>{{ $exchange->offeredBook->title }}</strong> in exchange.
            </p>
        @endif

        @if($exchange->dispute)
            <p class="text-muted small mt-2 mb-0">
                <i class="bi bi-flag me-1"></i>
                Dispute status: <strong>{{ ucfirst($exchange->dispute->status) }}</strong>
            </p>
        @endif
    </div>
</div>
@empty
    <p class="text-muted">No incoming requests.</p>
@endforelse

<hr class="my-4">

{{-- OUTGOING requests (books you requested) --}}
<h5 class="mb-3">My Requests</h5>

@forelse($outgoing as $exchange)
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                You requested <strong>{{ $exchange->book->title }}</strong>
                from <strong>{{ $exchange->owner->username }}</strong>
                @if($exchange->status === 'in_progress' && $exchange->offeredBook)
                    <p class="text-muted mt-1 mb-0">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        Your book <strong>{{ $exchange->offeredBook->title }}</strong> was chosen.
                    </p>
                @endif
                @if($exchange->message)
                    <p class="text-muted mt-1 mb-0"><em>"{{ $exchange->message }}"</em></p>
                @endif
            </div>
            <span class="badge
                {{ $exchange->status === 'pending'     ? 'bg-warning text-dark' : '' }}
                {{ $exchange->status === 'in_progress' ? 'bg-info text-dark'    : '' }}
            ">{{ ucfirst(str_replace('_', ' ', $exchange->status)) }}</span>
        </div>

        @if($exchange->status === 'in_progress' && ! $exchange->dispute)
        <div class="mt-3">
            <button class="btn btn-outline-secondary btn-sm"
                    data-bs-toggle="collapse"
                    data-bs-target="#dispute-outgoing-{{ $exchange->id }}">
                <i class="bi bi-flag me-1"></i>Open Dispute
            </button>
            <div class="collapse mt-3" id="dispute-outgoing-{{ $exchange->id }}">
                <form method="POST" action="{{ route('exchanges.dispute', $exchange) }}">
                    @csrf
                    <textarea class="form-control mb-2" name="description" rows="2"
                        placeholder="Describe the issue..." required></textarea>
                    <button class="btn btn-warning btn-sm" type="submit">
                        <i class="bi bi-flag me-1"></i>Submit Dispute
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($exchange->dispute)
            <p class="text-muted small mt-2 mb-0">
                <i class="bi bi-flag me-1"></i>
                Dispute status: <strong>{{ ucfirst($exchange->dispute->status) }}</strong>
            </p>
        @endif
    </div>
</div>
@empty
    <p class="text-muted">You haven't requested any books yet.</p>
@endforelse

<hr class="my-4">

{{-- COMPLETED exchanges --}}
<h5 class="mb-3">Completed Exchanges</h5>

@forelse($completed as $exchange)
@php $isIncoming = $exchange->owner_id === auth()->id(); @endphp
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                @if($isIncoming)
                    <strong>{{ $exchange->requester->username }}</strong>
                    requested your book
                    <strong>{{ $exchange->book->title }}</strong>
                @else
                    You requested <strong>{{ $exchange->book->title }}</strong>
                    from <strong>{{ $exchange->owner->username }}</strong>
                @endif

                @if($exchange->offeredBook)
                    <p class="text-muted small mt-1 mb-0">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        Exchanged for <strong>{{ $exchange->offeredBook->title }}</strong>
                    </p>
                @endif
            </div>
            <span class="badge
                {{ $exchange->status === 'accepted'  ? 'bg-success'   : '' }}
                {{ $exchange->status === 'rejected'  ? 'bg-danger'    : '' }}
                {{ $exchange->status === 'cancelled' ? 'bg-secondary' : '' }}
            ">{{ ucfirst($exchange->status) }}</span>
        </div>

        @if($exchange->dispute)
            <p class="text-muted small mt-2 mb-0">
                <i class="bi bi-flag me-1"></i>
                Dispute: <strong>{{ ucfirst($exchange->dispute->status) }}</strong>
            </p>
        @endif
    </div>
</div>
@empty
    <p class="text-muted">No completed exchanges yet.</p>
@endforelse

@endsection
