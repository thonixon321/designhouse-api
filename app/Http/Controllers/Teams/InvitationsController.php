<?php

namespace App\Http\Controllers\Teams;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use App\Repositories\Contracts\IInvitation;

class InvitationsController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(
        IInvitation $invitations,
        ITeam $teams,
        IUser $users)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request, $teamId) 
    {
        //get the team
        $team = $this->teams->find($teamId);
        $user = auth()->user();

        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        //check if user owns the team
        if (! $user->isOwnerOfTeam($team)){
            return response()->json([
                'email' => 'You are not the team owner'
            ], 401);
        }

        //check if email has a pending invitation
        if ($team->hasPendingInvite($request->email)) {
            return response()->json([
                'email' => 'Email already has a pending invite'
            ], 422);
        }

        //get the recipient by email
        $recipient = $this->users->findByEmail($request->email);

        //if $recipient does not exist (not signed up in designhouse), send invitation to join the team after asking them to join the platform
        if (! $recipient) {
            $invitation = $this->invitations->create([
                'team_id' => $team->id,
                'sender_id' => $user->id,
                'recipient_email' => $request->email,
                'token' => md5(uniqid(microtime())) //just one of many ways to make a random unique string in laravel
            ]);
        }
    }

    public function resend($id) 
    {
        
    }

    public function respond(Request $request, $id) 
    {
        
    }

    public function destroy($id) 
    {

    }
}
