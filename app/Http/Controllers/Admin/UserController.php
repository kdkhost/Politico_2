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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        return view('admin.usuarios.index');
    }

    public function show(int $id)
    {
        try {
            $user = User::with('profile')->findOrFail($id);

            if (!request()->expectsJson() && !request()->ajax()) {
                return view('admin.usuarios.show', compact('user'));
            }

            return response()->json([
                'status' => 'success',
                'data' => $user,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao carregar usuário.'], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $query = User::with('profile:id,nome');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('profile_id')) {
                $query->where('profile_id', $request->profile_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $sortField = in_array((string) $request->sort_by, self::SORTABLE_FIELDS, true)
                ? (string) $request->sort_by
                : 'created_at';
            $sortOrder = strtolower((string) $request->sort_order) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);

            $users = $query->paginate(config('sistema.pagination_per_page', 15));
            $total = $users->total();

            return response()->json([
                'status' => 'success',
                'data' => $users->items(),
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
                'status' => 'required|in:active,inactive',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);

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

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'profile_id' => 'nullable|exists:profiles,id',
                'telefone' => 'nullable|string|max:20',
                'cargo' => 'nullable|string|max:255',
                'is_super_admin' => 'boolean',
                'status' => 'required|in:active,inactive',
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

            $user->update($validated);

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
            session()->put('impersonated_by', Auth::id());
            Auth::login($user);

            return response()->json([
                'status' => 'success',
                'message' => "Você agora está logado como {$user->name}.",
                'redirect' => route('admin.dashboard'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao impersonar usuário.'], 500);
        }
    }
}
