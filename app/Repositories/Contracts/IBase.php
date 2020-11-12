<?php
namespace App\Repositories\Contracts;

interface IBase
{
    public function all();
    //e.g. find the user by id
    public function find($id);
    //e.g. find something like, user where id equals....
    public function findWhere($column, $value); 
    //just like previous but only return the first record that comes up
    public function findWhereFirst();
    //e.g. a find but paginates results
    public function paginate($perPage = 10);
    //takes the data given from frontend and loads it into the DB
    public function create(array $data);
    public function update($id, array $data);
    //deletes the record based on id
    public function delete($id);
}