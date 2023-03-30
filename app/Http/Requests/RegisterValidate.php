<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterValidate extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'name' => ['required', 'string', 'max:50'],
            'login' => ['required', 'string', 'max:50'],
            'number' => ['required', 'numeric', 'regex:/^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){10,14}(\s*)?$/', 'unique:users']
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
            'email.unique' => 'Пользователь с таким Email уже существует!',
            'email.required' => 'Введите Email!',
            'email.email' => 'Не верный формат Email!',
            'password.required' => 'Введите пароль!',
            'password.min' => 'Пароль должен содержать не менее 8 символов!',
            'name.required' => 'Введите имя!',
            'name.max' => 'Максимальное количество символов: 50!',
            'login.required' => 'Введите логин!',
            'login.max' => 'Максимальное количество символов: 50!',
            'number.regex' => 'Не верный формат телефона!',
            'number.required' => 'Введите свой телефон!',
            'number.unique' => 'Пользователь с таким номером уже существует!'
        ];
    }
}
