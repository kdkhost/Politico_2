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

namespace App\Services\Permissoes;

use App\Models\Module;
use App\Models\Permissions\Permission;
use App\Models\Permissions\PermissionGroup;
use App\Models\Permissions\Profile;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class PermissaoService
{
    public function getAllPermissions(): LengthAwarePaginator
    {
        return Permission::with('group')
            ->orderBy('permission_group_id')
            ->orderBy('nome')
            ->paginate(config('sistema.pagination_per_page', 15));
    }

    public function getProfilePermissions(int $profileId): array
    {
        $profile = Profile::with('permissions')->findOrFail($profileId);

        return $profile->permissions->pluck('slug')->toArray();
    }

    public function syncProfilePermissions(int $profileId, array $permissions): Profile
    {
        $profile = Profile::findOrFail($profileId);
        $permissions = array_values(array_filter($permissions, fn ($permission): bool => $permission !== null && $permission !== ''));

        if ($permissions !== [] && !is_numeric($permissions[0])) {
            $permissions = Permission::whereIn('slug', $permissions)->pluck('id')->all();
        }

        $profile->permissions()->sync($permissions);

        Cache::forget("profile_permissions_{$profileId}");

        return $profile;
    }

    public function userHasPermission(User $user, string $permissionSlug): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        return Cache::remember("user_{$user->id}_permission_{$permissionSlug}", 3600, function () use ($user, $permissionSlug) {
            return $user->profile && $user->profile->permissions()
                ->where('slug', $permissionSlug)
                ->exists();
        });
    }

    public function userHasModuleAccess(User $user, string $moduleSlug): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $module = Module::where('slug', $moduleSlug)->where('active', true)->first();

        if (!$module) {
            return false;
        }

        return Cache::remember("user_{$user->id}_module_{$moduleSlug}", 3600, function () use ($user, $moduleSlug) {
            return $user->profile && $user->profile->permissions()
                ->where('slug', 'like', "{$moduleSlug}.%")
                ->exists();
        });
    }

    public function getModulePermissions(string $moduleSlug): array
    {
        return Permission::where('slug', 'like', "{$moduleSlug}.%")
            ->with('group')
            ->get()
            ->toArray();
    }

    public function createInitialPermissions(): void
    {
        $modules = config('modules', []);

        foreach ($modules as $slug => $module) {
            $group = PermissionGroup::firstOrCreate(
                ['slug' => $slug],
                [
                    'nome' => $module['name'] ?? ucfirst($slug),
                    'descricao' => $module['description'] ?? '',
                    'modulo' => $slug,
                ]
            );

            $actions = ['view', 'create', 'edit', 'delete', 'publish'];

            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['slug' => "{$slug}.{$action}"],
                    [
                        'permission_group_id' => $group->id,
                        'nome' => ucfirst($action) . ' ' . ($module['name'] ?? $slug),
                        'descricao' => "Permite {$action} em {$slug}",
                    ]
                );
            }
        }
    }

    public function getPermissionGroups(): array
    {
        return PermissionGroup::with(['permissions' => function ($query) {
            $query->orderBy('nome');
        }])
            ->orderBy('nome')
            ->get()
            ->toArray();
    }
}
