<?php

namespace App\Http\Requests;

use App\Domain\Product\Constant\ProductConstant;
use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
                'min:'.ProductConstant::NAME_MIN_LENGTH,
                'max:'.ProductConstant::NAME_MAX_LENGTH,
            ],
            'description' => [
                'nullable',
                'string',
                'max:'.ProductConstant::DESCRIPTION_MAX_LENGTH,
            ],
            'product_category_id' => [
                'required',
                'integer',
                'exists:product_categories,id',
            ],
            'store_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'store_ids.*' => [
                'required',
                'integer',
                'exists:stores,id',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
            'is_featured' => [
                'sometimes',
                'boolean',
            ],
            'images' => [
                'sometimes',
                'array',
                'max:'.ProductConstant::MAX_GALLERY_IMAGES,
            ],
            'images.*' => [
                'required',
                'file',
                'mimes:'.implode(',', UploadedFileConstant::ALLOWED_IMAGE_EXTENSIONS),
                'max:'.UploadedFileConstant::MAX_IMAGE_SIZE_KB,
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
            'name.required' => 'Le nom du produit est requis.',
            'name.min' => 'Le nom doit contenir au moins :min caractères.',
            'name.max' => 'Le nom ne doit pas dépasser :max caractères.',
            'description.max' => 'La description ne doit pas dépasser :max caractères.',
            'product_category_id.required' => 'La catégorie est requise.',
            'product_category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'store_ids.required' => 'Au moins un magasin doit être sélectionné.',
            'store_ids.array' => 'Les magasins doivent être fournis sous forme de tableau.',
            'store_ids.min' => 'Au moins un magasin doit être sélectionné.',
            'store_ids.*.exists' => 'Un des magasins sélectionnés n\'existe pas.',
            'images.array' => 'Les images doivent être fournies sous forme de tableau.',
            'images.max' => 'Vous ne pouvez pas ajouter plus de :max images.',
            'images.*.file' => 'Chaque image doit être un fichier valide.',
            'images.*.mimes' => 'Les images doivent être au format jpeg, png, gif ou webp.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
        ];
    }
}
