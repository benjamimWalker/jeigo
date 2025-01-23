<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function auth;

class UserController extends Controller
{
    /**
     * List favorite words
     * @OA\Get (
     *     path="api/user/me/favorites",
     *     tags={"User"},
     *     security={ {"token": {} }},
     *     @OA\Parameter (
     *         name="cursor",
     *         in="query",
     *         description="Cursor for pagination",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter (
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="results",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="word", type="string", example="aahed"),
     *                     @OA\Property(property="added", type="string", format="date-time", example="2025-01-22T22:59:15.000000Z")
     *                 )
     *             ),
     *             @OA\Property(property="path", type="string", example="http://localhost/api/user/me/favorites"),
     *             @OA\Property(property="per_page", type="integer", example=4),
     *             @OA\Property(property="next_cursor", type="string", example="eyJ3b3Jkcy5pZCI6OCwiX3BvaW50c1RvTmV4dEl0ZW1zIjp0cnVlfQ"),
     *             @OA\Property(property="next_page_url", type="string", example="http://localhost/api/user/me/favorites?cursor=eyJ3b3Jkcy5pZCI6OCwiX3BvaW50c1RvTmV4dEl0ZW1zIjp0cnVlfQ"),
     *             @OA\Property(property="prev_cursor", type="string", example="eyJ3b3Jkcy5pZCI6NSwiX3BvaW50c1RvTmV4dEl0ZW1zIjpmYWxzZX0"),
     *             @OA\Property(property="prev_page_url", type="string", example="http://localhost/api/user/me/favorites?cursor=eyJ3b3Jkcy5pZCI6NSwiX3BvaW50c1RvTmV4dEl0ZW1zIjpmYWxzZX0")
     *         )
     *     )
     * )
     */
    public function favoriteWords(Request $request): JsonResponse
    {
        $cursor = $request->query('cursor', '');
        $perPage = $request->query('per_page', config('jeigo.per_page'));
        $cacheKey = 'favorite_words_' . $perPage . '_' . $cursor;

        $startTime = microtime(true);
        $cacheHeader = cache()->has($cacheKey) ? 'HIT' : 'MISS';

        $data = cache()->remember(
            $cacheKey,
            60 * 60 * 24 * 30,
            fn() => auth()
                ->user()
                ->favoriteWords()
                ->withPivot('created_at')
                ->paginate($perPage)
        );

        $results = collect($data->items())->map(
            fn(Word $favorite) => [
                'word' => $favorite->word,
                'added' => $favorite->pivot->created_at
            ]
        );
        return response()->json([
            'results' => $results,
            'current_page' => $data->currentPage(),
            'first_page_url' => $data->url(1),
            'from' => $data->firstItem(),
            'last_page' => $data->lastPage(),
            'last_page_url' => $data->url($data->lastPage()),
            'links' => $data->links(),
            'next_page_url' => $data->nextPageUrl(),
            'path' => $data->url($data->currentPage()),
            'per_page' => $data->perPage(),
            'prev_page_url' => $data->previousPageUrl(),
            'to' => $data->lastItem(),
            'total' => $data->total()
        ])
            ->header('x-cache', $cacheHeader)
            ->header('x-response-time', (microtime(true) - $startTime) * 1000);
    }

    /**
     * Favorite a word
     * @OA\Post (
     *     path="api/entries/en/{word}/favorite",
     *     tags={"Words"},
     *     security={ {"token": {} }},
     *     @OA\Parameter (
     *         name="word",
     *         in="path",
     *         description="The ID of the word to favorite",
     *         required=true,
     *         @OA\Schema(type="integer", example=35)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No Content (word favorited successfully)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Word not found"
     *     )
     * )
     */
    public function favoriteAWord(int $wordId): Response
    {
        auth()->user()->favoriteWords()->syncWithoutDetaching([$wordId]);

        return response()->noContent();
    }
}
