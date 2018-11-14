<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    //protected $table = 'users';
    use SoftDeletes;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        //
    ];

    protected $perPage = 15;

    public static function findByEmail($email)
    {
        return static::where(compact('email'))->first();
    }


    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class)->withDefault();
    }
    public function team()
    {
        return $this->belongsTo(Team::class)->withDefault();
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


                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('team', function ($query) use ($search){
                        $query->where('name',  'like', "%{$search}%");
                    });



    }
}
