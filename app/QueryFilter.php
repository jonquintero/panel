<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 10/12/2018
 * Time: 7:54 AM
 */

namespace App;


use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    protected $valid;
    abstract public function rules();

    public function applyTo($query, array $filters)
    {
        $rules = $this->rules();

        $validator = Validator::make(array_intersect_key($filters, $rules), $rules);
        $this->valid = $validator->valid();
        foreach ( $this->valid as $name => $value){

            $this->applyFilter($query,$name, $value);

        }
        /* $this->byState(request($filters['state']))
         ->byRole(request($filters['role']))
         ->search(request($filters['search']));*/

        return $query;
    }

    protected function applyFilter($query,$name, $value): void
    {
        $method = 'filterBy' . Str::studly($name);
        if (method_exists($this, $method)) {
            $this->$method($query, $value);
        }else{
            $query->where($name, $value);
        }
    }

    public function valid()
    {
        return $this->valid();
    }
}