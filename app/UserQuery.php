<?php
/**
 * Created by PhpStorm.
 * User: JONQUINTERO
 * Date: 5/12/2018
 * Time: 1:55 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class UserQuery extends Builder
{
    public function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }

    public function filterBy(array $filters)
    {
        $rules = [
            'search' => 'filled',
            'status' => 'in:active,inactive',
            'role' => 'in:admin,user',
        ];

        $validator = Validator::make($filters, $rules);
        foreach ($validator as $name => $value){
            $this->{'filterBy'.Str::studly($name)}($value);
        }
        $this->byState(request($filters['state']))
        ->byRole(request($filters['role']))
        ->search(request($filters['search']));

        return $this;
    }

    public function filterBySearch($search)
    {
        if (empty($search)){
            return $this;
        }
        /*$query->when(request('team'), function ($query, $team){
        if ($team === 'with_team'){
            $query->has('team');
        }elseif ($team === 'without_team'){
            $query->doesntHave('team');
        }
    })*/

        // $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', "%{$search}%")
        return $this->whereRaw('CONCAT(first_name, " ", last_name) like ?', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search){
                $query->where('name',  'like', "%{$search}%");
            });
    }
    public function filterByState($state)
    {
        if ($state == 'active'){
            return $this->where('active', true);
        }

        if ($state == 'inactive'){
            return $this->where('active', false);
        }
        return $this;
    }

    public function filterByRole($role)
    {
        if(in_array($role, ['admin', 'role'])){
            // if(! empty($role)){
            $this->where('role', $role);
        }
        return $this;
    }
}