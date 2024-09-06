<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'username' => 'required|unique:users,username|max:20',
            'email' => 'required|email|unique:users,email',
            'display_name' => 'required|string|alpha|max:50',
            'password' => 'required|string|min:8|max:50',
            'c_password' => 'required|string|same:password|max:50',
        ];
    }
}
