<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponder
{
    public static function ok(array $data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data
        ], $code);
    }

    public static function fail(string $message, int $code = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
}
