<?php

namespace App\Http\Requests\V1\Setup;

use App\Traits\CustomFailedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    use CustomFailedValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // 'email' => ['required', 'email', Rule::unique('users')->ignore($this->route('user'))], // we should not update user email
            'password' => 'sometimes|string|min:8|confirmed',
            'active' => 'boolean',
        ];
    }
}
