@extends('layouts.app')

@section('title', 'Exchange Details')

@section('content')

@php
    $isRequester = auth()->id() === $exchange->requester_id;
    $currentUserConfirmed = $isRequester ? $exchange->requester_confirmed_at : $exchange->owner_confirmed_at;
@endphp

<div class="page-header">
    <h2 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Exchange Details</h2>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h5 class="mb-1">{{ $exchange->book->title }}</h5>
                <small class="text-muted">Requested book from {{ $exchange->owner->username }}</small>
            </div>
            <span class="badge
                {{ $exchange->status === 'pending' ? 'bg-warning text-dark' : '' }}
                {{ $exchange->status === 'in_progress' ? 'bg-info text-dark' : '' }}
                {{ $exchange->status === 'accepted' ? 'bg-success' : '' }}
                {{ $exchange->status === 'rejected' ? 'bg-danger' : '' }}
                {{ $exchange->status === 'cancelled' ? 'bg-secondary' : '' }}
            ">{{ ucfirst(str_replace('_', ' ', $exchange->status)) }}</span>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <h6 class="text-muted text-uppercase small mb-2">Requested Book</h6>
                    <div class="fw-semibold">{{ $exchange->book->title }}</div>
                    <small class="text-muted">{{ $exchange->book->author }}</small>
                    <div class="mt-2">Owner: <strong>{{ $exchange->owner->username }}</strong></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <h6 class="text-muted text-uppercase small mb-2">Offered Book</h6>
                    @if($exchange->offeredBook)
                        <div class="fw-semibold">{{ $exchange->offeredBook->title }}</div>
                        <small class="text-muted">{{ $exchange->offeredBook->author }}</small>
                    @else
                        <div class="text-muted">No offered book selected yet.</div>
                    @endif
                    <div class="mt-2">Requester: <strong>{{ $exchange->requester->username }}</strong></div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="mb-2">Confirmation Status</h6>
                <p class="mb-1">
                    {{ $exchange->requester->username }}:
                    <strong>{{ $exchange->requester_confirmed_at ? 'Confirmed' : 'Not confirmed yet' }}</strong>
                </p>
                <p class="mb-0">
                    {{ $exchange->owner->username }}:
                    <strong>{{ $exchange->owner_confirmed_at ? 'Confirmed' : 'Not confirmed yet' }}</strong>
                </p>
            </div>

            <div class="col-md-6">
                <h6 class="mb-2">Dispute Status</h6>
                @if($exchange->dispute)
                    <p class="mb-0">
                        <strong>{{ ucfirst($exchange->dispute->status) }}</strong>
                        <span class="text-muted">- {{ $exchange->dispute->description }}</span>
                    </p>
                @else
                    <p class="text-muted mb-0">No dispute opened.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if($exchange->status === 'in_progress')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="mb-3">Actions</h5>

        <div class="d-flex gap-2 flex-wrap">
            @if(! $exchange->dispute)
                <button class="btn btn-outline-secondary btn-sm"
                        data-bs-toggle="collapse"
                        data-bs-target="#dispute-form">
                    <i class="bi bi-flag me-1"></i>Open Dispute
                </button>
            @endif

            @if(! $currentUserConfirmed)
                <form method="POST" action="{{ route('exchanges.confirm', $exchange) }}">
                    @csrf
                    <button class="btn btn-success btn-sm" type="submit">
                        <i class="bi bi-check-lg me-1"></i>Confirm / Satisfied with Exchange
                    </button>
                </form>
            @else
                <span class="text-muted small align-self-center">You already confirmed this exchange.</span>
            @endif
        </div>

        @if(! $exchange->dispute)
            <div class="collapse mt-3" id="dispute-form">
                <form method="POST" action="{{ route('exchanges.dispute', $exchange) }}">
                    @csrf
                    <textarea class="form-control mb-2" name="description" rows="3"
                        placeholder="Describe the issue..." required></textarea>
                    <button class="btn btn-warning btn-sm" type="submit">
                        <i class="bi bi-flag me-1"></i>Submit Dispute
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endif

<a class="btn btn-be-outline" href="{{ route('profile.index') }}">
    <i class="bi bi-arrow-left me-1"></i>Back to Profile
</a>

@endsection
