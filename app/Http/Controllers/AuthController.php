<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;

#[Group('Auth')]
class AuthController extends Controller
{
    /**
     * Create new user
     *
     * Extremely simplified register method for the sake of the example.
     * In real life, would require several steps (email validation, for instance)
     * And would be better at its own controller
     *
     * @param RegisterRequest $request
     */
    public function register(RegisterRequest $request)
    {
        $user = new User();
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        $user->name = $request->input('name');
        $user->save();

        return $user;
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     * @return array
     */
    #[Response(['user' => 33, 'token' => 'random_looking_token_here'], status: 200, description: "Successful login")]
    public function login(LoginRequest $request)
    {
        $user = Auth::user();
        $token = $user->createToken(
            'auth-token',
            ['*'],
            now()->addMinutes(config('sanctum.expiration'))
        )->plainTextToken;

        return [
            'user' => $user->id,
            'token' => $token,
        ];
    }

    /**
     * Get current user
     *
     * @param Request $request
     * @return User|mixed
     */
    #[Authenticated]
    public function user(Request $request)
    {
        return $request->user();
    }
}
