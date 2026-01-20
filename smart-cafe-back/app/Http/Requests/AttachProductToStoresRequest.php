<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachProductToStoresRequest extends FormRequest
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
            'store_ids.required' => 'Au moins un store doit être sélectionné.',
            'store_ids.array' => 'Les stores doivent être fournis sous forme de tableau.',
            'store_ids.min' => 'Au moins un store doit être sélectionné.',
            'store_ids.*.exists' => 'Un des stores sélectionnés n\'existe pas.',
        ];
    }
}
