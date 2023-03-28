<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase,WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_reset_password_blank_fields_fail()
    {
        $response = $this->json('POST', 'api/v1/auth/reset-password', ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $response->assertJson([
            "code" => 422,
            "status" => false,
            "message" => "The given data was invalid.",
            "errors" => [
                "email" => ["The email field is required."],
                "password" => ["The password field is required."],
                "password_confirmation" => ["The password_confirmation field is required."],
                "token" => ["The token field is required."],
            ]
        ]);
    }

    public function test_reset_password_invalid_credentials_fail()
    {

        $userData = [
            "token" => "426341",
            "email" => "sample@test.com",
            "password" => "testpassword",
            "password_confirmation" => "testpassword",
        ];

        $response = $this->json('POST', 'api/v1/auth/reset-password', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(401);
        $response->assertJson([
            "code" => 401,
            "status" => false,
            "message" => "Your credentials are incorrect.",
            "errors" => null
        ]);
    }

    public function test_reset_password_no_match_password_confirmation_fail()
    {
        $user = User::factory()->create([
            'email' => 'sample@test.com',
            'password' => bcrypt('sample12345'),
        ]);

        $userData = [
            "token" => "123123",
            "email" => "sample@test.com",
            "password" => "testpassword",
            "password_confirmation" => "testpasswordwrong",
        ];

        $response = $this->json('POST', 'api/v1/auth/reset-password', $userData, ['Accept' => 'application/json']);
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

    public function test_reset_password_min_password_rule_fail()
    {
        $user = User::factory()->create([
            'email' => 'sample@test.com',
            'password' => bcrypt('sample12345'),
        ]);

        $userData = [
            "token" => "123123",
            "email" => "sample@test.com",
            "password" => "123",
            "password_confirmation" => "123",
        ];

        $response = $this->json('POST', 'api/v1/auth/reset-password', $userData, ['Accept' => 'application/json']);
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

    public function test_reset_password_successful()
    {

        $user = User::factory()->create([
            'email' => 'sample@test.com',
            'password' => bcrypt('sample12345'),
        ]);

        $code = rand(100000, 999999);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'code' => $code,
            'created_at' => now(),
        ]);
        $userData = [
            "token" => $code,
            "email" => $user->email,
            "password" => $newPassword = "999888777",
            "password_confirmation" => $newPassword,
        ];

        $response = $this->json('POST', 'api/v1/auth/reset-password', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(200);
        $response->assertJson([
            "code" => 200,
            "status" => false,
            "message" => "Your password has been updated.",
            "data" => null
        ]);

        $this->assertDatabaseMissing('password_resets', [
            'email' => $user->email,
            'code' => $code,
        ]);

        $updatedUser = $user->fresh();
        $this->assertTrue(Hash::check($newPassword, $updatedUser->password));
    }
}
