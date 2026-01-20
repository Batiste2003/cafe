<?php

namespace App\Http\Requests;

use App\Domain\Product\Constant\ProductConstant;
use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductVariantRequest extends FormRequest
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
        $variantId = $this->route('variant')?->id;

        return [
            'sku' => [
                'sometimes',
                'string',
                'min:'.ProductConstant::SKU_MIN_LENGTH,
                'max:'.ProductConstant::SKU_MAX_LENGTH,
                Rule::unique('product_variants', 'sku')->ignore($variantId),
            ],
            'price_cent_ht' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'cost_price_cent_ht' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'stock' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'is_default' => [
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
            'sku.min' => 'Le SKU doit contenir au moins :min caractères.',
            'sku.max' => 'Le SKU ne doit pas dépasser :max caractères.',
            'sku.unique' => 'Ce SKU existe déjà.',
            'price_cent_ht.min' => 'Le prix HT doit être positif.',
            'cost_price_cent_ht.min' => 'Le prix de revient doit être positif.',
            'stock.min' => 'Le stock doit être positif.',
            'images.array' => 'Les images doivent être fournies sous forme de tableau.',
            'images.max' => 'Vous ne pouvez pas ajouter plus de :max images.',
            'images.*.file' => 'Chaque image doit être un fichier valide.',
            'images.*.mimes' => 'Les images doivent être au format jpeg, png, gif ou webp.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
        ];
    }
}
