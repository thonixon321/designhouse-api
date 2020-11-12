<?php

namespace App\Repositories\Criteria;

interface ICriterion
{
    //accepts model to apply query filter to
    public function apply($model);
}