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

    protected static function boot()
    {
        //have to initialize the boot method since we are extending a Model
        parent::boot();

        //when team is created, add current user as team member (to intermediate table Team_User)
        static::created(function($team){
            //using the team relation in the user model to attach to intermediate table: 
            // auth()->user()->teams()->attach($team->id);
            //using the members relation in this model to attach to intermediate table:
            $team->members()->attach(auth()->id());
        });

        //when team is deleted, delete team records from intermediate table Team_User
        static::deleting(function($team){
            //sync with empty array will replace all the target team members with the empty array(deleted them)
            $team->members()->sync([]);
        });
    }

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

    //relation to invitations
    public function invitation()
    {
        return $this->hasMany(Invitation::class);
    }

    //pending invitation for particular email given?
    public function hasPendingInvite($email)
    {
        return (bool)$this->invitation()
                          ->where('recipient_email', $email)
                          ->count();
    }
}
