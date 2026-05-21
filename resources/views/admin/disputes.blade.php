@extends('layouts.app')

@section('title', 'Dispute Resolution')

@section('content')

<div class="page-header d-flex align-items-center">
    <h2 class="mb-0"><i class="bi bi-flag me-2"></i>Dispute Resolution</h2>
    <span class="badge bg-danger ms-3 fs-6">{{ $disputes->where('status', 'open')->count() }} open</span>
</div>


<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Exchange</th>
                    <th>Reporter</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disputes as $dispute)
                <tr>
                    <td class="text-muted">#{{ $dispute->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $dispute->exchange->book->title }}</div>
                        @if($dispute->exchange->offeredBook)
                            <small class="text-muted">
                                for {{ $dispute->exchange->offeredBook->title }}
                            </small>
                        @endif
                    </td>
                    <td><i class="bi bi-person-circle me-1"></i>{{ $dispute->reporter->username }}</td>
                    <td><small class="text-muted">{{ $dispute->description }}</small></td>
                    <td>
                        <span class="badge {{ $dispute->status === 'open' ? 'bg-danger' : 'bg-success' }}">
                            {{ ucfirst($dispute->status) }}
                        </span>
                        @if($dispute->resolution)
                            <div><small class="text-muted">{{ ucfirst($dispute->resolution) }}</small></div>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $dispute->created_at->format('M d, Y') }}</small></td>
                    <td class="text-end">
                        @if($dispute->status === 'open')
                            <div class="d-flex gap-2 justify-content-end">
                                <form method="POST" action="{{ route('admin.disputes.accept', $dispute) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-danger" type="submit">
                                        Accept Dispute
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.disputes.reject', $dispute) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success" type="submit">
                                        Reject Dispute
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-muted small">Resolved</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No disputes yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
