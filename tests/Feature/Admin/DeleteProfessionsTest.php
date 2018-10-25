<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteProfessionsTest extends TestCase
{
    use RefreshDatabase;
   /** @test */

   function it_deletes_a_profession()
   {
       $profession = factory(Profession::class)->create();

       $reponse = $this->delete("profesiones/{$profession->id}");
       $reponse->assertRedirect();

       $this->assertDatabaseEmpty('professions');
   }

    /** @test */

    function a_profession_associated_to_a_profile_cannot_be_deleted()
    {
        $this->withExceptionHandling();
        $profession = factory(Profession::class)->create();

        $profile = factory(UserProfile::class)->create([
            'profession_id' => $profession->id,
        ]);

        $reponse = $this->delete("profesiones/{$profession->id}");
        $reponse->assertStatus(400);

        $this->assertDatabaseHas('professions',[
            'id' => $profession->id,
        ]);
    }
}
