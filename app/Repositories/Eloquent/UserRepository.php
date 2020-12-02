<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\IUser;
use App\Repositories\Eloquent\BaseRepository;


class UserRepository extends BaseRepository implements IUser
{
    //since this is implementing the IUser interface, we have to provide a concrete implementation of all the methods inside the IUser interface

    //we want to use the base repository methods which holds all the common methods used on models and to do that we dynamically send this model name to that BaseRepository class
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}