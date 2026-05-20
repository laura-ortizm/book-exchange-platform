@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

{{-- Profile header --}}
<div class="page-header d-flex align-items-center gap-3">
    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
         style="width:56px; height:56px; font-size:1.6rem;">
        <i class="bi bi-person-fill"></i>
    </div>
    <div>
        <h2 class="mb-0">{{ $user->username }} <span class="badge bg-secondary fs-6">{{ $user->role }}</span></h2>
        <small class="text-muted">Member since {{ $user->created_at->format('F Y') }}</small>
    </div>
    <a class="btn btn-be btn-sm ms-auto" href="{{ route('books.create') }}">
        <i class="bi bi-plus-circle me-1"></i>Publish a Book
    </a>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="profileTabs">
    <li class="nav-item">
        <a class="nav-link active" href="#my-books" data-bs-toggle="tab">
            <i class="bi bi-collection me-1"></i>My Books
            <span class="badge bg-secondary ms-1">{{ $books->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#inbox" data-bs-toggle="tab">
            <i class="bi bi-inbox me-1"></i>Inbox
            @if($incoming->where('status', 'pending')->count())
                <span class="badge be-badge ms-1">{{ $incoming->where('status', 'pending')->count() }}</span>
            @else
                <span class="badge bg-secondary ms-1">{{ $incoming->count() + $outgoing->count() }}</span>
            @endif
        </a>
    </li>
</ul>

<div class="tab-content">

    {{-- My Books tab --}}
    <div class="tab-pane fade show active" id="my-books">
        @if($books->isEmpty())
            <p class="text-muted">You haven't published any books yet.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Book</th>
                        <th>Category</th>
                        <th>Condition</th>
                        <th>Status</th>
                        <th>Listed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td>
                            <a class="fw-semibold text-decoration-none" href="{{ route('books.show', $book) }}">
                                {{ $book->title }}
                            </a>
                            <br><small class="text-muted">{{ $book->author }}</small>
                        </td>
                        <td><small class="text-muted">{{ $book->category->name }}</small></td>
                        <td>
                            <span class="badge
                                {{ $book->condition === 'new'  ? 'bg-primary' : '' }}
                                {{ $book->condition === 'good' ? 'bg-primary' : '' }}
                                {{ $book->condition === 'fair' ? 'bg-warning text-dark' : '' }}
                                {{ $book->condition === 'poor' ? 'bg-danger' : '' }}
                            ">{{ ucfirst($book->condition) }}</span>
                        </td>
                        <td>
                            <span class="badge
                                {{ $book->status === 'available' ? 'bg-success' : '' }}
                                {{ $book->status === 'pending'   ? 'bg-warning text-dark' : '' }}
                                {{ $book->status === 'exchanged' ? 'bg-secondary' : '' }}
                            ">{{ ucfirst($book->status) }}</span>
                        </td>
                        <td><small class="text-muted">{{ $book->created_at->format('M d, Y') }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Inbox tab --}}
    <div class="tab-pane fade" id="inbox">

        <h6 class="text-muted text-uppercase small mb-3">
            <i class="bi bi-arrow-down-left-circle me-1"></i>Incoming Requests
        </h6>
        @if($incoming->isEmpty())
            <p class="text-muted mb-4">No incoming requests.</p>
        @else
        <div class="table-responsive mb-4">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Book requested</th>
                        <th>Actions</th>
                        <th>From</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incoming as $exchange)
                    <tr>
                        <td class="fw-semibold">{{ $exchange->book->title }}</td>
                        <td>
                            @if($exchange->status === 'pending')
                                <div class="d-flex gap-2 flex-wrap">
                                    <a class="btn btn-success btn-sm" href="{{ route('exchanges.choose-book', $exchange) }}">
                                        <i class="bi bi-check-lg me-1"></i>Accept / Choose Book
                                    </a>
                                    <form method="POST" action="{{ route('exchanges.reject', $exchange) }}">
                                        @csrf
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="bi bi-x-lg me-1"></i>Reject
                                        </button>
                                    </form>
                                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('exchanges.show', $exchange) }}">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                </div>
                            @else
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('exchanges.show', $exchange) }}">
                                    <i class="bi bi-eye me-1"></i>View Details
                                </a>
                            @endif
                        </td>
                        <td><i class="bi bi-person-circle me-1"></i>{{ $exchange->requester->username }}</td>
                        <td><small class="text-muted">{{ $exchange->message ?? '—' }}</small></td>
                        <td>
                            <span class="badge
                                {{ $exchange->status === 'pending'  ? 'bg-warning text-dark' : '' }}
                                {{ $exchange->status === 'in_progress' ? 'bg-info text-dark' : '' }}
                                {{ $exchange->status === 'accepted' ? 'bg-success' : '' }}
                                {{ $exchange->status === 'rejected' ? 'bg-danger' : '' }}
                                {{ $exchange->status === 'cancelled' ? 'bg-secondary' : '' }}
                            ">{{ ucfirst(str_replace('_', ' ', $exchange->status)) }}</span>
                        </td>
                        <td><small class="text-muted">{{ $exchange->created_at->format('M d, Y') }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <h6 class="text-muted text-uppercase small mb-3">
            <i class="bi bi-arrow-up-right-circle me-1"></i>Outgoing Requests
        </h6>
        @if($outgoing->isEmpty())
            <p class="text-muted">You haven't requested any books yet.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Book requested</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outgoing as $exchange)
                    <tr>
                        <td class="fw-semibold">{{ $exchange->book->title }}</td>
                        <td><i class="bi bi-person-circle me-1"></i>{{ $exchange->owner->username }}</td>
                        <td>
                            <span class="badge
                                {{ $exchange->status === 'pending'  ? 'bg-warning text-dark' : '' }}
                                {{ $exchange->status === 'in_progress' ? 'bg-info text-dark' : '' }}
                                {{ $exchange->status === 'accepted' ? 'bg-success' : '' }}
                                {{ $exchange->status === 'rejected' ? 'bg-danger' : '' }}
                                {{ $exchange->status === 'cancelled' ? 'bg-secondary' : '' }}
                            ">{{ ucfirst(str_replace('_', ' ', $exchange->status)) }}</span>
                        </td>
                        <td>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('exchanges.show', $exchange) }}">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                        </td>
                        <td><small class="text-muted">{{ $exchange->created_at->format('M d, Y') }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const profilePath = @json(parse_url(route('profile.index'), PHP_URL_PATH));
    function showProfileTab(tabId, updateUrl = true) {
        const tabLink = document.querySelector(`a[href="#${tabId}"][data-bs-toggle="tab"]`);
        if (tabLink) {
            bootstrap.Tab.getOrCreateInstance(tabLink).show();
        }
        document.querySelectorAll('[data-profile-tab]').forEach(link => {
            link.classList.toggle('active', link.dataset.profileTab === tabId);
        });
        if (updateUrl && window.location.pathname === profilePath) {
            const newUrl = tabId === 'inbox'
                ? `${profilePath}#inbox`
                : profilePath;
            history.pushState(null, '', newUrl);
        }
    }
    if (window.location.pathname === profilePath) {
        showProfileTab(window.location.hash === '#inbox' ? 'inbox' : 'my-books', false);
    }
    document.querySelectorAll('[data-profile-tab]').forEach(link => {
        link.addEventListener('click', function (event) {
            const tabId = this.dataset.profileTab;
            if (window.location.pathname === profilePath) {
                event.preventDefault();
                showProfileTab(tabId);
            }
        });
    });
    document.querySelectorAll('#profileTabs a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const tabId = event.target.getAttribute('href').replace('#', '');
            showProfileTab(tabId);
        });
    });
});
</script>
@endpush
