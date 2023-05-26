<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthTest extends TestCase
{

    public function test_it_registers_user(): void
    {
        $pass = $this->faker->password(10);
        $email = $this->faker->email;
        $name = $this->faker->name;
        $response = $this->postJson('/api/register', [
            'email' => $email,
            'name' => $name,
            'password' => $pass,
            'confirm_password' => $pass
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', ['email' => $email, 'name' => $name]);
    }

    public function test_it_generates_tokens_for_users()
    {
        // create user
        $user = User::factory()->createOne();
        // login as the user
        $res = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // verify you get the token
        $res->assertOk()->assertJson(function (AssertableJson $json) use ($user) {
           $json->has('token');
           $json->where('user', $user->id);
        });
    }
}
