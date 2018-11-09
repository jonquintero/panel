<?php

use App\Skill;
use App\User;
use App\Profession;
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

      //  $professionId = Profession::where('title', 'Desarrollador back-end')->value('id');
        $professions = Profession::all();
        $skills = Skill::all();
        $user = factory(User::class)->create([
            'name' => 'Jonathan Quintero',
            'email' => 'jonquintero@hotmail.com',
            'password' => bcrypt('laravel'),
            'role' => 'admin',
            'created_at' => now()->addDay(),
        ]);

        $user->profile()->create([
            'bio' => 'Programador, profesor, editor, escritor, social media manager',
            'profession_id' => $professions->firstWhere('title', 'Desarrollador back-end')->id,
        ]);

        factory(User::class, 999)->create()->each(function ($user) use ($professions, $skills) {

                $randomSkills = $skills->random(rand(0,7));

                $user->skills()->attach($randomSkills);
                factory(\App\UserProfile::class)->create([
                    'user_id' => $user->id,
                    'profession_id' => rand(0,2) ? $professions->random()->id : null,
                ]);

        });
    }
}
