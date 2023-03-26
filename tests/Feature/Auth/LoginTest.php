<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_blank_fields_fail()
    {
        $response = $this->json('POST', 'api/auth/login', ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $response->assertJson([
            "code" => 422,
            "status" => false,
            "message" => "The given data was invalid.",
            "errors" => [
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
            ]
        ]);
    }

    public function test_login_wrong_credentials_fail()
    {
        $userData = [
            "email" => "doe@example.com",
            "password" => "demo12345"
        ];

        $response = $this->json('POST', 'api/auth/login', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(401);
        $response->assertJson([
            "code" => 401,
            "status" => false,
            "message" => "Your credentials are incorrect.",
            "errors" => null
        ]);
    }

    public function test_login_successful()
    {
        $user = User::factory()->create([
            'email' => 'sample@test.com',
            'password' => bcrypt('sample123'),
         ]);

         $loginData = ['email' => 'sample@test.com', 'password' => 'sample123'];

        $response = $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json']);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "code",
            "status",
            "message",
            "data" => [
                "user" => [
                   'id',
                   'name',
                   'email',
                   'email_verified_at',
                   'is_master',
                   'created_at',
                   'updated_at',
                ],
                "access_token",
            ]
        ]);
    }
}
