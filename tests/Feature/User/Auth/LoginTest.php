<?php

namespace Tests\Feature\User\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_email_and_password()
    {
        $this->json('POST', route('login'))
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email',
                    'password'
                ]
            ]);
    }

    /** @test */
    public function it_returns_a_validation_error_with_invalid_email_and_password()
    {
        $data = [
            'email' => $this->user->email,
            'password' => 'invalid'
        ];

        $this->json('POST', route('login'), $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
                ]
            ]);
    }

    /** @test */
    public function it_returns_message_if_email_is_not_verified_on_login()
    {
        $user = factory(User::class)->create();

        $data = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $this->json('POST', route('login'), $data)
            ->assertStatus(403)
            ->assertJsonStructure([
                'message'
            ]);
    }

    /** @test */
    public function it_returns_user_and_token_if_email_is_verified_on_login()
    {
        $data = [
            'email' => $this->user->email,
            'password' => 'password'
        ];

        Artisan::call('passport:install');

        $this->json('POST', route('login'), $data)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email'
                ],
                'meta' => [
                    'token',
                    'token_type',
                    'expires_in'
                ]
            ]);
    }

    /** @test */
    public function it_requires_valid_token_to_logout()
    {
        Passport::actingAs($this->user);

        $this->json('POST', route('logout'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
    }
}
