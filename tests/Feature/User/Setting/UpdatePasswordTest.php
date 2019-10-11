<?php

namespace Tests\Feature\User\Setting;

use Tests\TestCase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_current_password_should_match()
    {
    	Passport::actingAs($this->user);

        $this->json('PATCH', route('password.update'), [
                'password_current' => 'abc',
                'password' => 'password1',
                'password_confirmation' => 'password1'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'password_current'
                ]
            ]);
    }

    /** @test */
    public function it_requires_password_and_password_confirmation_do_match()
    {
        Passport::actingAs($this->user);

        $this->json('PATCH', route('password.update'), [
                'password_current' => 'password',
                'password' => 'password1',
                'password_confirmation' => 'password2'
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'password'
                ]
            ]);
    }

    /** @test */
    public function it_can_update_password()
    {
        Passport::actingAs($this->user);

        $this->json('PATCH', route('password.update'), [
                'password_current' => 'password',
                'password' => 'password1',
                'password_confirmation' => 'password1'
            ])
            ->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email'
                ]
            ]);
    }
}
