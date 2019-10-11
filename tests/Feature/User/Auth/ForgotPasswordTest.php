<?php

namespace Tests\Feature\User\Auth;

use Tests\TestCase;
use App\Models\PasswordReset;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\User\Auth\PasswordResetNotification;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_a_valid_existing_email()
    {
        $data = [
            'email' => 'john@example.com'
        ];

        $this->json('POST', route('password.email'), $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email',
                ]
            ]);
    }

    /** @test */
    public function it_sends_a_email_with_a_token_and_insert_into_db_if_email_existing()
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

        Notification::assertSentTo(
            [$this->user],
            PasswordResetNotification::class,
            function ($notification, $channels) use ($token) {
                return $notification->token === $token;
            }
        );
    }
}
