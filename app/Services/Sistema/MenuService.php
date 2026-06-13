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

namespace App\Services\Sistema;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MenuService
{
    public function getByLocation(string $location): ?Menu
    {
        return Menu::with(['items' => function ($query) {
            $query->with('children')
                ->whereNull('parent_id')
                ->orderBy('ordem');
        }])
            ->where('localizacao', $location)
            ->first();
    }

    public function buildMenuTree(string $location): array
    {
        $cacheKey = "menu_tree_{$location}";

        return Cache::remember($cacheKey, 3600, function () use ($location) {
            $menu = $this->getByLocation($location);

            if (!$menu) {
                return [];
            }

            return $this->buildTree($menu->items->toArray());
        });
    }

    public function createItem(array $data): MenuItem
    {
        $menu = Menu::firstOrCreate(
            ['slug' => Str::slug($data['localizacao'] ?? 'main')],
            [
                'nome' => $data['menu_nome'] ?? 'Menu Principal',
                'localizacao' => $data['localizacao'] ?? 'main',
                'descricao' => $data['menu_descricao'] ?? '',
            ]
        );

        $maxOrdem = MenuItem::where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->max('ordem');

        $item = MenuItem::create([
            'menu_id' => $menu->id,
            'parent_id' => $data['parent_id'] ?? null,
            'titulo' => $data['titulo'],
            'url' => $data['url'] ?? '#',
            'icone' => $data['icone'] ?? null,
            'target' => $data['target'] ?? '_self',
            'route' => $data['route'] ?? null,
            'params' => $data['params'] ?? null,
            'ordem' => $data['ordem'] ?? ($maxOrdem ?? 0) + 1,
            'active' => $data['active'] ?? true,
            'permission' => $data['permission'] ?? null,
        ]);

        Cache::forget("menu_tree_{$menu->localizacao}");

        return $item;
    }

    public function updateItem(int $id, array $data): MenuItem
    {
        $item = MenuItem::findOrFail($id);

        $item->update($data);

        Cache::forget("menu_tree_{$item->menu->localizacao}");

        return $item->fresh();
    }

    public function deleteItem(int $id): bool
    {
        $item = MenuItem::with('menu')->findOrFail($id);

        MenuItem::where('parent_id', $id)->update(['parent_id' => null]);

        $result = (bool) $item->delete();

        Cache::forget("menu_tree_{$item->menu->localizacao}");

        return $result;
    }

    public function reorderItems(array $order): void
    {
        foreach ($order as $position => $itemId) {
            MenuItem::where('id', $itemId)->update(['ordem' => $position]);
        }

        $item = MenuItem::find($order[0] ?? null);

        if ($item && $item->relationLoaded('menu')) {
            Cache::forget("menu_tree_{$item->menu->localizacao}");
        }
    }

    public function renderMenu(string $location, string|null $view = null): string
    {
        $menu = $this->getByLocation($location);

        if (!$menu) {
            return '';
        }

        if ($view) {
            return View::make($view, ['menu' => $menu, 'items' => $menu->items])->render();
        }

        return $this->renderHtml($menu->items);
    }

    protected function buildTree(array $items, int|null $parentId = null): array
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item['parent_id'] === $parentId) {
                $children = [];

                if (!empty($item['children'])) {
                    $children = $this->buildTree($item['children'], null);
                }

                $tree[] = [
                    'id' => $item['id'],
                    'titulo' => $item['titulo'],
                    'url' => $this->resolveUrl($item),
                    'icone' => $item['icone'],
                    'target' => $item['target'],
                    'active' => $item['active'],
                    'permission' => $item['permission'],
                    'children' => $children,
                ];
            }
        }

        return $tree;
    }

    protected function resolveUrl(MenuItem $item): string
    {
        if ($item->route) {
            $params = $item->params ? (array) $item->params : [];

            try {
                return route($item->route, $params, false);
            } catch (\Throwable) {
                return url('/');
            }
        }

        if (!empty($item->url) && $item->url !== '#') {
            if (str_starts_with($item->url, 'http://') || str_starts_with($item->url, 'https://') || str_starts_with($item->url, '/')) {
                return $item->url;
            }

            return url($item->url);
        }

        return '#';
    }

    protected function renderHtml($items): string
    {
        $html = '<ul class="nav-menu">';

        foreach ($items as $item) {
            $hasChildren = $item->children && $item->children->isNotEmpty();
            $url = $this->resolveUrlMenuItem($item);

            $html .= '<li class="nav-item' . ($hasChildren ? ' has-submenu' : '') . '">';
            $html .= '<a href="' . e($url) . '" target="' . e($item->target) . '"' . ($item->icone ? ' class="has-icon"' : '') . '>';

            if ($item->icone) {
                $html .= '<i class="' . e($item->icone) . '"></i> ';
            }

            $html .= e($item->titulo) . '</a>';

            if ($hasChildren) {
                $html .= '<ul class="submenu">';

                foreach ($item->children as $child) {
                    $childUrl = $this->resolveUrlMenuItem($child);
                    $html .= '<li class="nav-item">';
                    $html .= '<a href="' . e($childUrl) . '" target="' . e($child->target) . '">';

                    if ($child->icone) {
                        $html .= '<i class="' . e($child->icone) . '"></i> ';
                    }

                    $html .= e($child->titulo) . '</a></li>';
                }

                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    protected function resolveUrlMenuItem(MenuItem $item): string
    {
        if ($item->route) {
            $params = $item->params ? (array) $item->params : [];

            try {
                return route($item->route, $params, false);
            } catch (\Throwable) {
                return '#';
            }
        }

        if (!empty($item->url) && $item->url !== '#') {
            if (str_starts_with($item->url, 'http://') || str_starts_with($item->url, 'https://') || str_starts_with($item->url, '/')) {
                return $item->url;
            }

            return url($item->url);
        }

        return '#';
    }
}
