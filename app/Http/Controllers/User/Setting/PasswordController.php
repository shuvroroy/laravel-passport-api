<?php

namespace App\Http\Controllers\User\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\Setting\PasswordUpdateFormRequest;

class PasswordController extends Controller
{
    public function update(PasswordUpdateFormRequest $request)
    {
        $user = auth()->user();

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
