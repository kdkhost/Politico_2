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

namespace App\Support;

use Illuminate\Http\Request;

final class DataTableRequest
{
    /**
     * @param array<string, string> $columnMap
     * @param array<int, string> $filterKeys
     * @return array<string, mixed>
     */
    public static function filters(Request $request, array $columnMap, array $filterKeys = [], int $defaultPerPage = 25): array
    {
        $filters = $request->only($filterKeys);

        $search = trim((string) ($request->input('search.value') ?? $request->input('search', '')));
        if ($search !== '') {
            $filters['search'] = mb_substr($search, 0, 120);
        }

        $length = (int) ($request->input('length') ?? $request->input('per_page') ?? $defaultPerPage);
        if ($length < 1) {
            $length = $defaultPerPage;
        }

        $perPage = min(max($length, 1), 100);
        $start = max(0, (int) $request->input('start', 0));

        $filters['per_page'] = $perPage;
        $filters['page'] = intdiv($start, $perPage) + 1;

        $sortBy = self::sortBy($request, $columnMap);
        if ($sortBy !== null) {
            $filters['sort_by'] = $sortBy;
        }

        $filters['sort_order'] = self::sortOrder($request);

        return $filters;
    }

    /**
     * @param array<string, string> $columnMap
     */
    private static function sortBy(Request $request, array $columnMap): ?string
    {
        $requested = (string) $request->input('sort_by', '');
        if ($requested !== '') {
            return $columnMap[$requested] ?? $requested;
        }

        $columnIndex = $request->input('order.0.column');
        if ($columnIndex === null || $columnIndex === '') {
            return null;
        }

        $name = (string) ($request->input("columns.{$columnIndex}.name") ?: $request->input("columns.{$columnIndex}.data", ''));
        if ($name === '') {
            return null;
        }

        return $columnMap[$name] ?? $name;
    }

    private static function sortOrder(Request $request): string
    {
        $direction = strtolower((string) ($request->input('order.0.dir') ?? $request->input('sort_order', 'desc')));

        return $direction === 'asc' ? 'asc' : 'desc';
    }
}
