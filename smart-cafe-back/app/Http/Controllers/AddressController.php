<?php

namespace App\Http\Controllers;

use App\Domain\Address\DTOs\CreateAddressInputDTO;
use App\Domain\Address\DTOs\UpdateAddressInputDTO;
use App\Domain\Address\Services\CreateAddressService;
use App\Domain\Address\Services\DeleteAddressService;
use App\Domain\Address\Services\ListAddressesService;
use App\Domain\Address\Services\UpdateAddressService;
use App\Domain\ApiResponse\DTOs\PagingDTO;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessWithPaginationService;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function __construct(
        private readonly ApiResponseSuccessService $successResponse,
        private readonly ApiResponseSuccessWithPaginationService $paginatedResponse
    ) {}

    public function index(ListAddressesService $service): JsonResponse
    {
        $paginator = $service->execute();

        return $this->paginatedResponse->execute(
            message: 'Liste des adresses',
            data: AddressResource::collection($paginator->items()),
            paging: PagingDTO::fromPaginator($paginator)
        );
    }

    public function store(
        StoreAddressRequest $request,
        CreateAddressService $service
    ): JsonResponse {
        $dto = CreateAddressInputDTO::fromArray($request->validated());
        $address = $service->execute($dto);

        return $this->successResponse->execute(
            message: 'Adresse créée avec succès',
            data: new AddressResource($address),
            statusCode: 201
        );
    }

    public function show(Address $address): JsonResponse
    {
        return $this->successResponse->execute(
            message: 'Détail de l\'adresse',
            data: new AddressResource($address)
        );
    }

    public function update(
        UpdateAddressRequest $request,
        Address $address,
        UpdateAddressService $service
    ): JsonResponse {
        $dto = UpdateAddressInputDTO::fromArray($request->validated());
        $address = $service->execute($address, $dto);

        return $this->successResponse->execute(
            message: 'Adresse mise à jour',
            data: new AddressResource($address)
        );
    }

    public function destroy(
        Address $address,
        DeleteAddressService $service
    ): JsonResponse {
        $service->execute($address);

        return $this->successResponse->execute(
            message: 'Adresse supprimée',
            statusCode: 200
        );
    }
}
