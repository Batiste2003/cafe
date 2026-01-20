<?php

namespace App\Http\Requests;

use App\Domain\Product\Constant\ProductConstant;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductOptionRequest extends FormRequest
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
                'required',
                'string',
                'min:'.ProductConstant::OPTION_NAME_MIN_LENGTH,
                'max:'.ProductConstant::OPTION_NAME_MAX_LENGTH,
            ],
            'is_required' => [
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
            'name.required' => 'Le nom de l\'option est obligatoire.',
            'name.min' => 'Le nom de l\'option doit contenir au moins :min caractère.',
            'name.max' => 'Le nom de l\'option ne peut pas dépasser :max caractères.',
        ];
    }
}
