<?php

namespace App\Http\Requests;

use App\Domain\Product\Constant\ProductConstant;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductOptionValueRequest extends FormRequest
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
            'value' => [
                'required',
                'string',
                'min:'.ProductConstant::OPTION_VALUE_MIN_LENGTH,
                'max:'.ProductConstant::OPTION_VALUE_MAX_LENGTH,
            ],
            'price_add_cent_ht' => [
                'required',
                'integer',
                'min:0',
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
            'value.required' => 'La valeur est obligatoire.',
            'value.min' => 'La valeur doit contenir au moins :min caractère.',
            'value.max' => 'La valeur ne peut pas dépasser :max caractères.',
            'price_add_cent_ht.required' => 'Le supplément de prix est obligatoire.',
            'price_add_cent_ht.integer' => 'Le supplément de prix doit être un entier (en centimes).',
            'price_add_cent_ht.min' => 'Le supplément de prix ne peut pas être négatif.',
        ];
    }
}
