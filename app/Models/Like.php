<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'user_id'
    ];
    //like comments we want to allow this Like functionality to be able to be used by any other models, so we use a polymorphic relationship
    public function likeable()
    {
        return $this->morphTo();
    }
}
