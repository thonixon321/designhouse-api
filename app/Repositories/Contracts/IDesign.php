<?php
//Interface for the designs

namespace App\Repositories\Contracts;
//interface simply lists all the methods that you have to implement in your repository for a controller (DesignController here), it works for flexibility since we are not accessing the database directly and can therefore have the ability to switch to a different type of DB (Mongo instead of SQL) down the road if we wanted - this is where the controller actually binds to, then this contract gets used by the DesignRepository found in the Repositories/Eloquent folder
interface IDesign 
{
    public function applyTags($id, array $data);
}