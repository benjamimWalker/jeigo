<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ListWordsHistoryTest extends TestCase
{
    public function test_returns_user_words_history_sucessfully(): void
    {
        $user = User::factory()->create();
        $user->wordHistory()->attach([1, 2, 3]);

        $this->actingAs($user)
            ->getJson(route('user.history'))
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
