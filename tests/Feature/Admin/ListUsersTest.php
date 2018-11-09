<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListUsersTest extends TestCase
{
    use RefreshDatabase;

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
    function it_paginates_the_users()
    {
        factory(User::class)->times(12)->create([
            'create_at' => now()->subDays(4),
        ]);

        factory(User::class)->create([
            'name' => 'Decimosexto Usuario',
            'create_at' => now()->subDays(3),
        ]);

        factory(User::class)->create([
            'name' => 'Decimoseptimo Usuario',
            'create_at' => now()->subDays(2),
        ]);

        factory(User::class)->create([
            'name' => 'Primer Usuario',
            'create_at' => now()->subWeek(),
        ]);

        factory(User::class)->create([
            'name' => 'Segundo Usuario',
            'create_at' => now()->subDays(5),
        ]);

        factory(User::class)->create([
            'name' => 'Tercer Usuario',
            'create_at' => now()->subDays(6),
        ]);



        $this->get('/usuarios')
            ->assertStatus(200)

            ->assertSeeInOrder([
                'Decimoseptimo Usuario',
                'Decimosexto Usuario',
                'Tercer Usuario'])
            ->assertDontSee('Segundo Usuario')
            ->assertDontSee('Primer Usuario');

        $this->get('/usuarios?page=2')
            ->assertSeeInOrder([
                'Segundo Usuario',
                'Primer Usuario',
            ])
            ->assertDontSee('Tercer Usuario');
    }

    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    function it_shows_the_deleted_users()
    {
        factory(User::class)->create([
            'name' => 'Joel',
            'deleted_at' => now(),
        ]);

        factory(User::class)->create([
            'name' => 'Ellie',
        ]);

        $this->get('/usuarios/papelera')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios en papelera')
            ->assertSee('Joel')
            ->assertDontSee('Ellie');
    }
}
