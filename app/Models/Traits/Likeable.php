<?php

namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{   
    //boot is used in laravel to override things and create logic when a certain action happens on a model (like delete in this case), here we have to append Likeable to it since we are in a trait. This will let it call the parent boot method whichever model this trait is being used.   
    public static function bootLikeable()
    {
        //remove any likes attributed to the model that is being deleted
        static::deleting(function($model){
            $model->removeLikes();
        });
    }

    //delete the likes from the model when model is being deleted
    public function removeLikes()
    {
        if ($this->likes()->count()) {
            $this->likes()->delete();
        }
    }

    public function likes()
    {
        //the relationship with the model (say Design or Comment) that is using this trait
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        //if user is not authenticated
        if (! auth()->check()) {
            return;
        }

        //check if current user has already liked the model
        if ($this->isLikedByUser(auth()->id())) {
            return;
        }

        //made it past the checks so just create the like in the DB for the model using this like trait
        $this->likes()->create(['user_id' => auth()->id()]);
    }

    public function unlike()
    {
         //if user is not authenticated
         if (! auth()->check()) {
            return;
         }
        //check if current user has already liked the model
        if (! $this->isLikedByUser(auth()->id())) {
            return;
        }

        $this->likes()
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function isLikedByUser($user_id)
    {
        //look through the likes for the model (say Design) and see
        //if any of them have the same user id as the current authenticated users id - returns a boolean true or false for if a count was found or not
        return (bool)$this->likes()
                    ->where('user_id', $user_id)
                    ->count();
    }
}