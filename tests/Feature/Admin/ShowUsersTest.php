<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_displays_the_users_details()
    {
       // $this->withoutExceptionHandling();
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
        $this->withExceptionHandling();
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }
}
