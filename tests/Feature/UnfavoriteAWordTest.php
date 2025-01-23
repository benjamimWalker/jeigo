<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UnfavoriteAWordTest extends TestCase
{
    public function test_unfavorite_a_word_successfully(): void
    {
        $user = User::factory()->create();
        $user->favoriteWords()->syncWithoutDetaching([1]);

        $this->actingAs($user)
            ->deleteJson(route('entries.en.unfavorite', ['word' => 1]))
            ->assertNoContent();

        self::assertCount(0, $user->favoriteWords);

        $user->delete();
    }
}
