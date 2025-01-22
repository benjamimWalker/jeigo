<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SigninTest extends TestCase
{
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $this->postJson(route('signin'), [
            'email' => $user->email,
            'password' => 'password'
        ])
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'token'
            ])
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name
            ]);

        $user->delete();
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $this->postJson(route('signin'), [
            'email' => $user->email,
            'password' => 'wrong-password'
        ])
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Wrong email or password'
            ]);

        $user->delete();
    }
}
