<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Services\DictionaryService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WordController extends Controller
{
    /**
     * List words
     * @OA\Get (
     *     path="api/entries/en",
     *     tags={"Words"},
     *     security={ {"token": {} }},
     *     @OA\Parameter (
     *         name="search",
     *         in="query",
     *         description="Search term",
     *         required=false,
     *     @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter (
     *         name="cursor",
     *         in="query",
     *         description="Cursor",
     *         required=false,
     *     @OA\Schema(type="string")
     *    ),
     *     @OA\Parameter (
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *     @OA\Schema(type="integer")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="results", type="array", collectionFormat="multi",
     *                  @OA\Items(
     *                      type="string", example="benches"
     *                  ),
     *              ),
     *              @OA\Property(property="path", type="string", example="http://localhost/api/entries/en"),
     *              @OA\Property(property="per_page", type="integer", example=20),
     *              @OA\Property(property="next_cursor", type="string", example="eyJ3b3Jkcy5pZCI6MzE0ODksIl9wb2ludHNUb05leHRJdGVtcyI6dHJ1ZX0"),
     *              @OA\Property(property="next_page_url", type="string", example="http://localhost/api/entries/en?cursor=eyJ3b3Jkcy5pZCI6MzE0ODksIl9wb2ludHNUb05leHRJdGVtcyI6dHJ1ZX0"),
     *              @OA\Property(property="prev_cursor", type="string", example="eyJ3b3Jkcy5pZCI6MzE0NzAsIl9wb2ludHNUb05leHRJdGVtcyI6ZmFsc2V9"),
     *              @OA\Property(property="prev_page_url", type="string", example="http://localhost/api/entries/en?cursor=eyJ3b3Jkcy5pZCI6MzE0NzAsIl9wb2ludHNUb05leHRJdGVtcyI6ZmFsc2V9")
     *          )
     *      )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search', '');
        $cursor = $request->query('cursor', '');
        $perPage = $request->query('per_page', config('jeigo.per_page'));
        $cacheKey = 'words_' . $search . '_' . $perPage . '_' . $cursor;

        $startTime = microtime(true);
        $cacheHeader = cache()->has($cacheKey) ? 'HIT' : 'MISS';

        $data = cache()->remember(
            $cacheKey,
            60 * 60 * 24 * 30,
            fn() => Word::when(
                str($search)->isNotEmpty() && str($cursor)->isEmpty(),
                fn(Builder $query) => $query->where('word', 'like', "$search%")
            )
                ->cursorPaginate($perPage)
        );

        $results = $data->pluck('word');
        $data = $data->toArray();
        Arr::forget($data, 'data');

        return response()->json(Arr::prepend($data, $results, 'results'))
            ->header('x-cache', $cacheHeader)
            ->header('x-response-time', (microtime(true) - $startTime) * 1000);
    }

    /**
     * Retrieve word information from the dictionary.
     *
     * @OA\Get(
     *     path="/api/entries/en/{word}",
     *     summary="Get word details",
     *     description="Returns details about the given word, including meanings, phonetics, and source links.",
     *     operationId="getWordDetails",
     *     tags={"Dictionary"},
     *     @OA\Parameter(
     *         name="word",
     *         in="path",
     *         description="The word to fetch information for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Word details retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="word", type="string", example="documentation"),
     *                 @OA\Property(property="phonetic", type="string", example="/ˌdɒkjʊmənˈteɪʃən/"),
     *                 @OA\Property(
     *                     property="phonetics",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="text", type="string", example="/ˌdɒkjʊmənˈteɪʃən/"),
     *                         @OA\Property(property="audio", type="string", example="")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="meanings",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="partOfSpeech", type="string", example="noun"),
     *                         @OA\Property(
     *                             property="definitions",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="definition", type="string", example="Something transposed from a thought to a document; the written account of an idea."),
     *                                 @OA\Property(property="synonyms", type="array", @OA\Items(type="string")),
     *                                 @OA\Property(property="antonyms", type="array", @OA\Items(type="string"))
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="license",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="CC BY-SA 3.0"),
     *                     @OA\Property(property="url", type="string", example="https://creativecommons.org/licenses/by-sa/3.0")
     *                 ),
     *                 @OA\Property(
     *                     property="sourceUrls",
     *                     type="array",
     *                     @OA\Items(type="string", example="https://en.wiktionary.org/wiki/documentation")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Word not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Word not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred.")
     *         )
     *     )
     * )
     */
    public function show(Word $word, DictionaryService $dictionary): JsonResponse
    {
        $cacheKey = "word_$word->word";
        $cacheHeader = cache()->has($cacheKey) ? 'HIT' : 'MISS';
        $startTime = microtime(true);

        try {
            $data = response()->json(
                cache()->remember(
                    $cacheKey, 60 * 60 * 3, fn() => $dictionary->getWord($word->word)
                )
            )
                ->header('x-cache', $cacheHeader)
                ->header('x-response-time', (microtime(true) - $startTime) * 1000);

            $this->saveHistory($word->id);

            return $data;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    private function saveHistory(int $wordId): void
    {
        auth()->user()->wordHistory()->syncWithoutDetaching([$wordId]);
    }
}
