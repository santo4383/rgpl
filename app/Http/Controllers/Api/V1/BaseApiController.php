<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Standard success response wrapper.
     */
    protected function successResponse(mixed $data = null, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Standard error response wrapper.
     */
    protected function errorResponse(string $errorCode, string $message, int $statusCode = 400, mixed $details = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => $message,
                'details' => $details,
            ],
        ], $statusCode);
    }

    /**
     * Standard paginated response.
     */
    protected function paginatedResponse($paginator, $items = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $items ?? $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ]);
    }
}
