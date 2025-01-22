<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WordListTest extends TestCase
{
    public function test_return_words_successfully(): void
    {
        $user = User::factory()->create();

        Cache::shouldReceive('remember')
            ->once()
            ->with('words_ben_10_', 60 * 60 * 24 * 30, \Mockery::any())
            ->andReturn(collect([['word' => 'ben']]));
        Cache::shouldReceive('has')
            ->once()
            ->andReturn(false);

        $this->actingAs($user)
            ->getJson(route('entries.en.index', ['search' => 'ben', 'per_page' => 10]))
            ->assertOk()
            ->assertJsonStructure([
                'results',
            ])
            ->assertJsonPath('results.0', 'ben');

        $user->delete();
    }
}
