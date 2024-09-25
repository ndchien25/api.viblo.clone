<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     required={"username", "email", "password", "c_password"},
 *     @OA\Property(property="username", type="string", example="username123"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123"),
 *     @OA\Property(property="c_password", type="string", format="password", example="password123")
 * )
 */
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
            'display_name' => 'required|string|regex:/^[\pL\s\-]+$/u|max:50',
            'password' => 'required|string|min:8|max:50',
            'c_password' => 'required|string|same:password|max:50',
        ];
    }
}
