<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_hashes_the_password_when_creating()
    {
        $user = factory(User::class)->create([
            'password' => 'password'
        ]);

        $this->assertNotEquals($user->password, 'password');
    }
}
