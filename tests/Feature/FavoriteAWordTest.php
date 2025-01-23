<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class FavoriteAWordTest extends TestCase
{
    public function test_favorite_a_word_successfully(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('entries.en.favorite', ['word' => 1]))
            ->assertNoContent();

        self::assertCount(1, $user->favoriteWords);

        $user->delete();
    }
}
