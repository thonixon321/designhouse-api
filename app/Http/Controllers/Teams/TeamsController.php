<?php

namespace App\Http\Controllers\Teams;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\ITeam;

class TeamsController extends Controller
{
    protected $teams;

    public function __construct(ITeam $teams)
    {
        $this->teams = $teams;
    }

    //get list of all teams (eg for Search)
    public function index()
    {
        $teams = $this->teams->all();

        return TeamResource::collection($teams);
    }

    //Save team to DB
    public function store(Request $request)
    {
        //only need to validate the user's team name - unique because we will use it for the team slug (can't use a taken team name)
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name']
        ]);
        //create team in DB    
        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        //create the membership in the team for the user who created the team - we have a boot method in the team model that observes when this team is created and then automatically adds the current user to the team members

        return new TeamResource($team);
    }

    //update team info
    public function update(Request $request, $id)
    {
        $team = $this->teams->find($id);
        $this->authorize('update', $team);
        //pass the id of the current team to exclude it from the unique check
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,'.$id]
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    //find team by id
    public function findById($id)
    {
        $team = $this->teams->find($id);
        return new TeamResource($team);
    }

    //get teams current user belongs to
    public function fetchUserTeams()
    {
        $teams = $this->teams->fetchUserTeams();
        
        return TeamResource::collection($teams);
    }

    //get team by slug for public view
    public function findBySlug()
    {

    }

    //delete a team
    public function destroy($id)
    {
        $team = $this->teams->find($id);
        $this->authorize('delete', $team);
        $this->teams->delete($id);

        return response()->json(['message' => 'Record deleted'], 200);
    }
}
