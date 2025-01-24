<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ShowWordTest extends TestCase
{
    public function test_return_word_successfully(): void
    {
        $this->actingAs($user = User::factory()->create())
            ->getJson(route('entries.en.show', ['word' => 'test']))
            ->assertOk()
            ->assertJsonFragment([
                'word' => 'test'
            ]);

        $user->delete();
    }
}
