<?php

namespace App\Http\Requests;

use App\Domain\Product\Constant\ProductConstant;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductOptionRequest extends FormRequest
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
                'min:'.ProductConstant::OPTION_NAME_MIN_LENGTH,
                'max:'.ProductConstant::OPTION_NAME_MAX_LENGTH,
            ],
            'is_required' => [
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
            'name.min' => 'Le nom de l\'option doit contenir au moins :min caractère.',
            'name.max' => 'Le nom de l\'option ne peut pas dépasser :max caractères.',
        ];
    }
}
