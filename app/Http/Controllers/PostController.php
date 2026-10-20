<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\SearchAnalytics;
use App\Services\FuzzySearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PostController extends Controller
{
    protected FuzzySearchService $fuzzyService;

    public function __construct(FuzzySearchService $fuzzyService)
    {
        $this->fuzzyService = $fuzzyService;
    }

    /**
     * Display Posts with Full-Text Scout Search, Highlighting, and Typo Tolerance.
     */
    public function index(Request $request)
    {
        $startTime = microtime(true);
        $search = trim($request->search ?? '');
        $category = trim($request->category ?? '');
        $sort = $request->sort ?? 'latest';

        // Available categories for faceted filter pills
        $categories = Post::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values()
            ->all();
        if (empty($categories)) {
            $categories = ['Laravel', 'PHP', 'JavaScript', 'DevOps', 'Database', 'General'];
        }

        // Did You Mean suggestion placeholder
        $didYouMean = null;

        if (!empty($search)) {
            // Laravel Scout Search
            $scoutQuery = Post::search($search);

            if (!empty($category) && $category !== 'all') {
                $scoutQuery->where('category', $category);
            }

            // Apply ordering on underlying query builder
            $posts = $scoutQuery->query(function ($builder) use ($sort, $category) {
                if (!empty($category) && $category !== 'all') {
                    $builder->where('category', $category);
                }

                switch ($sort) {
                    case 'oldest':
                        $builder->oldest();
                        break;
                    case 'az':
                        $builder->orderBy('title');
                        break;
                    case 'za':
                        $builder->orderByDesc('title');
                        break;
                    default:
                        $builder->latest();
                        break;
                }
            })->paginate(6)->withQueryString();

            // Record Search Analytics with results count
            $analytics = SearchAnalytics::firstOrCreate(
                ['keyword' => strtolower(trim($search))],
                ['search_count' => 0, 'results_count' => 0]
            );
            $analytics->increment('search_count');
            $analytics->update([
                'results_count' => $posts->total(),
                'last_searched_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            // Feature 4: "Did you mean?" Typo Tolerance
            if ($posts->total() === 0) {
                $didYouMean = $this->fuzzyService->suggestCorrection($search);
            }

            // Feature 2: Keyword Contextual Snippet & Highlighting
            $posts->getCollection()->transform(function ($post) use ($search) {
                $post->highlight_title = $this->fuzzyService->highlight($post->title, $search);
                $snippet = $post->getHighlightedSnippet($search);
                $post->highlight_content = $this->fuzzyService->highlight($snippet, $search);
                return $post;
            });

        } else {
            $query = Post::query();

            if (!empty($category) && $category !== 'all') {
                $query->where('category', $category);
            }

            switch ($sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'az':
                    $query->orderBy('title');
                    break;
                case 'za':
                    $query->orderByDesc('title');
                    break;
                default:
                    $query->latest();
                    break;
            }

            $posts = $query->paginate(6)->withQueryString();

            $posts->getCollection()->transform(function ($post) {
                $post->highlight_title = e($post->title);
                $post->highlight_content = e(\Illuminate\Support\Str::words($post->content, 25));
                return $post;
            });
        }

        // Metrics
        $totalPosts = Post::count();
        $todayPosts = Post::whereDate('created_at', Carbon::today())->count();
        $weekPosts = Post::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $monthPosts = Post::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();

        $recentPosts = Post::latest()->take(5)->get();
        $trendingSearches = SearchAnalytics::orderByDesc('search_count')->orderByDesc('last_searched_at')->take(8)->get();
        $searchTime = number_format(microtime(true) - $startTime, 4);

        return view('posts.index', compact(
            'posts',
            'search',
            'category',
            'categories',
            'sort',
            'totalPosts',
            'todayPosts',
            'weekPosts',
            'monthPosts',
            'recentPosts',
            'trendingSearches',
            'searchTime',
            'didYouMean'
        ));
    }

    /**
     * Feature 1: Instant Live Search & Typeahead Autocomplete (AJAX endpoint).
     */
    public function liveSearch(Request $request)
    {
        $startTime = microtime(true);
        $query = trim($request->input('query', ''));

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'results' => [],
                'suggestions' => [],
                'did_you_mean' => null,
                'count' => 0,
                'duration_ms' => 0,
            ]);
        }

        // Scout Search
        $posts = Post::search($query)->take(5)->get();

        $results = $posts->map(function ($post) use ($query) {
            return [
                'id' => $post->id,
                'title' => $this->fuzzyService->highlight($post->title, $query),
                'raw_title' => $post->title,
                'category' => $post->category ?? 'General',
                'snippet' => $this->fuzzyService->highlight($post->getHighlightedSnippet($query, 10), $query),
                'created_at' => $post->created_at->format('M d, Y'),
            ];
        });

        // Autocomplete suggestions
        $suggestions = SearchAnalytics::where('keyword', 'like', '%' . $query . '%')
            ->orderByDesc('search_count')
            ->take(4)
            ->pluck('keyword')
            ->all();

        // Typo tolerance
        $didYouMean = null;
        if ($posts->isEmpty()) {
            $didYouMean = $this->fuzzyService->suggestCorrection($query);
        }

        $durationMs = round((microtime(true) - $startTime) * 1000, 1);

        return response()->json([
            'success' => true,
            'query' => $query,
            'results' => $results,
            'suggestions' => $suggestions,
            'did_you_mean' => $didYouMean,
            'count' => $posts->count(),
            'duration_ms' => $durationMs,
        ]);
    }

    /**
     * Feature 3: Global Federated Multi-Model Search (Ctrl+K Modal AJAX endpoint).
     */
    public function federatedSearch(Request $request)
    {
        $startTime = microtime(true);
        $query = trim($request->input('query', ''));

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'total' => 0,
                'posts' => [],
                'users' => [],
                'categories' => [],
                'duration_ms' => 0,
            ]);
        }

        // 1. Search Posts
        $posts = Post::search($query)->take(5)->get()->map(function ($p) use ($query) {
            return [
                'id' => $p->id,
                'type' => 'post',
                'title' => $this->fuzzyService->highlight($p->title, $query),
                'subtitle' => $p->category ?? 'General',
                'description' => $this->fuzzyService->highlight($p->getHighlightedSnippet($query, 8), $query),
            ];
        });

        // 2. Search Users
        $users = User::search($query)->take(4)->get()->map(function ($u) use ($query) {
            return [
                'id' => $u->id,
                'type' => 'user',
                'title' => $this->fuzzyService->highlight($u->name, $query),
                'subtitle' => 'Author / User',
                'description' => $this->fuzzyService->highlight($u->email, $query),
            ];
        });

        // 3. Match Categories
        $categories = Post::select('category')
            ->where('category', 'like', '%' . $query . '%')
            ->distinct()
            ->take(3)
            ->pluck('category')
            ->map(function ($cat) use ($query) {
                return [
                    'title' => $this->fuzzyService->highlight($cat, $query),
                    'type' => 'category',
                    'raw_category' => $cat,
                ];
            });

        $totalCount = $posts->count() + $users->count() + $categories->count();
        $durationMs = round((microtime(true) - $startTime) * 1000, 1);

        return response()->json([
            'success' => true,
            'total' => $totalCount,
            'posts' => $posts,
            'users' => $users,
            'categories' => $categories,
            'duration_ms' => $durationMs,
        ]);
    }

    /**
     * Create Page.
     */
    public function create()
    {
        $categories = ['Laravel', 'PHP', 'JavaScript', 'DevOps', 'Database', 'General'];
        return view('posts.create', compact('categories'));
    }

    /**
     * Store Post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'category' => 'required|string|max:60',
            'content' => 'required',
            'tags' => 'nullable|string|max:255',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'category' => $request->category,
            'content' => $request->content,
            'tags' => $request->tags,
        ]);

        return redirect('/')->with('success', "Post '{$post->title}' created and indexed in Scout search successfully.");
    }

    /**
     * Export Posts to CSV.
     */
    public function export()
    {
        $fileName = 'posts_' . now()->format('Ymd_His') . '.csv';
        $posts = Post::latest()->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($posts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Category', 'Tags', 'Content', 'Created At']);

            foreach ($posts as $post) {
                fputcsv($file, [
                    $post->id,
                    $post->title,
                    $post->category ?? 'General',
                    $post->tags ?? '',
                    strip_tags($post->content),
                    $post->created_at,
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
