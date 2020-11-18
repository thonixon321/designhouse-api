<?php
namespace App\Repositories\Eloquent;

use App\Models\Comment;
use App\Repositories\Contracts\IComment;
use App\Repositories\Eloquent\BaseRepository;


class CommentRepository extends BaseRepository implements IComment
{
    //since this is implementing the IComment interface, we have to provide a concrete implementation of all the methods inside the IComment interface

    //we want to use the base repository methods which holds all the common methods used on models and to do that we dynamically send this model name to that BaseRepository class
    public function model()
    {
        return Comment::class;
    }
}