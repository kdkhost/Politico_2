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
use App\Support\DataTableRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            $filters = DataTableRequest::filters($request, [
                'title' => 'titulo',
                'titulo' => 'titulo',
                'category.name' => 'category_id',
                'category_name' => 'category_id',
                'author.name' => 'user_id',
                'author_name' => 'user_id',
                'visits_count' => 'views_count',
            ], [
                'status', 'category_id', 'tag_id', 'author_id',
                'date_from', 'date_to', 'formato',
            ]);

            $posts = $this->blogService->listPosts($filters);
            $total = $posts->total();
            $data = collect($posts->items())->map(function (Post $post): array {
                $editUrl = route('admin.blog.edit', $post->id);
                $deleteUrl = route('admin.blog.destroy', $post->id);
                $statusLabels = [
                    'draft' => ['Rascunho', 'secondary'],
                    'published' => ['Publicado', 'success'],
                    'scheduled' => ['Agendado', 'warning'],
                    'archived' => ['Arquivado', 'dark'],
                ];
                [$statusText, $statusClass] = $statusLabels[$post->status] ?? [$post->status ?: 'Indefinido', 'secondary'];

                return [
                    'id' => $post->id,
                    'title' => e($post->titulo),
                    'category_name' => e($post->category?->nome ?? 'Sem categoria'),
                    'tags' => $post->tags->isNotEmpty()
                        ? $post->tags->map(fn (Tag $tag): string => '<span class="badge bg-info me-1">' . e($tag->nome) . '</span>')->implode('')
                        : '<span class="text-muted">Sem tags</span>',
                    'status' => '<span class="badge bg-' . $statusClass . '">' . e($statusText) . '</span>',
                    'author_name' => e($post->author?->name ?? 'N/A'),
                    'published_at' => $post->published_at?->format('d/m/Y H:i') ?? '<span class="text-muted">-</span>',
                    'visits_count' => number_format((int) $post->views_count, 0, ',', '.'),
                    'action' => '<div class="btn-group btn-group-sm" role="group">'
                        . '<a href="' . $editUrl . '" class="btn btn-primary" title="Editar"><i class="fas fa-edit"></i></a>'
                        . '<button type="button" class="btn btn-danger btn-delete-post" data-id="' . $post->id . '" data-url="' . $deleteUrl . '" title="Excluir"><i class="fas fa-trash"></i></button>'
                        . '</div>',
                ];
            })->all();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $data,
                'draw' => (int) $request->draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao listar posts: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $post = new Post();
        $categories = Category::active()->orderBy('nome')->get();
        $tags = Tag::orderBy('nome')->get();
        $statuses = ['draft' => 'Rascunho', 'published' => 'Publicado', 'archived' => 'Arquivado', 'scheduled' => 'Agendado'];
        $postTags = [];

        return view('admin.blog.create', compact('post', 'categories', 'tags', 'statuses', 'postTags'));
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
                'success' => true,
                'message' => 'Post criado com sucesso.',
                'data' => $post,
                'redirect' => route('admin.blog.edit', $post->id),
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao criar post: ' . $e->getMessage()], 500);
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
                'success' => true,
                'message' => 'Post atualizado com sucesso.',
                'data' => $post,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Dados inválidos.', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao atualizar post: ' . $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $post = Post::with(['category', 'tags', 'author'])->findOrFail($id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.blog.show', compact('post'));
            }

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
                'success' => true,
                'message' => 'Post publicado com sucesso.',
                'data' => $post,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao publicar post.'], 500);
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
                'success' => true,
                'message' => 'Post arquivado com sucesso.',
                'data' => $post,
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao arquivar post.'], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->blogService->deletePost($id);

            return response()->json(['status' => 'success', 'success' => true, 'message' => 'Post excluído com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao excluir post.'], 500);
        }
    }
}
