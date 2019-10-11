<?php

namespace Tests\Feature\User\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\User\Auth\EmailVerificationNotification;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_name_valid_email_password_and_password_confirmation()
    {
        $this->json('POST', route('register'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password'
                ]
            ]);
    }

    /** @test */
    public function it_requires_a_unique_email()
    {
        $data = [
            'email' => $this->user->email,
        ];

        $this->json('POST', route('register'), $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
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

        $this->json('POST', route('register'), $data)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'password'
                ]
            ]);
    }

    /** @test */
    public function it_returns_a_verification_email_on_registration()
    {
        Notification::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        Notification::assertNothingSent();

        $this->json('POST', route('register'), $data)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'message'
            ]);

        $user = User::where('email', $data['email'])->first();

        $this->assertDatabaseHas('users', [
            'email' => $user->email
        ]);

        Notification::assertSentTo(
            [$user],
            EmailVerificationNotification::class,
            function ($notification, $channels) use ($user) {
                return $notification->user->is($user);
            }
        );
    }
}
