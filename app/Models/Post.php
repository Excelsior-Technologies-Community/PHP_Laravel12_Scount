<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;

    protected $fillable = [
        'title',
        'category',
        'content',
        'tags',
    ];

    /**
     * Get the indexable data array for Laravel Scout.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'content' => $this->content,
            'tags' => $this->tags,
        ];
    }

    /**
     * Helper to generate a contextual snippet around a searched keyword.
     */
    public function getHighlightedSnippet(string $keyword, int $wordPadding = 15): string
    {
        $text = strip_tags($this->content);
        if (empty(trim($keyword))) {
            return \Illuminate\Support\Str::words($text, 30);
        }

        $pos = mb_stripos($text, $keyword);
        if ($pos === false) {
            return \Illuminate\Support\Str::words($text, 30);
        }

        // Extract surrounding window
        $start = max(0, $pos - 100);
        $length = mb_strlen($keyword) + 200;
        $snippet = mb_substr($text, $start, $length);

        if ($start > 0) {
            $snippet = '...' . $snippet;
        }
        if (($start + $length) < mb_strlen($text)) {
            $snippet .= '...';
        }

        return $snippet;
    }
}
