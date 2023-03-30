<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AddBet extends FormRequest
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
            'coef' => ['required', 'numeric', 'min:1.01', 'max:101'],
            'sum' => ['required', 'numeric', 'min:1'],
            'id' => ['required', 'integer', 'exists:actions'],
            'move' => ['required', 'integer', 'min:1', 'max:2'],
            'team' => ['required', 'integer', 'min:1']
        ];
    }
}
