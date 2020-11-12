<?php

namespace App\Repositories\Criteria;

interface ICriteria
{
    //accepts an array or list of criteria that will be used to filter a query
    public function withCriteria(...$criteria);
}