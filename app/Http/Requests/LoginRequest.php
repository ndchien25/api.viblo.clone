<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email_or_username", "password"},
 *     @OA\Property(property="email_or_username", type="string", example="nona.upton@example.org"),
 *     @OA\Property(property="password", type="string", format="password", example="password")
 * )
 */
class LoginRequest extends FormRequest
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
            'email_or_username' => 'required|string|max:50',
            'password' => 'required|string|max:50',
        ];
    }
}
