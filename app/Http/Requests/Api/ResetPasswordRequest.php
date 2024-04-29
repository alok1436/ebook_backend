<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password_confirmation' => 'required|min:6',
            'password'  => 'required|required_with:password_confirmation|same:password_confirmation',
            'password'  => ['required', 'confirmed',Password::min(6)
                            ->letters()
                            ->mixedCase()
                            ->numbers()
                            ->symbols()
                        ],
        ];
    }
}
