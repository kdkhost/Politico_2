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

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    protected function successJson(string $message, array|object|null $data = null, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'status' => 'success',
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => [],
        ], $extra));
    }

    protected function errorJson(string $message, array $errors = [], int $status = 422, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'status' => 'error',
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $extra), $status);
    }
}
