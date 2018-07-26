<?php

namespace Tests\Feature;

use App\Profession;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $profession;

    /** @test */
    function it_shows_the_users_list()
    {
        factory(User::class)->create([
            'name' => 'Joel'
        ]);

        factory(User::class)->create([
            'name' => 'Ellie',
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }

    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->withoutExceptionHandling();
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }
    
    /** @test */
    function it_displays_the_users_details()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'name' => 'Jonathan Quintero'
        ]);

        $this->get("/usuarios/{$user->id}") // usuarios/5
            ->assertStatus(200)
            ->assertSee('Jonathan Quintero');
    }

    /** @test */
    function it_displays_a_404_error_if_the_user_is_not_found()
    {
        $this->withoutExceptionHandling();
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }
    
    /** @test */
    function it_loads_the_new_users_page()
    {
        $this->withoutExceptionHandling();
        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario');
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios/', $this->getValidData())->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.net',
            'password' => '123456',
            'profession_id' => $this->profession->id,
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y PHP',
            'twitter' => 'https://twitter.com/jonquintero',
            'user_id' => User::findByEmail('jquintero@hotmail.net')->id,
        ]);

    }

    /** @test */
    function the_twitter_field_is_optional()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios/', $this->getValidData(['twitter' => null]))->assertRedirect('usuarios');

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
    function the_profession_id_field_is_optional()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios/', $this->getValidData(['profession_id' => '']))->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.net',
            'password' => '123456',
            'profession_id' => null,
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y PHP',

            'user_id' => User::findByEmail('jquintero@hotmail.net')->id,
        ]);

    }

    /** @test */
    function the_profession_must_be_valid()
    {
        $this->handleValidationExceptions();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData(['profession_id' => '10000']))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

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
            ->post('/usuarios/', $this->getValidData(['profession_id' =>  $deletedProfession->id]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_name_is_required()
    {
        $this->withoutExceptionHandling();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData(['name' => '']))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        //$this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_is_required()
    {
        $this->withoutExceptionHandling();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData(['email' => '']))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

       // $this->assertEquals(0, User::count());
       $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->withoutExceptionHandling();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/',$this->getValidData(['email' => 'correo-no-valido']))
            ->assertRedirect('usuarios/nuevo')
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

        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData(['email' => 'jquintero@hotmail.com']))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {
        $this->withoutExceptionHandling();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData(['password' => '']))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);

      //  $this->assertEquals(0, User::count());
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function it_loads_the_edit_user_page()
    {
        $this->withoutExceptionHandling();

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
        $user = factory(User::class)->create();

        $this->withoutExceptionHandling();

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
    function the_name_is_required_when_updating_the_user()
    {
        $this->withoutExceptionHandling();
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
    function the_email_must_be_valid_when_updating_the_user()
    {
        $this->withoutExceptionHandling();
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
    function the_email_must_be_unique_when_updating_the_user()
    {
        $this->withoutExceptionHandling();

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
    function the_users_email_can_stay_the_same_when_updating_the_user()
    {
        $this->withoutExceptionHandling();
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
    function the_password_is_optional_when_updating_the_user()
    {
        $this->withoutExceptionHandling();
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

    /** @test */
    function it_deletes_a_user()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->delete("usuarios/{$user->id}")
            ->assertRedirect('usuarios');

        $this->assertDatabaseMissing('users', [
           'id' => $user->id
        ]);

        // Or:
        //$this->assertSame(0, User::count());
    }

    /**
     * @return array
     */
    protected function getValidData(array $custom = [])
    {
        $this->profession = factory(Profession::class)->create();
        return array_filter(array_merge([
            'name' => 'Jonathan',
            'email' => 'jquintero@hotmail.net',
            'password' => '123456',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador de Laravel y PHP',
            'twitter' => 'https://twitter.com/jonquintero',
        ], $custom));
    }
}












