<?php

namespace App\Http\Controllers\User\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\Setting\ProfileUpdateFormRequest;

class ProfileController extends Controller
{
    public function update(ProfileUpdateFormRequest $request)
    {
        $user = auth()->user();

        $user->update($request->only('name', 'email'));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(202);
    }
}
