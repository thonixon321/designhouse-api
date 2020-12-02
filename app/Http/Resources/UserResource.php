<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //auth user is passed in and this returns the json info we want to send back to the UI (nuxt) about the user ($this is accessed by the illuminate JsonResource class which just allows for the user properties to be accessed here)
        //ultimately this resource lets us customize exactly what we want to send back to the ui, 
        //we don't always want to send all of the user info back and some of it we want to format differently and in an organized way
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            //only want to get the designs of users when we explictly load them from a controller (e.g. attach the Design model in the controller somewhere like in the eager load criteria ) - because for instance when we update a team and it returns the team info with all of its members from the team resource, we don't really need to see all of the designs for each team member. It is too much unneeded info to the frontend
            'designs' => DesignResource::collection( 
                $this->whenLoaded('designs')
            ),
            'create_dates' => [
                'created_at_human' => $this->created_at->diffForHumans(),
                'created_at' => $this->created_at,
            ],
            'email' => $this->email,
            'formatted_address' => $this->formatted_address,
            'tagline' => $this->tagline,
            'about' => $this->about,
            'location' => $this->location,
            'available_to_hire' => $this->available_to_hire
        ];
    }
}
