<?php

namespace App\Http\Requests\API\MobileApp\Auth\Customer;

use App\Http\Requests\API\BaseAPIRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerRegisterRequest extends BaseAPIRequest {

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
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $existsInCustomers = DB::table('customers')
                        ->where('email', $value)
                        ->whereNull('deleted_at')
                        ->exists();
                    if ($existsInCustomers) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'name' => 'required|string|min:3',
            // 'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
            'gender_preference' => 'nullable|string',
            'program_id' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'program_id.required' => 'An error occurred while setting up your account. Please try agin after reopen the app.',
        ];
    }
    public function passedValidation()
    {
        // Add 'register_by_email' to the request after validation
        $this->merge([
            'register_type' => 'code',
        ]);
    }

}
