<?php

namespace Tests\Browser\Admin;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\{Profession, Skill, User};
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateUserTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_user_can_be_created()
    {
        $profession = factory(Profession::class)->create();
        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->browse(function (Browser $browser) use ($profession, $skillA, $skillB) {
            $browser->visit('usuarios/nuevo')
                ->type('first_name', 'Jonathan')
                ->type('last_name', 'Quintero')
                ->type('email', 'jonathan@hotmail.com')
                ->type('password', 'laravel')
                ->type('bio', 'Programador')
                ->select('profession_id', $profession->id)
                ->type('twitter', 'https://twitter.com/jonquintero')
                ->check("skills[{$skillA->id}]")
                ->check("skills[{$skillB->id}]")
                ->radio('role', 'user')
                ->press('Crear usuario')
                ->assertPathIs('/usuarios')
                ->assertSee('Jonathan')
                ->assertSee('jonathan@hotmail.com');
        });

        $this->assertCredentials([
            'first_name' => 'Jonathan',
            'last_name' => 'Quintero',
            'email' => 'jonathan@hotmail.com',
            'password' => 'laravel',
            'role' => 'user',
        ]);

        $user = User::findByEmail('jonathan@hotmail.com');

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador',
            'twitter' => 'https://twitter.com/jonquintero',
            'user_id' => $user->id,
            'profession_id' => $profession->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id,
        ]);
    }
}
