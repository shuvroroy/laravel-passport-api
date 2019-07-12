<?php

namespace App\Http\Controllers\User\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\User\Auth\LoginFormRequest;

class LoginController extends Controller
{
    public function login(LoginFormRequest $request)
    {
        if (!auth()->attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (!$request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => trans('verification.unverified')
            ], 403);
        }

        $token = $request->user()->createToken('Personal Access Token')->accessToken;

        return (new UserResource($request->user()))
            ->additional([
                'meta' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => config('auth.guards.api.expire') * 60
                ]
            ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => trans('auth.logout')
        ], 200);
    }
}
