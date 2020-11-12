<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\ICriterion;

class EagerLoad implements ICriterion
{
    protected $relationships;

    public function __construct($relationships)
    {
        $this->relationships = $relationships;
    }
    //the chain events to get here is this: 1. the DesignController uses the IDesign contract to pull in the BaseRepository 2. The BaseRepository implements an ICriteria contract which has a withCriteria method 3. The withCriteria method takes an array of classes (ForUser is one) which implement the ICriterion contract 4. Each class/criterion applys some unique filter to the model that is being accessed (like grab Designs but only ones that belong to a certain user) 5. DesignController uses the filter along with a query (like all()) to return the instructed data from the DB
    public function apply($model)
    {
        //this eager loads a model with another related model (like the users DB table with designs DB table so that all the info is available from both tables without having to make multiple queries)
        return $model->with($this->relationships);
    }
}