<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostController extends Controller
{
    /**
     * Display Posts
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->sort ?? 'latest';

        // Statistics
        $totalPosts = Post::count();

        $todayPosts = Post::whereDate('created_at', Carbon::today())->count();

        $weekPosts = Post::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        $monthPosts = Post::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Recent Posts
        $recentPosts = Post::latest()->take(5)->get();

        /*
        |--------------------------------------------------------------------------
        | Search + Filter
        |--------------------------------------------------------------------------
        */

        if ($search) {

            // Laravel Scout Search
            $query = Post::search($search);

            if ($sort == 'oldest') {

                $posts = $query
                    ->query(function ($builder) {
                        $builder->oldest();
                    })
                    ->paginate(5)
                    ->withQueryString();
            } elseif ($sort == 'az') {

                $posts = $query
                    ->query(function ($builder) {
                        $builder->orderBy('title');
                    })
                    ->paginate(5)
                    ->withQueryString();
            } elseif ($sort == 'za') {

                $posts = $query
                    ->query(function ($builder) {
                        $builder->orderByDesc('title');
                    })
                    ->paginate(5)
                    ->withQueryString();
            } else {

                $posts = $query
                    ->query(function ($builder) {
                        $builder->latest();
                    })
                    ->paginate(5)
                    ->withQueryString();
            }
        } else {

            $posts = Post::query();

            switch ($sort) {

                case 'oldest':
                    $posts->oldest();
                    break;

                case 'az':
                    $posts->orderBy('title');
                    break;

                case 'za':
                    $posts->orderByDesc('title');
                    break;

                default:
                    $posts->latest();
                    break;
            }

            $posts = $posts->paginate(5)->withQueryString();
        }

        return view('posts.index', compact(
            'posts',
            'search',
            'sort',
            'totalPosts',
            'todayPosts',
            'weekPosts',
            'monthPosts',
            'recentPosts'
        ));
    }

    /**
     * Create Page
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store Post
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect('/')
            ->with('success', 'Post added successfully.');
    }

    /**
     * Export Posts to CSV
     */
    public function export()
    {
        $fileName = 'posts.csv';

        $posts = Post::latest()->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($posts) {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Title',
                'Content',
                'Created At'
            ]);

            foreach ($posts as $post) {

                fputcsv($file, [
                    $post->id,
                    $post->title,
                    strip_tags($post->content),
                    $post->created_at,
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
