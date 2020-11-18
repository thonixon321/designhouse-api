<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'slug'
    ];

    public function owner()
    {
        //have to specify 'owner_id' since this isn't in the user_id naming convention
        return $this->belongsTo(User::class, 'owner_id');
    }

    //this is where the intermediate table comes in to connect a many to many relationship between users and teams
    public function members()
    {
        //with timestamps so we can see when a user is added or updated to a team
        return $this->belongsToMany(User::class)
                    ->withTimestamps();
    }
    //user will be able to add a design to a team
    public function designs()
    {
        return $this->hasMany(Design::class);
    }
    //check if team has a particular user
    public function hasUser(User $user)
    {
        //return true if user was found in members, otherwise return false
        return $this->members()
                    ->where('user_id', $user->id)
                    ->first() ? true : false;
    }
}
