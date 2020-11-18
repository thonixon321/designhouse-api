<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

//the mustVerifyEmail is enabled, so if the user has not verified their email (not null in that email_verified_at column in the DB) then they won't be able to log in
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'tagline', 'about', 'username', 'location', 'available_to_hire', 'formatted_address'
    ];

    protected $spatialFields = [
        'location',
        'area'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    //a token will be stored on the backend for the user trying to reset their password, it is attached to the email link we send to them (verifies that the user who requested the password reset is the right person since token will be in the email)
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    //teams user belongs to
    public function teams()
    {
        return $this->belongsToMany(Team::class)
                    ->withTimestamps();
    }
    //teams user created
    public function ownedTeams()
    {
        return $this->teams()
                    ->where('owner_id', $this->id);
    }
    //check if user is owner of team
    public function isOwnerOfTeam($team)
    {
        return (bool)$this->teams()
                    ->where('id', $team->id)
                    ->where('owner_id', $this->id)
                    ->count();
    }
}
