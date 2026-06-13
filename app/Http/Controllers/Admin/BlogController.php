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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\Blog\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        protected BlogService $blogService,
    ) {}

    public function index()
    {
        return view('admin.blog.index');
    }

    public function list(Request $request)
    {
        try {
            $filters = $request->only([
                'status', 'category_id', 'tag_id', 'author_id',
                'search', 'date_from', 'date_to', 'formato',
                'sort_by', 'sort_order', 'per_page',
            ]);

            $posts = $this->blogService->listPosts($filters);
            $total = $posts->total();

            return response()->json([
                'status' => 'success',
                'data' => $posts->items(),
                'draw' => (int) $request->draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar posts: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $categories = Category::active()->orderBy('nome')->get();
        $tags = Tag::orderBy('nome')->get();
        $statuses = ['draft' => 'Rascunho', 'published' => 'Publicado', 'archived' => 'Arquivado', 'scheduled' => 'Agendado'];

        return view('admin.blog.create', compact('categories', 'tags', 'statuses'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:posts,slug',
                'resumo' => 'nullable|string|max:500',
                'conteudo' => 'nullable|string',
                'imagem_destaque' => 'nullable|string|max:500',
                'category_id' => 'nullable|exists:categories,id',
                'tags' => 'nullable|array',
                'tags.*' => 'nullable|string',
                'status' => 'required|in:draft,published,archived,scheduled',
                'published_at' => 'nullable|date',
                'scheduled_for' => 'nullable|date',
                'formato' => 'nullable|string|in:artigo,noticia,video,galeria',
                'seo_title' => 'nullable|string|max:255',
                'seo_description' => 'nullable|string|max:500',
                'seo_keywords' => 'nullable|string|max:500',
                'seo_og_image' => 'nullable|string|max:500',
            ]);

            $post = $this->blogService->createPost($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Post criado com sucesso.',
                'data' => $post,
                'redirect' => route('admin.blog.edit', $post->id),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar post: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $post = Post::with(['author:id,name', 'category:id,nome,slug', 'tags:id,nome,slug'])->findOrFail($id);
        $categories = Category::active()->orderBy('nome')->get();
        $tags = Tag::orderBy('nome')->get();
        $statuses = ['draft' => 'Rascunho', 'published' => 'Publicado', 'archived' => 'Arquivado', 'scheduled' => 'Agendado'];
        $postTags = $post->tags->pluck('id')->toArray();

        return view('admin.blog.edit', compact('post', 'categories', 'tags', 'statuses', 'postTags'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:posts,slug,' . $id,
                'resumo' => 'nullable|string|max:500',
                'conteudo' => 'nullable|string',
                'imagem_destaque' => 'nullable|string|max:500',
                'category_id' => 'nullable|exists:categories,id',
                'tags' => 'nullable|array',
                'tags.*' => 'nullable|string',
                'status' => 'required|in:draft,published,archived,scheduled',
                'published_at' => 'nullable|date',
                'scheduled_for' => 'nullable|date',
                'formato' => 'nullable|string|in:artigo,noticia,video,galeria',
                'seo_title' => 'nullable|string|max:255',
                'seo_description' => 'nullable|string|max:500',
                'seo_keywords' => 'nullable|string|max:500',
                'seo_og_image' => 'nullable|string|max:500',
            ]);

            $post = $this->blogService->updatePost($id, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Post atualizado com sucesso.',
                'data' => $post,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar post: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $post = Post::with(['category', 'tags', 'author'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $post,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar post.'], 500);
        }
    }

    public function publish(int $id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->status = 'published';
            if ($post->getTable() === 'posts' && in_array('published_at', $post->getFillable())) {
                $post->published_at = now();
            }
            $post->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Post publicado com sucesso.',
                'data' => $post,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao publicar post.'], 500);
        }
    }

    public function archive(int $id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->status = 'archived';
            $post->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Post arquivado com sucesso.',
                'data' => $post,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao arquivar post.'], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->blogService->deletePost($id);

            return response()->json(['status' => 'success', 'message' => 'Post excluído com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir post.'], 500);
        }
    }
}
