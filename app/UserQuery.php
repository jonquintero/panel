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
    public function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }

    public function filterBy(array $filters)
    {
        $this->byState(request('state'))
        ->byRole(request('role'))
        ->search(request('search'));
    }

    public function search($search)
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
    public function byState($state)
    {
        if ($state == 'active'){
            return $this->where('active', true);
        }

        if ($state == 'inactive'){
            return $this->where('active', false);
        }
        return $this;
    }

    public function byRole($role)
    {
        if(in_array($role, ['admin', 'role'])){
            // if(! empty($role)){
            $this->where('role', $role);
        }
        return $this;
    }
}