<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use Likeable;   
    
    protected $fillable = [
        'body',
        'user_id'
    ];

    //polymorphic relationship means we can create one relationship in this class and then reuse it across other classes. So imagine if we wanted to comment on not only designs but other things as well
    public function commentable()
    {
        //can basically belong to more than one model - morph to any other relationship that we want to use
        return $this->morphTo();
    }

    //who is writing this comment
    public function user()
    {
      return $this->belongsTo(User::class);
    }
}
