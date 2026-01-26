@extends('layout.app')

@section('content')

<style>
    .form-card {
        max-width: 600px;
        margin: auto;
        background: #f9fafb;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .form-card h2 {
        margin-bottom: 20px;
        color: #111827;
        text-align: center;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #374151;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 15px;
        resize: vertical;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79,70,229,0.2);
    }

    .form-actions {
        text-align: center;
        margin-top: 25px;
    }

    .btn-primary {
        background: #4f46e5;
        color: #fff;
        padding: 12px 26px;
        border-radius: 10px;
        border: none;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.2s, transform 0.2s;
    }

    .btn-primary:hover {
        background: #4338ca;
        transform: translateY(-2px);
    }
</style>

<div class="form-card">
    <h2>➕ Add New Post</h2>

    <form method="POST" action="{{ route('posts.store') }}">
        @csrf

        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" placeholder="Enter post title" required>
        </div>

        <div class="form-group">
            <label>Content</label>
            <textarea name="content" rows="5" placeholder="Write post content..." required></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">💾 Save Post</button>
        </div>
    </form>
</div>

@endsection
