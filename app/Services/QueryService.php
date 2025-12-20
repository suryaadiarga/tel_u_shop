<?php

namespace App\Services;

use Illuminate\Http\Request;

class QueryService
{
    public static function perPage(Request $request, int $default = 20, int $max = 100): int
    {
        $perPage = (int) $request->get('per_page', $default);

        if ($perPage < 1) {
            return $default;
        }

        return min($perPage, $max);
    }

    /**
     * @return array{0:string,1:string}
     */
    public static function sort(Request $request, array $allowed, string $defaultBy = 'created_at', string $defaultOrder = 'desc'): array
    {
        $sortBy = $request->get('sort_by', $defaultBy);
        if (!in_array($sortBy, $allowed, true)) {
            $sortBy = $defaultBy;
        }

        $sortOrder = strtolower((string) $request->get('sort_order', $defaultOrder));
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = $defaultOrder;
        }

        return [$sortBy, $sortOrder];
    }
}
