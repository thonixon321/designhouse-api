<?php

namespace App\Http\Controllers\Teams;

use Mail;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use App\Mail\SendInvitationToJoinTeam;
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
            //use helper function from down below
            $this->createInvitation(false, $team, $request->email);
            return response()->json([
                'message' => 'Invitation sent to new user'
            ], 200);
        }
        //if user does exist in platform...
        //check if team already has the user
        if ($team->hasUser($recipient)) {
            return response()->json([
                'email' => 'This user seems to be a team member already'
            ], 422);
        }

        //send the invitation to user
        $this->createInvitation(true, $team, $request->email);
        return response()->json([
            'email' => 'Invitation sent to existing user'
        ], 200);
    }

    public function resend($id) 
    {
        $invitation = $this->invitations->find($id);
        //check if user owns the team
        $this->authorize('resend', $invitation);
        $recipient = $this->users->findByEmail($invitation->recipient_email);

        Mail::to($invitation->recipient_email)
                ->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));

        return response()->json(['message' => 'Invitation resent'], 200);
    }

    public function respond(Request $request, $id) 
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required']
        ]);
        $token = $request->token;
        $decision = $request->decision; // 'accept' or 'deny'
        $invitation = $this->invitations->find($id);  
        //check if the invitation belongs to the user
        $this->authorize('respond', $invitation);
        //check that tokens match
        if ($invitation->token !== $token) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }
        //check if accepted
        if ($decision !== 'deny') {
           $this->invitations->addUserToTeam($invitation->team, auth()->id());
        }

        $invitation->delete();
        return response()->json(['message' => 'Successful'], 200);
    }

    public function destroy($id) 
    {
        $invitation = $this->invitations->find($id);
        //use policy to make sure user who deletes invitation is indeed the one who actually sent the invitation
        $this->authorize('delete', $invitation);  
        $invitation->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }

    protected function createInvitation(bool $user_exists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime())) //just one of many ways to make a random unique string in laravel
        ]);
        //mail invitation to non signed up user
        Mail::to($email)
                ->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }
}
