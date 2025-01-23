<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ListFavoriteWordsTest extends TestCase
{
    public function test_returns_user_favorite_words_successfully(): void
    {
        $user = User::factory()->create();
        $user->favoriteWords()->attach([1, 2, 3]);

        $this->actingAs($user)
            ->getJson(route('user.favorites'))
            ->assertOk()
            ->assertJsonStructure([
                'results',
                'current_page',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
    }
}
