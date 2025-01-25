<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Word;
use Tests\TestCase;

class ListFavoriteWordsTest extends TestCase
{
    public function test_returns_user_favorite_words_successfully(): void
    {
        $user = User::factory()->create();
        $user->favoriteWords()->attach(Word::take(3)->get());

        $this->actingAs($user)
            ->getJson(route('user.favorites'))
            ->assertOk()
            ->assertJsonStructure([
                'results',
                'path',
                'per_page',
                'next_cursor',
                'next_page_url',
                'prev_cursor',
                'prev_page_url'
            ]);
    }
}
