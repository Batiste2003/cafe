<?php

namespace App\Http\Requests;

use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use Illuminate\Foundation\Http\FormRequest;

class AttachProductGalleryRequest extends FormRequest
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
            'image' => [
                'required',
                'file',
                'mimes:'.implode(',', UploadedFileConstant::ALLOWED_IMAGE_EXTENSIONS),
                'max:'.UploadedFileConstant::MAX_IMAGE_SIZE_KB,
            ],
            'position' => [
                'nullable',
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
            'image.required' => 'L\'image est requise.',
            'image.mimes' => 'L\'image doit être au format jpeg, png, gif ou webp.',
            'image.max' => 'L\'image ne doit pas dépasser 5 Mo.',
            'position.min' => 'La position doit être positive.',
        ];
    }
}
