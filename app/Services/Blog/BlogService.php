<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

namespace App\Services\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BlogService
{
    public function listPosts(array $filters = []): LengthAwarePaginator
    {
        $query = Post::with(['author:id,name', 'category:id,nome,slug', 'tags:id,nome,slug']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['category_slug'])) {
            $query->whereHas('category', fn($q) => $q->where('slug', $filters['category_slug']));
        }

        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', fn($q) => $q->where('tags.id', $filters['tag_id']));
        }

        if (!empty($filters['author_id'])) {
            $query->where('user_id', $filters['author_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('conteudo', 'like', "%{$search}%")
                    ->orWhere('resumo', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('published_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('published_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['formato'])) {
            $query->where('formato', $filters['formato']);
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortField, $sortOrder);

        $perPage = (int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15));

        return $query->paginate($perPage);
    }

    public function findPostBySlug(string $slug): Post
    {
        return Post::with(['author:id,name', 'category:id,nome,slug', 'tags:id,nome,slug'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function createPost(array $data): Post
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['titulo']);
        }

        if (!isset($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        if (isset($data['conteudo'])) {
            $data['tempo_leitura'] = $this->calculateReadingTime($data['conteudo']);
        }

        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $post = Post::create($data);

        if (!empty($tags)) {
            $tagIds = $this->syncTags($tags);
            $post->tags()->sync($tagIds);
        }

        return $post->load(['author:id,name', 'category:id,nome,slug', 'tags:id,nome,slug']);
    }

    public function updatePost(int $id, array $data): Post
    {
        $post = Post::findOrFail($id);

        if (isset($data['titulo']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['titulo']);
        }

        if (isset($data['conteudo'])) {
            $data['tempo_leitura'] = $this->calculateReadingTime($data['conteudo']);
        }

        $tags = $data['tags'] ?? null;
        unset($data['tags']);

        $post->update($data);

        if ($tags !== null) {
            $tagIds = $this->syncTags($tags);
            $post->tags()->sync($tagIds);
        }

        return $post->load(['author:id,name', 'category:id,nome,slug', 'tags:id,nome,slug']);
    }

    public function deletePost(int $id): bool
    {
        return (bool) Post::findOrFail($id)->delete();
    }

    public function publishPost(int $id): Post
    {
        $post = Post::findOrFail($id);

        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);

        return $post->fresh();
    }

    public function archivePost(int $id): Post
    {
        $post = Post::findOrFail($id);

        $post->update(['status' => 'archived']);

        return $post->fresh();
    }

    public function getRelatedPosts(int $postId, int $limit = 3): array
    {
        $post = Post::with('tags', 'category')->findOrFail($postId);

        $tagIds = $post->tags->pluck('id')->toArray();

        $related = Post::with(['author:id,name', 'tags:id,nome,slug'])
            ->where('id', '!=', $postId)
            ->where('status', 'published')
            ->where(function ($query) use ($tagIds, $post) {
                if (!empty($tagIds)) {
                    $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagIds));
                }

                if ($post->category_id) {
                    $query->orWhere('category_id', $post->category_id);
                }
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get()
            ->toArray();

        return $related;
    }

    public function getPopularPosts(int $limit = 5): array
    {
        return Post::with(['author:id,name', 'category:id,nome,slug'])
            ->where('status', 'published')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function incrementViews(int $postId): void
    {
        Post::where('id', $postId)->increment('views_count');
    }

    public function searchPosts(string $query): LengthAwarePaginator
    {
        return Post::with(['author:id,name', 'category:id,nome,slug'])
            ->where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('titulo', 'like', "%{$query}%")
                    ->orWhere('conteudo', 'like', "%{$query}%")
                    ->orWhere('resumo', 'like', "%{$query}%");
            })
            ->orderByDesc('published_at')
            ->paginate(config('sistema.pagination_per_page', 15));
    }

    protected function calculateReadingTime(string $content): int
    {
        $wordsPerMinute = 200;
        $words = str_word_count(strip_tags($content));

        return max(1, (int) ceil($words / $wordsPerMinute));
    }

    protected function syncTags(array $tags): array
    {
        $tagIds = [];

        foreach ($tags as $tag) {
            if (is_numeric($tag)) {
                $tagIds[] = (int) $tag;
            } elseif (is_string($tag)) {
                $existing = Tag::firstOrCreate(
                    ['slug' => Str::slug($tag)],
                    ['nome' => $tag]
                );
                $tagIds[] = $existing->id;
            }
        }

        return $tagIds;
    }
}
