<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
