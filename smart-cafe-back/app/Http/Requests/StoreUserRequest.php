<?php

namespace App\Http\Requests;

use App\Domain\User\Constant\UserConstant;
use App\Domain\User\Enumeration\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
        $creatableRoles = array_map(
            fn (UserRoleEnum $role) => $role->value,
            UserRoleEnum::creatableByAdmin()
        );

        return [
            'name' => [
                'required',
                'string',
                'min:' . UserConstant::NAME_MIN_LENGTH,
                'max:' . UserConstant::NAME_MAX_LENGTH,
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'string',
                'min:' . UserConstant::PASSWORD_MIN_LENGTH,
                'max:' . UserConstant::PASSWORD_MAX_LENGTH,
                Password::defaults(),
            ],
            'role' => [
                'required',
                'string',
                Rule::in($creatableRoles),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.in' => UserConstant::CANNOT_CREATE_ADMIN,
        ];
    }
}
