<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Rules\CheckSamePassword;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        //even though these fields aren't required at registration, we require them here
        //when user wants to update their profile (note: location is an object from the json packet sent from frontend)
        $this->validate($request, [
            'tagline' => ['required'],
            'name' => ['required'],
            'about' => ['required', 'string', 'min:20'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180']
        ]);

        //using the spatial library we pulled in, we set location with the longitude and latitude
        $location = new Point($request->location['latitude'], $request->location['longitude']);

        $user->update([
            'tagline' => $request->tagline,
            'name' => $request->name,
            'about' => $request->about,
            'formatted_address' => $request->formatted_address,
            'location' => $location,
            'available_to_hire'  => $request->available_to_hire
        ]);

        return new UserResource($user);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword], //new MatchOldPassword checks the rule we set up (current password must match one on file)
            'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword] //saying confirmed means password_confirmation field must be passed in along with the password field in th request from frontend - another rule used that we set up as well
        ]);

        //now before we update the password, we need to make sure the current password matches what we have on file and that the new password isn't the current password - we make rules for this with php artisan - you can see where these rules are used above in $this->validate()

        $request->user()->update([
            'password' => bcrypt($request->password) //encrypt the new password 
        ]);

        return response()->json(['message' => 'password updated!'], 200);
    }
}
