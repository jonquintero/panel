<?php

namespace Tests\Feature;

use App\Profession;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListProfessionsTest extends TestCase
{
    /** @test */

    function it_shows_the_users_lists()
    {
        factory(Profession::class)->create(['title' => 'Disenador']);

        factory(Profession::class)->create(['title' => 'Programador']);

        factory(Profession::class)->create(['title' => 'Administrador']);

        $this->get('/profesiones')
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Administrador',
                'Disenador',
                'Programador',
            ]);
    }
}
