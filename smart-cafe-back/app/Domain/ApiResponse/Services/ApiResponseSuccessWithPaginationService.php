<?php

namespace App\Domain\ApiResponse\Services;

use App\Domain\ApiResponse\DTOs\ApiSuccessWithPagingResponseDTO;
use App\Domain\ApiResponse\DTOs\PagingDTO;
use Illuminate\Http\JsonResponse;

class ApiResponseSuccessWithPaginationService
{
    public function execute(string $message, mixed $data, PagingDTO $paging, int $statusCode = 200): JsonResponse
    {
        $dto = new ApiSuccessWithPagingResponseDTO(
            success: true,
            message: $message,
            data: $data,
            paging: $paging
        );

        return response()->json($dto->toArray(), $statusCode);
    }
}
