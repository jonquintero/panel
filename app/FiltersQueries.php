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

trait FiltersQueries
{
    public function filterBy(array $filters)
    {
        $rules = $this->filterRules();

        $validator = Validator::make(array_intersect_key($filters, $rules), $rules);
        foreach ($validator->valid() as $name => $value){

            $this->applyFilter($name, $value);

        }
        /* $this->byState(request($filters['state']))
         ->byRole(request($filters['role']))
         ->search(request($filters['search']));*/

        return $this;
    }

    protected function applyFilter($name, $value): void
    {
        $method = 'filterBy' . Str::studly($name);
        if (method_exists($this, $method)) {
            $this->$method($value);
        }else{
            $this->where($name, $value);
        }
    }
}