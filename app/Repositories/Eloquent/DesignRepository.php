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
}