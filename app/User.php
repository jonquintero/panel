<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        //
    ];

    public static function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }

    public function team()
    {
        return $this->belongsTo(Team::class)->withDefault();
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function scopeSearch($query, $search)
    {
        if (empty($search)){
            return;
        }
        /*$query->when(request('team'), function ($query, $team){
        if ($team === 'with_team'){
            $query->has('team');
        }elseif ($team === 'without_team'){
            $query->doesntHave('team');
        }
    })*/

        // $query->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', "%{$search}%")
        $query->whereRaw('CONCAT(first_name, " ", last_name) like ?', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhereHas('team', function ($query) use ($search){
                $query->where('name',  'like', "%{$search}%");
            });



    }

    public function scopeByState($query, $state)
    {
        if ($state == 'active'){
            return $query->where('active', true);
        }

        if ($state == 'inactive'){
            return $query->where('active', false);
        }
    }

    public function getNameAttribute()
    {
        return strtoupper("{$this->first_name} {$this->last_name}");
    }

    public function setStateAttribute($value)
    {
        //$this->active = $value == 'active';
        $this->attributes['active'] = $value == 'active';
    }

    public function getStateAttribute()
    {
        if ($this->active !== null){
            return $this->active ? 'active' : 'inactive';
        }

    }
}
