<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\UserClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientProgramControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {

        $client = UserClient::factory()->create();

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
