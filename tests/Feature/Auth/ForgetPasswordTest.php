<?php

namespace Tests\Feature\Auth;

use App\Events\ResetPassword;
use App\Listeners\SendResetTokenEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ForgetPasswordTest extends TestCase
{
    use RefreshDatabase,WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_forget_password_blank_fields_fail()
    {
        $response = $this->json('POST', 'api/v1/auth/forget-password', ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $response->assertJson([
            "code" => 422,
            "status" => false,
            "message" => "The given data was invalid.",
            "errors" => [
                "email" => ["The email field is required."],
            ]
        ]);
    }

    public function test_forget_password_not_exists_email_fail()
    {
        $userData = [
            "email" => "doe@example.com",
        ];

        $response = $this->json('POST', 'api/v1/auth/forget-password', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(401);
        $response->assertJson([
            "code" => 401,
            "status" => false,
            "message" => "Your credentials are incorrect.",
            "errors" => null
        ]);
    }

    public function test_forget_password_successful()
    {

        $user = User::factory()->create([
            'email' => 'sample@test.com',
            'password' => bcrypt('sample12345'),
        ]);

        $userData = [
            "email" => "sample@test.com",
        ];

        $response = $this->json('POST', 'api/v1/auth/forget-password', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(200);
        $response->assertJson([
            "code" => 200,
            "status" => false,
            "message" => "Your email has been sent.",
            "data" => null
        ]);

    }
 
}
