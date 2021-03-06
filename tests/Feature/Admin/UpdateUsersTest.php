<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
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
    function it_loads_the_edit_user_page()
    {
     //   $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->get("/usuarios/{$user->id}/editar") // usuarios/5/editar
        ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editar usuario')
            ->assertViewHas('user', function ($viewUser) use ($user) {
                return $viewUser->id === $user->id;
            });
    }

    /** @test */
    function it_updates_a_user()
    {
      //  $this->handleValidationExceptions();
        $user = factory(User::class)->create();

        //$this->withoutExceptionHandling();

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.com',
            'password' => '123456'
        ])->assertRedirect("/usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.com',
            'password' => '123456',
        ]);
    }
    /** @test */
    function the_name_is_required()
    {
        $this->handleValidationExceptions();
        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => '',
                'email' => 'jquintero@hotmail.com',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email' => 'jquintero@hotmail.com']);
    }

    /** @test */
    function the_email_must_be_valid()
    {
     //   $this->withoutExceptionHandling();
        $this->handleValidationExceptions();
        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Jonathan Quintero',
                'email' => 'correo-no-valido',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name' => 'Jonathan Quintero']);
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email' => 'existing-email@example.com',
        ]);

        $user = factory(User::class)->create([
            'email' => 'jquintero@hotmail.com'
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Jonathan',
                'email' => 'existing-email@example.com',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        //
    }

    /** @test */
    function the_users_email_can_stay_the_same()
    {
        $this->handleValidationExceptions();
        $user = factory(User::class)->create([
            'email' => 'jquintero@hotmail.com'
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Jonathan Quintero',
                'email' => 'jquintero@hotmail.com',
                'password' => '12345678'
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)

        $this->assertDatabaseHas('users', [
            'name' => 'Jonathan Quintero',
            'email' => 'jquintero@hotmail.com',
        ]);
    }

    /** @test */
    function the_password_is_optional()
    {
        $this->handleValidationExceptions();
        $oldPassword = 'CLAVE_ANTERIOR';

        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword)
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Jonathan',
                'email' => 'jquintero@hotmail.com',
                'password' => ''
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)

        $this->assertCredentials([
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.com',
            'password' => $oldPassword // VERY IMPORTANT!
        ]);
    }

}
