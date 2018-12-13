<?php
/**
 * Created by PhpStorm.
 * User: JONQUINTERO
 * Date: 5/12/2018
 * Time: 1:55 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Builder;

class UserQuery extends Builder
{
    use FiltersQueries;

    public function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }

    protected function filterRules(): array
    {
        return [
            'search' => 'filled',
            'state' => 'in:active,inactive',
            'role' => 'in:admin,user',
        ];

    }



    public function filterBySearch($search)
    {
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

        return $this->where('active', $state == 'active');
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