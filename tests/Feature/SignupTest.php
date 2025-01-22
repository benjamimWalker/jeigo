<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SignupTest extends TestCase
{
    public function test_user_can_signup(): void
    {
        $response = $this->postJson(route('signup'), [
            'name' => 'John Doe',
            'email' => 'foo@bar.com',
            'password' => 'password'
        ])
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'name'
            ])
            ->assertJson([
                'name' => 'John Doe'
            ])
            ->assertJsonMissing([
                'password' => 'password'
            ]);

        User::find($response->json('id'))->delete();
    }

    public function test_user_cannot_signup_without_name(): void
    {
        $this->postJson(route('signup'), [
            'email' => 'foo@bar.com',
            'password' => 'password'
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_user_cannot_signup_without_email(): void
    {
        $this->postJson(route('signup'), [
            'name' => 'John Doe',
            'password' => 'password'
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_user_cannot_signup_without_password(): void
    {
        $this->postJson(route('signup'), [
            'name' => 'John Doe',
            'email' => 'foo@bar.com',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }
}
