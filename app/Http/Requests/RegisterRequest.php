<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
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
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field cannot be empty.',
            'name.string' => 'The name must be a valid string.',
            'email.required' => 'Email field cannot be empty.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Password field cannot be empty.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }


    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(
            response()->json([
                'status' => 'Validation failed.',
                'message' => 'Please check the input data.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
