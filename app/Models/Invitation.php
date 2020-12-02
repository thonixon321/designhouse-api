<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'recipient_email',
        'sender_id',
        'team_id',
        'token'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function recipient()
    {
        //foreign key on the user table will be the email, and local key will be recipient email - have to explicitly state these relations here since laravel will look for user_id instead of email, we use email since we can send the email to someone not even in signed up for designhouse and can find them by email instead of id easier, the emails are unique anyway
        return $this->hasOne(User::class, 'email', 'recipient_email');
    }

    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }
}
