<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Arr;
use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\IBase;
use App\Repositories\Criteria\ICriteria;

//abstract class means you cannot instantiate it on its own - it will be extended by the other repository classes (this is going to hold all the base methods to be shared across repositories as well as filters for those with criteria)
abstract class BaseRepository implements IBase, ICriteria
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    public function all()
    {
        return $this->model->get();
    }

    //e.g. find the user by id
    public function find($id) {
         //findOrFail means laravel will return a 404 error if it does not find the id passed in here
        $result = $this->model->findOrFail($id);
        return $result;
    }
    //e.g. find something like, user where id equals....
    public function findWhere($column, $value) {
        return $this->model->where($column, $value)->get();
    } 
    //just like previous but only return the first record that comes up
    public function findWhereFirst() {
        return $this->model->where($column, $value)->firstOrFail();
    }
    //e.g. a find but paginates results
    public function paginate($perPage = 10) {
        return $this->model->paginate($perPage);
    }
    //takes the data given from frontend and loads it into the DB
    public function create(array $data) {
        return $this->model->create($data);
    }
    public function update($id, array $data) {
        //reuse method from above to get the record to update
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }
    //deletes the record based on id
    public function delete($id) {
        $record = $this->find($id);
        return $record->delete();
    }
    //adds filters to the above queries using the arrays passed in (like give me all designs but make them from most recent to oldest or only the ones that are live, etc.)
    public function withCriteria(...$criteria) {
        //make sure nested arrays flatten to a single array
        $criteria = Arr::flatten($criteria);
        
        foreach($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }

    //since the other repositories are extending this class, we have access to the model method they are using to pass in their particular model they are using 
    protected function getModelClass()
    {
        //check that the model method exists
        if ( !method_exists($this, 'model') ) {
            throw new ModelNotDefined();
        }
        //if it does then return the namespace of the model accessing this BaseRepository
        return app()->make($this->model());
    }
}