<?php

namespace Tests\Feature\User\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\User\Auth\EmailVerificationNotification;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_verify_email()
    {
        Notification::fake();

        $user = factory(User::class)->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['user' => $user->id]);

        Notification::assertNothingSent();

        $this->json('POST', $url)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    /** @test */
    public function it_cannot_verify_email_if_already_verified()
    {
        Notification::fake();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['user' => $this->user->id]);

        $this->json('POST', $url)
            ->assertStatus(400)
            ->assertJsonStructure([
                'message'
            ]);

        Notification::assertNothingSent();
    }

    /** @test */
    public function it_can_resend_verification_notification()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $this->json('POST', route('verification.resend'), ['email' => $user->email])
            ->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);

        Notification::assertSentTo($user, EmailVerificationNotification::class);
    }

    /** @test */
    public function it_can_not_resend_verification_notification_if_email_does_not_exist()
    {
        $this->json('POST', route('verification.resend'), ['email' => 'invalid-email'])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
                ]
            ]);
    }

    /** @test */
    public function it_can_not_resend_verification_notification_if_email_already_verified()
    {
        Notification::fake();

        $this->json('POST', route('verification.resend'), ['email' => $this->user->email])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
                ]
            ]);

        Notification::assertNothingSent();
    }
}
