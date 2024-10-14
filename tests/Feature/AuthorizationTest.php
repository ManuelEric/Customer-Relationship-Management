<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_login(): void
    {
        $user = \App\Models\User::find('3dabcbea-162a-43a0-98c2-6f5843fb7248');

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => '12345678'
        ]);

        $response->assertRedirect('dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
