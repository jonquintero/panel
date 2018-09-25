<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\Skill;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUsersTest extends TestCase
{
    use RefreshDatabase;
    protected $defaultData = [
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.net',
            'password' => '123456',
            'profession_id' => '',
            'bio' => 'Programador de Laravel y PHP',
            'twitter' => 'https://twitter.com/jonquintero',
            'role' => 'user',
        ];

    /** @test */
    function it_loads_the_new_users_page()
    {
    //    $this->withoutExceptionHandling();
        $profession = factory(Profession::class)->create();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();


        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions', function ($professions) use ($profession){
                return $professions->contains($profession);
            })
            ->assertViewHas('skills', function ($skills) use ($skillA, $skillB) {
                return $skills->contains($skillA) && $skills->contains($skillB);
            });

    }

    /** @test */
    function it_creates_a_new_user()
    {
      //  $this->withoutExceptionHandling();
        $profession = factory(Profession::class)->create();
        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $skillC = factory(Skill::class)->create();
        $this->post('/usuarios/', $this->withData([
            'skills' => [$skillA->id,$skillB->id],
            'profession_id' => $profession->id,
        ]))->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.net',
            'password' => '123456',
            'role' => 'user',

        ]);

        $user =  User::findByEmail('jquintero@hotmail.net');

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y PHP',
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

        $this->assertDatabaseMissing('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillC->id,
        ]);

    }

    /** @test */
    function the_twitter_field_is_optional()
    {
        //$this->withoutExceptionHandling();

        $this->post('/usuarios/', $this->withData(['twitter' => null]))->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.net',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y PHP',
            'twitter' => null,
            'user_id' => User::findByEmail('jquintero@hotmail.net')->id,
        ]);

    }

    /** @test */
    function the_role_field_is_optional()
    {
       // $this->withoutExceptionHandling();

        $this->post('/usuarios/', $this->withData(['role' => null]))->assertRedirect('usuarios');

        $this->assertDatabaseHas('users',[

            'email' => 'jquintero@hotmail.net',
            'role' => 'user',
        ]);

    }
    /** @test */
    function the_role_must_be_valid()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios/', $this->withData(['role' => 'invitado']))

            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_profession_id_field_is_optional()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios/', $this->withData(['profession_id' => '']))
            ->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.net',
            'password' => '123456',

        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y PHP',

            'user_id' => User::findByEmail('jquintero@hotmail.net')->id,
            'profession_id' => null,
        ]);

    }

    /** @test */
    function the_profession_must_be_valid()
    {
        $this->handleValidationExceptions();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->withData(['profession_id' => '10000']))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_an_array()
    {
        $this->handleValidationExceptions();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->withData([
                'skills' => 'PHP, JS'
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['skills']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function the_skills_must_be_valid()
    {
        $this->handleValidationExceptions();
        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->withData([
                'skills' => [$skillA->id, $skillB->id + 1]
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['skills']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function only_selectable_profession_are_valid()
    {
        $deletedProfession = factory(Profession::class)->create([
            'deleted_at' => now()->format('Y-m-d'),
        ]);
        $this->handleValidationExceptions();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->withData(['profession_id' =>  $deletedProfession->id]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function the_user_is_redirected_to_the_previous_page_when_validation_fail()
    {
        $this->withoutExceptionHandling();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [])
            ->assertRedirect('usuarios/nuevo');

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function the_name_is_required()
    {
        $this->withoutExceptionHandling();
        $this->post('/usuarios/', $this->withData(['name' => '']))
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_is_required()
    {
        $this->withoutExceptionHandling();
        $this->post('/usuarios/', $this->withData(['email' => '']))
              ->assertSessionHasErrors(['email']);

        // $this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->withoutExceptionHandling();
        $this->post('/usuarios/',$this->withData(['email' => 'correo-no-valido']))
             ->assertSessionHasErrors(['email']);

        //  $this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->withoutExceptionHandling();
        factory(User::class)->create([
            'email' => 'jquintero@hotmail.com'
        ]);

        $this->post('/usuarios/', $this->withData(['email' => 'jquintero@hotmail.com']))
             ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {
    //    $this->withoutExceptionHandling();
        $this->post('/usuarios/', $this->withData(['password' => '']))
             ->assertSessionHasErrors(['password']);

        //  $this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }


}
