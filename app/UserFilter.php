<?php
/**
 * Created by PhpStorm.
 * User: JONQUINTERO
 * Date: 13/12/2018
 * Time: 11:07 AM
 */

namespace App;


class UserFilter extends QueryFilter
{
    public function rules(): array
    {
       return [
           'search' => 'filled',
           'state' => 'in:active,inactive',
           'role' => 'in:admin,user',
       ];
    }

    public function filterBySearch($query, $search)
    {
        /*$query->when(request('team'), function ($query, $team){
        if ($team === 'with_team'){
            $query->has('team');
        }elseif ($team === 'without_team'){
            $query->doesntHave('team');
        }
    })*/

        // $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', "%{$search}%")
        return $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search){
                $query->where('name',  'like', "%{$search}%");
            });
    }

    public function filterByState($query, $state)
    {

        return $query->where('active', $state == 'active');
    }
}