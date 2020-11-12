<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class UserController extends Controller
{
    protected $users;
    //inject the repository contracts in the controller (could be as many as you want in the construct parameter here)
    public function __construct(IUser $users)
    {
        $this->users = $users;
    }

    public function index()
    {
        //eager loading tells laravel to fetch the resource together with the provided related models (trying to avoid a situation where one query turns into many queries like when you want to fetch all users and then that also fetches the designs for each user causing another query to the DB for each user to get their designs) - so it should only run the query once if possible to pull all the related info in one chunk
        $users = $this->users->withCriteria([
            new EagerLoad(['designs'])
        ])->all();
        return UserResource::collection($users);
    }
}
