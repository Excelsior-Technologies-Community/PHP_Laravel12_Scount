<?php

namespace App\Services;

use App\Models\Post;
use App\Models\SearchAnalytics;
use Illuminate\Support\Facades\Cache;

class FuzzySearchService
{
    /**
     * Get or build a dictionary vocabulary from existing content and historical queries.
     */
    public function getVocabulary(): array
    {
        return Cache::remember('search_dictionary_vocabulary', now()->addHours(6), function () {
            $words = [];

            // 1. From Search Analytics historical terms
            $analyticsWords = SearchAnalytics::where('results_count', '>', 0)
                ->pluck('keyword')
                ->toArray();
            foreach ($analyticsWords as $kw) {
                foreach (explode(' ', strtolower($kw)) as $w) {
                    $cleaned = trim(preg_replace('/[^a-z0-9]/i', '', $w));
                    if (strlen($cleaned) >= 3) {
                        $words[$cleaned] = true;
                    }
                }
            }

            // 2. From Post titles and categories
            $posts = Post::select('title', 'category', 'tags')->take(200)->get();
            foreach ($posts as $post) {
                $content = strtolower($post->title . ' ' . $post->category . ' ' . $post->tags);
                $tokens = preg_split('/[\s,\-\._\/]+/', $content);
                foreach ($tokens as $token) {
                    $cleaned = trim(preg_replace('/[^a-z0-9]/i', '', $token));
                    if (strlen($cleaned) >= 3) {
                        $words[$cleaned] = true;
                    }
                }
            }

            // Common default technology keywords if database is new
            $seedWords = [
                'laravel', 'scout', 'search', 'mysql', 'database', 'sqlite', 'redis',
                'docker', 'python', 'javascript', 'framework', 'controller', 'artisan',
                'eloquent', 'tailwind', 'bootstrap', 'vue', 'react', 'api', 'analytics',
                'performance', 'queue', 'horizon', 'security', 'authentication', 'developer'
            ];
            foreach ($seedWords as $sw) {
                $words[$sw] = true;
            }

            return array_keys($words);
        });
    }

    /**
     * Find a "Did you mean?" suggestion for a given query if it's misspelled.
     */
    public function suggestCorrection(string $query): ?string
    {
        $query = strtolower(trim($query));
        if (strlen($query) < 3) {
            return null;
        }

        $vocabulary = $this->getVocabulary();

        // Exact match already exists
        if (in_array($query, $vocabulary)) {
            return null;
        }

        $words = explode(' ', $query);
        $correctedWords = [];
        $hasCorrection = false;

        foreach ($words as $word) {
            if (strlen($word) < 3 || in_array($word, $vocabulary)) {
                $correctedWords[] = $word;
                continue;
            }

            $bestMatch = null;
            $bestDistance = 999;
            $wordMetaphone = metaphone($word);

            foreach ($vocabulary as $candidate) {
                // Check Levenshtein distance
                $dist = levenshtein($word, $candidate);

                // Allow 1 edit for short words, 2 for longer words
                $maxAllowed = strlen($word) <= 5 ? 1 : 2;

                if ($dist <= $maxAllowed && $dist < $bestDistance) {
                    $bestDistance = $dist;
                    $bestMatch = $candidate;
                } elseif ($dist <= $maxAllowed + 1 && metaphone($candidate) === $wordMetaphone) {
                    // Phonetic similarity match
                    $bestDistance = $dist;
                    $bestMatch = $candidate;
                }
            }

            if ($bestMatch && $bestMatch !== $word) {
                $correctedWords[] = $bestMatch;
                $hasCorrection = true;
            } else {
                $correctedWords[] = $word;
            }
        }

        return $hasCorrection ? implode(' ', $correctedWords) : null;
    }

    /**
     * Highlight matched keywords in text with HTML markup.
     */
    public function highlight(string $text, ?string $keyword): string
    {
        if (empty(trim($keyword ?? ''))) {
            return e($text);
        }

        $words = array_filter(explode(' ', trim($keyword)));
        $escapedText = e($text);

        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) continue;

            $pattern = '/(' . preg_quote($word, '/') . ')/i';
            $escapedText = preg_replace(
                $pattern,
                '<mark class="bg-warning text-dark px-1 py-0.5 rounded fw-bold shadow-sm">$1</mark>',
                $escapedText
            );
        }

        return $escapedText;
    }
}
