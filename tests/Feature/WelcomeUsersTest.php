<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WelcomeUsersTest extends TestCase
{
    /** @test */
    function it_welcomes_users_with_nickname()
    {
        $this->get('saludo/jonathan/jon')
            ->assertStatus(200)
            ->assertSee('Bienvenido Jonathan, tu apodo es jon');
    }
    
    /** @test */
    function it_welcomes_users_without_nickname()
    {
        $this->get('saludo/jonathan')
            ->assertStatus(200)
            ->assertSee('Bienvenido Jonathan');
    }
}
