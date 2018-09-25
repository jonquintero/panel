<?php

use App\User;
use App\Profession;
use App\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$professions = DB::select('SELECT id FROM professions WHERE title = ?', ['Desarrollador back-end']);

        $professionId = Profession::where('title', 'Desarrollador back-end')->value('id');

        $user = factory(User::class)->create([
            'name' => 'Jonathan Quintero',
            'email' => 'jonquintero@hotmail.com',
            'password' => bcrypt('laravel'),
           // 'profession_id' => $professionId,
            'role' => 'admin',
        ]);

        $user->profile()->create([
            'bio' => 'Programador, escritor, editor, social media manager',
            'profession_id' => $professionId,
        ]);

    /*    factory(User::class)->create([
            'profession_id' => $professionId
        ]);*/

        factory(User::class,29) ->create()->each(function ($user) {
            $user->profile()->create(
                factory(UserProfile::class)->raw()
            );
        });
    }
}
