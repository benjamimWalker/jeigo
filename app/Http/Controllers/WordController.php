<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @OA\Info(
 *    title="Jeigo API",
 *    description="Dictionary API",
 *    version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *      type="apiKey",
 *      in="header",
 *      securityScheme="token",
 *      name="Authorization"
 *  )
 */
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
}
