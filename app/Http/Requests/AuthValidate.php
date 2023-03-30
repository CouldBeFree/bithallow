<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthValidate extends FormRequest
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
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required', 'min:8'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.exists' => 'Проверьте правильность введенных данных!',
            'email.required' => 'Введите Email!',
            'email.email' => 'Не верный формат Email!',
            'password.required' => 'Введите пароль!',
            'password.min' => 'Проверьте правильность введенных данных!' 
        ];
    }
}
