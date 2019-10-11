<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        return (new UserResource(auth()->user()));
    }
}
