<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Hashids;

class UpdateProfileRequest extends FormRequest {

    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl() {
        $url = $this->redirector->getUrlGenerator();
        $action = FormRequest::input('action');
        $id = $this->route()->parameter('profile');
        return $url->route('profile.index', [$id, "action=" . $action]);
    }

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

        $action = FormRequest::input('action');
        $action = app("request")->input('action');

        switch ($action) {
            case "password":
                return [
                    'current_password' => 'required',
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required',
                ];
                break;
            case "profile":
                $validate = [
                    'first_name' => 'required|max:50',
                    'last_name' => 'required|max:50',
                    'address_line_1' => 'required',
                ];

                if (isset($_FILES['profile_img'])) {
                    $validate["profile_img"] = 'mimes:jpeg,png,jpg,JPEG,PNG,JPG';
                }

                return $validate;
                break;
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator) {
        // checks user current password
        // before making changes
        $action = FormRequest::input('action');
        if ($action == "password") {
            $validator->after(function ($validator) {
                if (!Hash::check($this->current_password, $this->user()->password)) {
                    $validator->errors()->add('current_password', 'Your current password is incorrect.');
                }
            });
            return;
        }
    }

}
