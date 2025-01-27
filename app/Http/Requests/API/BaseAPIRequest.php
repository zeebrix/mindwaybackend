<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseAPIRequest extends FormRequest {

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
                            'code' => 422,
                            'status' => 'Validation Failed',
                            'message' => "The given data was invalid.",
                            'validation_params_error' => $validator->errors(),
                                ], 422));
    }

}
