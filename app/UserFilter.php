<?php
/**
 * Created by PhpStorm.
 * User: JONQUINTERO
 * Date: 13/12/2018
 * Time: 11:07 AM
 */

namespace App;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserFilter extends QueryFilter
{
    public function rules(): array
    {
       return [
           'search' => 'filled',
           'state' => 'in:active,inactive',
           'role' => 'in:admin,user',
           'skills' => 'array|exists:skills,id',
           'from' => 'date_format:d/m/Y',
           'to' => 'date_format:d/m/Y',
       ];
    }

    public function search($query, $search)
    {
        /*$query->when(request('team'), function ($query, $team){
        if ($team === 'with_team'){
            $query->has('team');
        }elseif ($team === 'without_team'){
            $query->doesntHave('team');
        }
    })*/

        // $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', "%{$search}%")
        return $query->where(function ($query) use ($search) {
            $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('team', function ($query) use ($search){
                    $query->where('name',  'like', "%{$search}%");
                });
        });

    }

    public function state($query, $state)
    {
        return $query->where('active', $state == 'active');
    }

    public function skills($query, $skills)
    {
        $subquery = DB::table('user_skill AS s')
            ->selectRaw('COUNT(`s`.`id`)')
            ->whereColumn('s.user_id', 'users.id')
            ->whereIn('skill_id', $skills);

        $query->whereQuery($subquery, count($skills));
    }
    public function from ($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);

        $query->whereDate('created_at','>=', $date);
    }

    public function to ($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);

        $query->whereDate('created_at','<=', $date);
    }
}