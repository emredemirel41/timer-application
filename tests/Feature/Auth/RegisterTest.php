<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_blank_fields_fail()
    {
        $response = $this->json('POST', 'api/auth/register', ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $response->assertJson([
            "code" => 422,
            "status" => false,
            "message" => "The given data was invalid.",
            "errors" => [
                "name" => ["The name field is required."],
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
                "password_confirmation" => ["The password_confirmation field is required."],
            ]
        ]);
    }

    public function test_register_min_name_rule_fail()
    {
        $userData = [
            "name" => "e",
            "email" => "doe@example.com",
            "password" => "12345",
            "password_confirmation" => "12345",
        ];

        $response = $this->json('POST', 'api/auth/register', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(401);
        $response->assertJson([
            "code" => 401,
            "status" => false,
            "message" => "Your credentials are incorrect.",
            "errors" => [
                "name" => ["The name field is minimum 5 character."],
            ]
        ]);
    }

    public function test_register_no_match_password_confirmation_fail()
    {
        $userData = [
            "name" => "Emre Demirel",
            "email" => "doe@example.com",
            "password" => "123",
            "password_confirmation" => "12345",
        ];

        $response = $this->json('POST', 'api/auth/register', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(401);
        $response->assertJson([
            "code" => 401,
            "status" => false,
            "message" => "Your credentials are incorrect.",
            "errors" => [
                "name" => ["The name field is minimum 5 character."],
            ]
        ]);
    }

    public function test_register_min_password_rule_fail()
    {
        $userData = [
            "name" => "Emre Demirel",
            "email" => "doe@example.com",
            "password" => "123",
            "password_confirmation" => "123",
        ];

        $response = $this->json('POST', 'api/auth/register', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(401);
        $response->assertJson([
            "code" => 401,
            "status" => false,
            "message" => "Your credentials are incorrect.",
            "errors" => [
                "password" => ["The name field is minimum 8 character."],
            ]
        ]);
    }

    public function test_register_successful()
    {
        $userData = [
            "name" => "Emre Demirel",
            "email" => "doe@example.com",
            "password" => "12345678",
            "password_confirmation" => "12345678",
        ];

        $response = $this->json('POST', 'api/auth/register', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(201);
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
