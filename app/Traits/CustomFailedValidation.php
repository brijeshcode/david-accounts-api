<?php

namespace App\Traits;

use App\Http\Responses\V1\ApiResponse;
use Illuminate\Contracts\Validation\Validator;

trait CustomFailedValidation
{
    protected function failedValidation(Validator $validator)
    {
 
        ApiResponse::failValidation($validator->errors());

        exit;
    }
}
