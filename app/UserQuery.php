<?php
/**
 * Created by PhpStorm.
 * User: JONQUINTERO
 * Date: 5/12/2018
 * Time: 1:55 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Builder;

class UserQuery extends QueryBuilder
{




    public function findByEmail($email)
    {
        return$this->where(compact('email'))->first();
    }


   /* public function filterByRole($role)
    {
        if(in_array($role, ['admin', 'role'])){
            // if(! empty($role)){
            $this->where('role', $role);
        }
        return $this;
    }*/




}