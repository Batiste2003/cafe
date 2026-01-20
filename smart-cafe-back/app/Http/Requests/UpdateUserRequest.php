<?php

namespace App\Http\Requests;

use App\Domain\User\Constant\UserConstant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => [
                'sometimes',
                'string',
                'min:' . UserConstant::NAME_MIN_LENGTH,
                'max:' . UserConstant::NAME_MAX_LENGTH,
            ],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                'sometimes',
                'string',
                'min:' . UserConstant::PASSWORD_MIN_LENGTH,
                'max:' . UserConstant::PASSWORD_MAX_LENGTH,
                Password::defaults(),
            ],
        ];
    }
}
