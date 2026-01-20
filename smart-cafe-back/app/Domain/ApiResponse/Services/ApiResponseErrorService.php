<?php

namespace App\Domain\ApiResponse\Services;

use App\Domain\ApiResponse\DTOs\ApiErrorResponseDTO;
use App\Domain\Logger\Services\LoggerService;
use Illuminate\Http\JsonResponse;

class ApiResponseErrorService
{
    public function __construct(
        private readonly LoggerService $logger
    ) {}

    public function execute(string $message, string $errorCode = 'GENERIC_ERROR', int $statusCode = 400): JsonResponse
    {
        $this->logger->error($message, $errorCode, [
            'status_code' => $statusCode,
        ]);

        $dto = new ApiErrorResponseDTO(
            success: false,
            message: $message,
            errorCode: $errorCode
        );

        return response()->json($dto->toArray(), $statusCode);
    }
}
