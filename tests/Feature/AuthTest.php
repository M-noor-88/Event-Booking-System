<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;


    public function test_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
            'phone'=> '0987654321',
            'role'=> 'customer'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id', 'name', 'email', 'phone', 'role', 'created_at', 'updated_at'
                    ],
                    'token'
                ]
            ]);
    }



    public function test_user_can_login_with_valid_credentials()
    {
        // 1. Create a user
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        // 2. Attempt login
        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'phone', 'role', 'created_at', 'updated_at'],
                    'token'
                ]
            ]);

        $this->assertEquals($user->id, $response->json('data.user.id'));

    }


}
