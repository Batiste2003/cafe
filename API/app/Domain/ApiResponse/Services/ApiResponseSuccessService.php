<?php

namespace App\Domain\ApiResponse\Services;

use App\Domain\ApiResponse\DTOs\ApiSuccessResponseDTO;
use Illuminate\Http\JsonResponse;

class ApiResponseSuccessService
{
    public function execute(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        $dto = new ApiSuccessResponseDTO(
            success: true,
            message: $message,
            data: $data
        );

        return response()->json($dto->toArray(), $statusCode);
    }
}
