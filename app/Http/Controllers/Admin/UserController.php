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
use App\Models\Permissions\Profile;
use App\Models\User;
use App\Services\Upload\UploadService;
use App\Support\DataTableRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private const SORTABLE_FIELDS = [
        'id',
        'name',
        'email',
        'status',
        'created_at',
        'updated_at',
        'ultimo_acesso',
    ];

    public function index()
    {
        $profiles = Profile::orderBy('nome')->get(['id', 'nome']);

        return view('admin.usuarios.index', compact('profiles'));
    }

    public function show(int $id)
    {
        try {
            $user = User::with('profile')->findOrFail($id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.usuarios.show', compact('user'));
            }

            return response()->json($this->formatUserForJson($user));
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar usuário.'], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $filters = DataTableRequest::filters($request, [
                'profile.name' => 'profile_id',
                'profile_name' => 'profile_id',
                'last_login' => 'ultimo_acesso',
                'last_login_at' => 'ultimo_acesso',
            ], ['profile_id', 'status']);

            $query = User::with('profile:id,nome');

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if (!empty($filters['profile_id'])) {
                $query->where('profile_id', $filters['profile_id']);
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            $sortField = in_array((string) ($filters['sort_by'] ?? ''), self::SORTABLE_FIELDS, true)
                ? (string) $filters['sort_by']
                : 'created_at';
            $sortOrder = strtolower((string) ($filters['sort_order'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);

            $users = $query->paginate(
                min(max((int) ($filters['per_page'] ?? config('sistema.pagination_per_page', 15)), 1), 100),
                ['*'],
                'page',
                max((int) ($filters['page'] ?? 1), 1)
            );
            $total = $users->total();
            $data = collect($users->items())->map(fn (User $user): array => $this->formatUserRow($user))->all();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'data' => $data,
                'draw' => (int) $request->draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao listar usuários: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $profiles = Profile::orderBy('nome')->pluck('nome', 'id');

        return view('admin.usuarios.create', compact('profiles'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'profile_id' => 'nullable|exists:profiles,id',
                'telefone' => 'nullable|string|max:20',
                'cargo' => 'nullable|string|max:255',
                'is_super_admin' => 'boolean',
                'status' => 'nullable|in:active,inactive',
                'active' => 'nullable|boolean',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
            ]);

            $validated['status'] = $request->input('status') ?: ($request->boolean('active') ? 'active' : 'inactive');
            $validated['is_blocked'] = $validated['status'] !== 'active';
            $validated['password'] = Hash::make($validated['password']);
            unset($validated['active'], $validated['avatar']);

            $user = User::create($validated);

            if ($request->hasFile('avatar')) {
                $user->update(['avatar' => $this->storeAvatar($request->file('avatar'))]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Usuário criado com sucesso.',
                'data' => $user,
                'redirect' => route('admin.users.edit', $user->id),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao criar usuário: ' . $e->getMessage()], 500);
        }
    }

    public function edit(int $id)
    {
        $user = User::with('profile')->findOrFail($id);
        $profiles = Profile::orderBy('nome')->pluck('nome', 'id');

        return view('admin.usuarios.edit', compact('user', 'profiles'));
    }

    public function update(Request $request, int $id)
    {
        try {
            $user = User::findOrFail($id);
            $previousAvatar = $user->avatar;

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'profile_id' => 'nullable|exists:profiles,id',
                'telefone' => 'nullable|string|max:20',
                'cargo' => 'nullable|string|max:255',
                'is_super_admin' => 'boolean',
                'status' => 'nullable|in:active,inactive',
                'active' => 'nullable|boolean',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
            ];

            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8|confirmed';
            }

            $validated = $request->validate($rules);

            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['status'] = $request->input('status') ?: ($request->boolean('active') ? 'active' : 'inactive');
            $validated['is_blocked'] = $validated['status'] !== 'active';
            unset($validated['active'], $validated['avatar']);

            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $this->storeAvatar($request->file('avatar'));
            }

            $user->update($validated);
            $this->deleteReplacedAvatar($previousAvatar, $user->fresh()->avatar);

            return response()->json([
                'status' => 'success',
                'message' => 'Usuário atualizado com sucesso.',
                'data' => $user->fresh()->load('profile'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            if ($id === Auth::id()) {
                return response()->json(['status' => 'error', 'message' => 'Você não pode excluir seu próprio usuário.'], 400);
            }

            $user = User::findOrFail($id);
            $this->deleteUserAvatar($user->avatar);
            $user->delete();

            return response()->json(['status' => 'success', 'message' => 'Usuário excluído com sucesso.', 'reload' => true]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao excluir usuário.'], 500);
        }
    }

    public function block(int $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['is_blocked' => true, 'status' => 'inactive']);

            return response()->json(['status' => 'success', 'message' => 'Usuário bloqueado com sucesso.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao bloquear usuário.'], 500);
        }
    }

    public function unblock(int $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['is_blocked' => false, 'status' => 'active']);

            return response()->json(['status' => 'success', 'message' => 'Usuário desbloqueado com sucesso.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao desbloquear usuário.'], 500);
        }
    }

    public function toggleStatus(int $id)
    {
        try {
            $user = User::findOrFail($id);
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';

            $user->update([
                'status' => $newStatus,
                'is_blocked' => $newStatus !== 'active',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $newStatus === 'active' ? 'Usuário ativado com sucesso.' : 'Usuário desativado com sucesso.',
                'reload' => true,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao alternar status do usuário.'], 500);
        }
    }

    public function loginAs(int $id)
    {
        try {
            if (!Auth::user()?->is_super_admin) {
                return response()->json(['status' => 'error', 'message' => 'Apenas super administradores podem impersonar usuários.'], 403);
            }

            $user = User::findOrFail($id);

            if ($user->id === Auth::id()) {
                return response()->json(['status' => 'error', 'message' => 'Você já está autenticado como este usuário.'], 422);
            }

            session()->put('impersonated_by', Auth::id());
            Auth::login($user);

            Log::info('Impersonação iniciada.', [
                'admin_id' => session('impersonated_by'),
                'target_user_id' => $user->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Você agora está logado como {$user->name}.",
                'redirect' => route('admin.dashboard'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao impersonar usuário.'], 500);
        }
    }

    public function stopImpersonation()
    {
        try {
            $originalUserId = session('impersonated_by');

            if (!$originalUserId) {
                return redirect()->route('admin.dashboard');
            }

            $currentUserId = Auth::id();
            $originalUser = User::findOrFail((int) $originalUserId);
            session()->forget('impersonated_by');
            Auth::login($originalUser);

            Log::info('Impersonação encerrada.', [
                'admin_id' => $originalUser->id,
                'impersonated_user_id' => $currentUserId,
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Impersonação encerrada com sucesso.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Erro ao encerrar impersonação.');
        }
    }

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:' . config('sistema.upload_max_size', 10) * 1024,
            ]);

            $user = Auth::user();

            if (!$user instanceof User) {
                return response()->json(['status' => 'error', 'message' => 'Usuario nao autenticado.'], 401);
            }

            $previousAvatar = $user->avatar;
            $user->update([
                'avatar' => $this->storeAvatar($request->file('avatar')),
            ]);
            $this->deleteReplacedAvatar($previousAvatar, $user->fresh()->avatar);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Foto do perfil atualizada.',
                'data' => [
                    'avatar_url' => $user->fresh()->avatar_url,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Erro ao atualizar foto do perfil: ' . $e->getMessage()], 500);
        }
    }

    private function formatUserForJson(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_id' => $user->profile_id,
            'profile' => [
                'id' => $user->profile?->id,
                'name' => $user->profile?->nome,
            ],
            'status' => $user->status,
            'active' => $user->status === 'active' && !$user->is_blocked,
            'is_blocked' => (bool) $user->is_blocked,
            'avatar_url' => $user->avatar_url,
            'created_at' => $user->created_at,
            'last_login_at' => $user->ultimo_acesso,
        ];
    }

    private function formatUserRow(User $user): array
    {
        $isActive = $user->status === 'active' && !$user->is_blocked;
        $statusClass = $isActive ? 'success' : 'danger';
        $statusText = $isActive ? 'Ativo' : 'Inativo';

        return [
            'id' => $user->id,
            'name' => e($user->name),
            'email' => e($user->email),
            'profile_name' => e($user->profile?->nome ?? 'Sem perfil'),
            'status' => '<span class="badge bg-' . $statusClass . '">' . $statusText . '</span>',
            'last_login' => $user->ultimo_acesso?->format('d/m/Y H:i') ?? '<span class="text-muted">Nunca</span>',
            'action' => '<div class="btn-group btn-group-sm" role="group">'
                . '<button type="button" class="btn btn-info btn-view-user" data-id="' . $user->id . '" title="Ver"><i class="fas fa-eye"></i></button>'
                . '<button type="button" class="btn btn-primary btn-edit-user" data-id="' . $user->id . '" title="Editar"><i class="fas fa-edit"></i></button>'
                . '<button type="button" class="btn btn-warning btn-toggle-user" data-id="' . $user->id . '" title="Ativar/desativar"><i class="fas fa-power-off"></i></button>'
                . '</div>',
        ];
    }

    private function storeAvatar(\Illuminate\Http\UploadedFile $file): string
    {
        $media = app(UploadService::class)->upload($file, 'profile-avatars', [
            'alt_text' => 'Foto de perfil',
        ]);

        return $media->url ?: ('storage/' . ltrim((string) $media->caminho, '/'));
    }

    private function deleteReplacedAvatar(?string $oldReference, ?string $newReference): void
    {
        if (!$oldReference || $oldReference === $newReference) {
            return;
        }

        $this->deleteUserAvatar($oldReference);
    }

    private function deleteUserAvatar(?string $reference): void
    {
        app(UploadService::class)->deleteReference($reference);
    }
}
