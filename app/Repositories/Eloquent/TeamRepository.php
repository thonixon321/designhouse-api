<?php
namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Eloquent\BaseRepository;


class TeamRepository extends BaseRepository implements ITeam
{
    //since this is implementing the ITeam interface, we have to provide a concrete implementation of all the methods inside the ITeam interface

    //we want to use the base repository methods which holds all the common methods used on models and to do that we dynamically send this model name to that BaseRepository class
    public function model()
    {
        return Team::class;
    }

    public function fetchUserTeams()
    {
        return auth()->user()->teams;
    }
}