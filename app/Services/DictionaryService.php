<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

readonly class DictionaryService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.dictionary.base_url');
    }

    /**
     * @throws Exception
     */
    public function getWord(string $word): array
    {
        $response = Http::get($this->baseUrl . $word);

        if ($response->successful()) {
            return $response->json();
        } else if ($response->clientError()) {
            throw new Exception('Word not found', 404);
        } else {
            throw new Exception('External dictionary service is unavailable', 500);
        }
    }
}
