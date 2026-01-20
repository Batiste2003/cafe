<?php

namespace App\Http\Requests;

use App\Domain\ProductCategory\Constant\ProductCategoryConstant;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
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
                'min:'.ProductCategoryConstant::NAME_MIN_LENGTH,
                'max:'.ProductCategoryConstant::NAME_MAX_LENGTH,
            ],
            'description' => [
                'nullable',
                'string',
                'max:'.ProductCategoryConstant::DESCRIPTION_MAX_LENGTH,
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:product_categories,id',
            ],
            'is_active' => [
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
            'name.required' => 'Le nom de la catégorie est requis.',
            'name.min' => 'Le nom doit contenir au moins :min caractères.',
            'name.max' => 'Le nom ne doit pas dépasser :max caractères.',
            'description.max' => 'La description ne doit pas dépasser :max caractères.',
            'parent_id.exists' => 'La catégorie parente sélectionnée n\'existe pas.',
        ];
    }
}
