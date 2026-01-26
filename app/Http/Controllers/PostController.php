<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index(Request $request)
    {
        if ($request->search) {
            $posts = Post::search($request->search)->get();
        } else {
            $posts = Post::latest()->get();
        }

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        Post::create($request->only('title', 'content'));
        return redirect('/')->with('success', 'Post Added');
    }
}
