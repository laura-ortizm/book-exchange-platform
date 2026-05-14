@extends('layouts.app')

@section('title', 'Publish a Book')

@section('content')

<div class="page-header">
    <h2 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Publish a Book</h2>
</div>

<div class="row justify-content-center">
<div class="col-md-8 col-lg-7">

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">

                <div class="col-md-8">
                    <label class="form-label fw-semibold" for="title">Title <span class="text-danger">*</span></label>
                    <input class="form-control" id="title" name="title" type="text"
                           placeholder="e.g. The Great Gatsby" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="isbn">ISBN</label>
                    <input class="form-control" id="isbn" name="isbn" type="text"
                           placeholder="e.g. 9780743273565">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="author">Author <span class="text-danger">*</span></label>
                    <input class="form-control" id="author" name="author" type="text"
                           placeholder="e.g. F. Scott Fitzgerald" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="category_id">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select a category…</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="condition">Condition <span class="text-danger">*</span></label>
                    <select class="form-select" id="condition" name="condition" required>
                        <option value="">Select condition…</option>
                        <option value="new">New</option>
                        <option value="good">Good</option>
                        <option value="fair">Fair</option>
                        <option value="poor">Poor</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="description">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description"
                              rows="4" placeholder="Brief description of the book…" required></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="cover_image">Cover Image</label>
                    <input class="form-control" id="cover_image" name="cover_image"
                           type="file" accept="image/*">
                    <div class="form-text">JPG or PNG, max 2 MB.</div>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-be" type="submit">
                        <i class="bi bi-cloud-upload me-1"></i>Publish Book
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('catalog.index') }}">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

</div>
</div>

@endsection
