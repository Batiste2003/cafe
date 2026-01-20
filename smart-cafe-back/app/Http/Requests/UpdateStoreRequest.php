<?php

namespace App\Http\Requests;

use App\Domain\Store\Constant\StoreConstant;
use App\Domain\Store\Enumeration\StoreStatusEnum;
use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'min:'.StoreConstant::NAME_MIN_LENGTH,
                'max:'.StoreConstant::NAME_MAX_LENGTH,
            ],
            'banner' => [
                'nullable',
                'file',
                'mimes:'.implode(',', UploadedFileConstant::ALLOWED_IMAGE_EXTENSIONS),
                'max:'.UploadedFileConstant::MAX_IMAGE_SIZE_KB,
            ],
            'logo' => [
                'nullable',
                'file',
                'mimes:'.implode(',', UploadedFileConstant::ALLOWED_IMAGE_EXTENSIONS),
                'max:'.UploadedFileConstant::MAX_IMAGE_SIZE_KB,
            ],
            'address_id' => [
                'nullable',
                'integer',
                'exists:addresses,id',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::enum(StoreStatusEnum::class),
            ],
            'remove_banner' => [
                'sometimes',
                'boolean',
            ],
            'remove_logo' => [
                'sometimes',
                'boolean',
            ],
            'remove_address' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'banner.mimes' => 'La bannière doit être une image (jpeg, png, gif, webp).',
            'banner.max' => 'La bannière ne doit pas dépasser 5 Mo.',
            'logo.mimes' => 'Le logo doit être une image (jpeg, png, gif, webp).',
            'logo.max' => 'Le logo ne doit pas dépasser 5 Mo.',
        ];
    }
}
