<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\RegisterFormRequest;
use App\Notifications\User\Auth\EmailVerificationNotification;

class RegisterController extends Controller
{
    public function register(RegisterFormRequest $request)
    {
        $user = User::create($request->only('name', 'email', 'password'));

        $user->notify(
            new EmailVerificationNotification($user)
        );

        return response()->json([
            'message' => trans('verification.sent')
        ], Response::HTTP_CREATED);
    }
}
