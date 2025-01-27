<?php

namespace App\Http\Requests\API\MobileApp\Auth\Customer;

use App\Http\Requests\API\BaseAPIRequest;
use Illuminate\Validation\Rule;

class CustomerResetPasswordVerificationRequest extends BaseAPIRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'phone' => "required",
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
            'verification_code' => 'required'
        ];
    }

}
