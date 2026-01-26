@extends('layout.app')

@section('content')

<style>
    .search-box {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .search-box input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 15px;
    }

    .search-box button {
        background: #4f46e5;
        color: #fff;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 15px;
    }

    .search-box button:hover {
        background: #4338ca;
    }

    .post-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 18px;
        border-radius: 10px;
        margin-bottom: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .post-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .post-card h3 {
        margin: 0 0 8px;
        color: #111827;
    }

    .post-card p {
        margin: 0;
        color: #4b5563;
        line-height: 1.6;
    }

    .no-posts {
        text-align: center;
        color: #6b7280;
        padding: 30px;
    }
</style>

<h2>🔍 Search Posts</h2>

<form method="GET" class="search-box">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search title or content..."
    >
    <button type="submit">Search</button>
</form>

<hr>

<h2>📝 All Posts</h2>

@if($posts->count())
    @foreach($posts as $post)
        <div class="post-card">
            <h3>{{ $post->title }}</h3>
            <p>{{ $post->content }}</p>
        </div>
    @endforeach
@else
    <div class="no-posts">
        ❌ No posts found.
    </div>
@endif

@endsection
