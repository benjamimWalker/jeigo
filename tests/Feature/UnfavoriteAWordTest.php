<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Word;
use Tests\TestCase;

class UnfavoriteAWordTest extends TestCase
{
    public function test_unfavorite_a_word_successfully(): void
    {
        $user = User::factory()->create();
        $user->favoriteWords()->syncWithoutDetaching([Word::where('word', 'bachelor')->first()->id]);

        $this->actingAs($user)
            ->deleteJson(route('entries.en.unfavorite', ['word' => 'bachelor']))
            ->assertNoContent();

        self::assertCount(0, $user->favoriteWords);

        $user->delete();
    }
}
