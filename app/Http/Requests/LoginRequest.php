<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|exists:users,email',
            'password' => 'required|string'
        ];
    }

    public function authorize(): bool
    {
        return Auth::attempt([
            'email' => $this->input('email'),
            'password' => $this->input('password'),
        ]);
    }
}
