<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;

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
    }

    public function user(Request $request)
    {
        return $request->user();
    }
}
