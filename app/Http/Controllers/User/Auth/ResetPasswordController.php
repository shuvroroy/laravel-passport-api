<?php

namespace App\Http\Controllers\User\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\User\Auth\ResetPasswordFormRequest;

class ResetPasswordController extends Controller
{
    public function reset(ResetPasswordFormRequest $request)
    {
        $passwordReset = $this->find($request->token, $request->email);

        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        $passwordReset->delete();

        return response()->json([
            'message' => trans('passwords.reset')
        ], Response::HTTP_ACCEPTED);
    }

    protected function find($token, $email)
    {
        $passwordReset = PasswordReset::where([
            ['token', $token],
            ['email', $email]
        ])->first();

        if (!$passwordReset) {
            throw ValidationException::withMessages([
                'email' => [trans('passwords.token')],
            ]);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();

            throw ValidationException::withMessages([
                'email' => [trans('passwords.token')],
            ]);
        }

        return $passwordReset;
    }
}
