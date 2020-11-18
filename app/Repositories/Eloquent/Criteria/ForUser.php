<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\ICriterion;

class ForUser implements ICriterion
{
    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }
    //the chain events to get here is this: 1. the DesignController uses the IDesign contract to pull in the BaseRepository (they are connected through a service provider) 2. The BaseRepository implements an ICriteria contract which has a withCriteria method 3. The withCriteria method takes an array of classes (ForUser is one) which implement the ICriterion contract 4. Each class/criterion applys some unique filter to the model that is being accessed (like grab Designs but only ones that belong to a certain user) 5. DesignController uses the filter along with a query (like all()) to return the instructed data from the DB
    public function apply($model)
    {
        return $model->where('user_id', $this->user_id);
    }
}