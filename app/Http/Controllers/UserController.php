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
        return $this->paginateWords($request, 'favoriteWords', 'favorite_words');
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
     *         description="The word to favorite",
     *         required=true,
     *         @OA\Schema(type="string", example="peach")
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
    public function favoriteAWord(Word $word): Response
    {
        auth()->user()->favoriteWords()->syncWithoutDetaching([$word->id]);

        return response()->noContent();
    }

    /**
     * Unfavorite a word
     * @OA\Delete (
     *     path="api/entries/en/{word}/unfavorite",
     *     tags={"Words"},
     *     security={ {"token": {} }},
     *     @OA\Parameter (
     *         name="word",
     *         in="path",
     *         description="The the word to unfavorite",
     *         required=true,
     *         @OA\Schema(type="string", example="peach")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No Content (word unfavorited successfully)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Word not found"
     *     )
     * )
     */
    public function unfavoriteAWord(Word $word): Response
    {
        auth()->user()->favoriteWords()->detach($word->id);

        return response()->noContent();
    }

    /**
     * List words history
     * @OA\Get (
     *     path="api/user/me/history",
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
     *                     @OA\Property(property="word", type="string", example="aah"),
     *                     @OA\Property(property="added", type="string", format="date-time", example="2025-01-23T11:52:37.000000Z")
     *                 )
     *             ),
     *             @OA\Property(property="path", type="string", example="http://localhost/api/user/me/history?cursor=eyJ3b3Jkcy5pZCI6MywiX3BvaW50c1RvTmV4dEl0ZW1zIjp0cnVlfQ"),
     *             @OA\Property(property="per_page", type="integer", example=3),
     *             @OA\Property(property="next_cursor", type="string", example=null),
     *             @OA\Property(property="next_page_url", type="string", example=null),
     *             @OA\Property(property="prev_cursor", type="string", example="eyJ3b3Jkcy5pZCI6NCwiX3BvaW50c1RvTmV4dEl0ZW1zIjpmYWxzZX0"),
     *             @OA\Property(property="prev_page_url", type="string", example="http://localhost/api/user/me/history?cursor=eyJ3b3Jkcy5pZCI6NCwiX3BvaW50c1RvTmV4dEl0ZW1zIjpmYWxzZX0")
     *         )
     *     )
     * )
     */

    public function wordsHistory(Request $request): JsonResponse
    {
        return $this->paginateWords($request, 'wordHistory', 'history_words');
    }

    private function paginateWords(Request $request, string $relation, string $cachePrefix): JsonResponse
    {
        $cursor = $request->query('cursor', '');
        $perPage = $request->query('per_page', config('jeigo.per_page'));
        $cacheKey = $cachePrefix . '_' . $perPage . '_' . $cursor;

        $startTime = microtime(true);
        $cacheHeader = cache()->has($cacheKey) ? 'HIT' : 'MISS';

        $data = cache()->remember(
            $cacheKey,
            60 * 60 * 24 * 30,
            fn() => auth()
                ->user()
                ->{$relation}()
                ->withPivot('created_at')
                ->cursorPaginate($perPage)
        );

        $results = collect($data->items())->map(
            fn(Word $word) => [
                'word' => $word->word,
                'added' => $word->pivot->created_at,
            ]
        );

        return response()->json([
            'results' => $results,
            'path' => $data->url($data->cursor()),
            'per_page' => $data->perPage(),
            'next_cursor' => $data->nextCursor(),
            'next_page_url' => $data->nextPageUrl(),
            'prev_cursor' => $data->previousCursor(),
            'prev_page_url' => $data->previousPageUrl(),
        ])
            ->header('x-cache', $cacheHeader)
            ->header('x-response-time', (microtime(true) - $startTime) * 1000);
    }
}
