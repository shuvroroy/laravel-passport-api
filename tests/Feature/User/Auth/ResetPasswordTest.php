<?php

namespace Tests\Feature\User\Auth;

use Tests\TestCase;
use App\Models\PasswordReset;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_password_and_password_confirmation_and_a_valid_token_associate_with_that_email()
    {
        $data = [
            'token' => 'invalid',
            'email' => 'invalid@example.com',
        ];

        $this->json('POST', route('password.reset'), $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email',
                    'password'
                ]
            ]);
    }

    /** @test */
    public function it_requires_password_and_password_confirmation_do_match()
    {
        $data = [
            'password' => 'password1',
            'password_confirmation' => 'password2',
        ];

        $this->json('POST', route('password.reset'), $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'password'
                ]
            ]);
    }

    /** @test */
    public function it_returns_a_message_if_all_info_is_correct()
    {
        Notification::fake();

        $data = [
            'email' => $this->user->email
        ];

        Notification::assertNothingSent();

        $this->json('POST', route('password.email'), $data)
            ->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'message'
            ]);

        $token = PasswordReset::where('email', $this->user->email)->first()->token;

        $updateData = [
            'token' => $token,
            'email' => $this->user->email,
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ];

        $this->json('POST', route('password.reset'), $updateData)
            ->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                'message'
            ]);
    }
}
