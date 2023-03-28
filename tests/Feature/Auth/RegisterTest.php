<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase,WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_register_blank_fields_fail()
    {
        $response = $this->json('POST', 'api/v1/auth/register', ['Accept' => 'application/json']);
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

    public function test_register_wrong_credentials_set_1_fail()
    {
        $userData = [
            "name" => "e",
            "email" => "doe",
            "password" => "123",
            "password_confirmation" => "123",
        ];

        $response = $this->json('POST', 'api/v1/auth/register', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $response->assertJson([
            "code" => 401,
            "status" => false,
            "message" => "The given data was invalid.",
            "errors" => [
                "name" => ["The name field is minimum 3 character."],
                "email" => ["The email must be a valid email address."],
                "password" => ["The password field is minimum 8 character."],
            ]
        ]);
    }

    public function test_register_wrong_credentials_set_2_fail()
    {
        $user = User::factory()->create([
            'email' => 'sample@test.com',
            'password' => bcrypt('sample12345'),
        ]);

        $userData = [
            "name" => "doedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoe",
            "email" => "sample@test.com",
            "password" => "doedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoedoe",
            "password_confirmation" => "1232",
        ];

        $response = $this->json('POST', 'api/v1/auth/register', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $response->assertJson([
            "code" => 422,
            "status" => false,
            "message" => "The given data was invalid.",
            "errors" => [
                "name" => ["The name field is maximum 50 character."],
                "email" => ["The email has already been taken."],
                "password" => ["The password field is maximum 20 character."],
                "password_confirmation" => ["The password_confirmation confirmation does not match."],
            ]
        ]);
    }

    public function test_register_no_match_password_confirmation_fail()
    {
        $userData = [
            "name" => "Emre Demirel",
            "email" => "doe@example.com",
            "password" => "123456789",
            "password_confirmation" => "123456777",
        ];

        $response = $this->json('POST', 'api/v1/auth/register', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $response->assertJson([
            "code" => 422,
            "status" => false,
            "message" => "Your credentials are incorrect.",
            "errors" => [
                "password" => ["The password confirmation does not match."],
            ]
        ]);
    }

    public function test_register_successful()
    {
        $userData = [
            "name" => $name = 'Emre Demirel',
            "email" => $email = 'tester@mail.com',
            "password" => "12345678",
            "password_confirmation" => "12345678",
        ];

        $response = $this->json('POST', 'api/v1/auth/register', $userData, ['Accept' => 'application/json']);
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

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => $name,
        ]);

       
    }
}
