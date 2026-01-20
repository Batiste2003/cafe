<?php

namespace App\Http\Requests;

use App\Domain\Address\Constant\AddressConstant;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['sometimes', 'string', 'max:' . AddressConstant::LABEL_MAX_LENGTH],
            'address_line1' => ['sometimes', 'string', 'max:' . AddressConstant::ADDRESS_LINE_MAX_LENGTH],
            'address_line2' => ['nullable', 'string', 'max:' . AddressConstant::ADDRESS_LINE_MAX_LENGTH],
            'city' => ['sometimes', 'string', 'max:' . AddressConstant::CITY_MAX_LENGTH],
            'postal_code' => ['sometimes', 'string', 'max:' . AddressConstant::POSTAL_CODE_MAX_LENGTH],
            'country' => ['sometimes', 'string', 'max:' . AddressConstant::COUNTRY_MAX_LENGTH],
        ];
    }
}
