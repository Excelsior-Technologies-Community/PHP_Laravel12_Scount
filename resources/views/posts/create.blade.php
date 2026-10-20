@extends('layout.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-plus-fill text-primary me-2"></i>Create New Searchable Post</h5>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>Back to Search
                </a>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('posts.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Post Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg rounded-3 shadow-none border" placeholder="e.g. Master Laravel Scout Search & Typo Tolerance" value="{{ old('title') }}" required>
                        <small class="text-muted">Indexed automatically for Scout full-text queries and typeahead autocomplete.</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select rounded-3 shadow-none border" required>
                                <option value="" disabled selected>-- Select Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tags (comma-separated)</label>
                            <input type="text" name="tags" class="form-control rounded-3 shadow-none border" placeholder="e.g. laravel, scout, database, php" value="{{ old('tags') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Post Content <span class="text-danger">*</span></label>
                        <textarea name="content" rows="6" class="form-control rounded-3 shadow-none border" placeholder="Write comprehensive post content for contextual snippet highlighting..." required>{{ old('content') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <a href="{{ url('/') }}" class="text-muted text-decoration-none">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Save & Auto-Index
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
