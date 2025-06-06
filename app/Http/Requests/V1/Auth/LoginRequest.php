<?php

namespace App\Http\Requests\V1\Auth;

use App\Traits\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
class LoginRequest extends FormRequest
{
    use CustomFailedValidation;

    public function authorize(): bool
    {
        return true;
    }
 
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }
}
