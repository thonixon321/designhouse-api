<?php
namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Eloquent\BaseRepository;


class InvitationRepository extends BaseRepository implements IInvitation
{
    //since this is implementing the IInvitation interface, we have to provide a concrete implementation of all the methods inside the IInvitation interface

    //we want to use the base repository methods which holds all the common methods used on models and to do that we dynamically send this model name to that BaseRepository class
    public function model()
    {
        return Invitation::class;
    }

    public function addUserToTeam($team, $user_id) 
    {
        $team->members()->attach($user_id);
    }

    public function removeUserFromTeam($team, $user_id) 
    {
        $team->members()->detach($user_id);
    }
}