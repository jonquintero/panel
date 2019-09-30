<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 18/12/2018
 * Time: 6:27 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QueryBuilder extends Builder
{
    public function whereQuery($subquery, $operator, $value = null)
    {
        $this->addBinding($subquery->getBindings());
        $this->where(DB::raw("({$subquery->toSql()})"), $operator, $value);
    }
}