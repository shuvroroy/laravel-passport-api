<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\User\Auth\VerificationFormRequest;
use App\Notifications\User\Auth\EmailVerificationNotification;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, User $user)
    {
        if (! URL::hasValidSignature($request)) {
            return response()->json([
                'message' => trans('verification.invalid'),
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => trans('verification.already_verified'),
            ], 400);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => trans('verification.verified'),
        ]);
    }

    public function resend(VerificationFormRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => [trans('verification.already_verified')],
            ]);
        }

        $user->notify(
            new EmailVerificationNotification($user)
        );

        return response()->json([
            'message' => trans('verification.sent')
        ]);
    }
}
