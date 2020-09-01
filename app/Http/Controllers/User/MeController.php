<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class MeController extends Controller
{
    //check if user that requested this route endpoint is authenticated (logged in)
    public function getMe()
    {
        if (auth()->check()) {
            $user = auth()->user();
            //to display the created at time in an easier to read format (e.g. '33 minutes ago')
            // $user->created_at_human = $user->created_at->diffForHumans();

            //user is logged in (authenticate check passed), so return user in json
            // return response()->json(["user" => auth()->user(), 200]);

            //using the user resource to return the authenticated user (json by default but puts the user info in "data" key in response)
            return new UserResource($user); 
        } 
        //not authenticated
        return response()->json(null, 401);

    }
}
