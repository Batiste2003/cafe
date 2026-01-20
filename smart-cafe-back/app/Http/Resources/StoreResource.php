<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'banner' => new StoredFileResource($this->whenLoaded('banner')),
            'logo' => new StoredFileResource($this->whenLoaded('logo')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'users_count' => $this->when(isset($this->users_count), $this->users_count),
            'is_deleted' => $this->trashed(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->when($this->trashed(), $this->deleted_at?->format('Y-m-d H:i:s')),
        ];
    }
}
