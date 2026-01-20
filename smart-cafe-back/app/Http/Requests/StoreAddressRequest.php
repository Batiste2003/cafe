<?php

namespace App\Http\Requests;

use App\Domain\Address\Constant\AddressConstant;
use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:' . AddressConstant::LABEL_MAX_LENGTH],
            'address_line1' => ['required', 'string', 'max:' . AddressConstant::ADDRESS_LINE_MAX_LENGTH],
            'address_line2' => ['nullable', 'string', 'max:' . AddressConstant::ADDRESS_LINE_MAX_LENGTH],
            'city' => ['required', 'string', 'max:' . AddressConstant::CITY_MAX_LENGTH],
            'postal_code' => ['required', 'string', 'max:' . AddressConstant::POSTAL_CODE_MAX_LENGTH],
            'country' => ['required', 'string', 'max:' . AddressConstant::COUNTRY_MAX_LENGTH],
        ];
    }
}
