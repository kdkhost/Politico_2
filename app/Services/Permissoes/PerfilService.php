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

use App\Models\Permissions\Profile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PerfilService
{
    public function listAll(): LengthAwarePaginator
    {
        return Profile::withCount('users')
            ->orderBy('nivel', 'desc')
            ->orderBy('nome')
            ->paginate(config('sistema.pagination_per_page', 15));
    }

    public function findById(int $id): Profile
    {
        return Profile::with(['permissions', 'permissions.group'])->findOrFail($id);
    }

    public function create(array $data): Profile
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['nome']);

        return Profile::create($data);
    }

    public function update(int $id, array $data): Profile
    {
        $profile = Profile::findOrFail($id);

        if (isset($data['nome']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['nome']);
        }

        $profile->update($data);

        return $profile;
    }

    public function delete(int $id): bool
    {
        $profile = Profile::withCount('users')->findOrFail($id);

        if ($profile->users_count > 0) {
            throw new \RuntimeException('Não é possível excluir um perfil que possui usuários vinculados.');
        }

        return (bool) $profile->delete();
    }

    public function getProfilesWithPermissionsCount(): Collection
    {
        return Profile::withCount('permissions')
            ->orderBy('nivel', 'desc')
            ->get();
    }
}
