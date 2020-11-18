<?php
namespace App\Repositories\Eloquent;

use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\BaseRepository;

class DesignRepository extends BaseRepository implements IDesign
{
    //since this is implementing the IDesign interface, we have to provide a concrete implementation of all the methods inside the IDesign interface

   //we want to use the base repository methods which holds all the common methods used on models and to do that we dynamically send this model name to that BaseRepository class
   public function model()
   {
       return Design::class; //returns the namespace string of the model (not new Design::class; instead it is 'App\Models\Design')
   }

   //extracting out database operations from controllers is needed for more modular code, and here instead of putting a method in the BaseRepository, we set it here in the design repository since it is specific to designs.
   public function applyTags($id, array $data)
   {
      $design = $this->find($id);
      $design->retag($data);
   }

   public function addComment($designId, array $data)
   {
       //get the design for which we want to create a comment
       $design = $this->find($designId);

       //create the comment for the design
       $comment = $design->comments()->create($data);

       return $comment;
   }

   public function like($id)
   {
       //get the design for which we want to create/uncreate like
       $design = $this->model->findOrFail($id);
       if ($design->isLikedByUser(auth()->id())) {
           $design->unlike();
       } else {
           $design->like();
       }
   }
   //this has the same name as a method in the likeable trait
   public function isLikedByUser($id)
   {
        $design = $this->model->findOrFail($id);
        //see if the design found is liked by current user
        return $design->isLikedByUser(auth()->id());
   }
}