<?php

namespace App\Http\Requests\User\Setting;

use App\Rules\User\Setting\CurrentPassword;
use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateFormRequest extends FormRequest
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
            'password_current' => ['required', new CurrentPassword],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ];
    }
}
