@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')

<div class="page-header d-flex align-items-center">
    <h2 class="mb-0"><i class="bi bi-folder2-open me-2"></i>Manage Categories</h2>
    <span class="badge bg-secondary ms-3 fs-6">{{ $categories->count() }} total</span>
</div>

{{-- Validation errors (for add/edit forms) --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-1"></i>
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Add category form --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header fw-semibold bg-light">
        <i class="bi bi-plus-circle me-1"></i>Add New Category
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label fw-semibold" for="name">Name <span class="text-danger">*</span></label>
                <input class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" type="text"
                       placeholder="e.g. Horror" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="description">Description <span class="text-danger">*</span></label>
                <input class="form-control @error('description') is-invalid @enderror"
                       id="description" name="description" type="text"
                       placeholder="Brief description…" value="{{ old('description') }}" required>
            </div>
            <div class="col-md-2">
                <button class="btn btn-be w-100" type="submit">
                    <i class="bi bi-plus-lg me-1"></i>Add
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Categories table --}}
<div class="card shadow-sm border-0">
    <div class="card-header fw-semibold bg-light">
        <i class="bi bi-list-ul me-1"></i>Existing Categories
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th class="text-center">Books</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            @forelse($categories as $category)
            <tbody>
                <tr>
                    <td class="fw-semibold">{{ $category->name }}</td>
                    <td><code class="text-muted">{{ $category->slug }}</code></td>
                    <td><small class="text-muted">{{ $category->description }}</small></td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $category->books_count }}</span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary me-1"
                                data-bs-toggle="collapse"
                                data-bs-target="#edit-{{ $category->id }}"
                                title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"
                                    type="submit"
                                    title="{{ $category->books_count > 0 ? 'Cannot delete — has books' : 'Delete' }}"
                                    {{ $category->books_count > 0 ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <tr class="collapse bg-light" id="edit-{{ $category->id }}">
                    <td colspan="5" class="py-3 px-4">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}"
                              class="row g-2 align-items-end">
                            @csrf
                            @method('PUT')
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Name</label>
                                <input class="form-control form-control-sm" name="name" type="text"
                                       value="{{ $category->name }}" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Description</label>
                                <input class="form-control form-control-sm" name="description" type="text"
                                       value="{{ $category->description }}" required maxlength="255">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button class="btn btn-sm btn-be" type="submit">Save</button>
                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#edit-{{ $category->id }}">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            </tbody>
            @empty
            <tbody>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No categories yet.</td>
                </tr>
            </tbody>
            @endforelse
        </table>
    </div>
</div>

@endsection
