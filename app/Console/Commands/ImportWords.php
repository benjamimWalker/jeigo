<?php

namespace App\Console\Commands;

use App\Models\Word;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ImportWords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-words';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import words from a file';

    /**
     * Execute the console command.
     * @throws Exception
     */
    public function handle()
    {
        $url = 'https://raw.githubusercontent.com/dwyl/english-words/refs/heads/master/words_dictionary.json';

        foreach (array_chunk(
                     array_keys(
                         json_decode(
                             tap(Http::get($url), function (Response $response) {
                                 if (!$response->successful()) {
                                     throw new Exception('Failed to fetch data');
                                 }
                             })->body(),
                             true
                         )), 2000
                 ) as $chunk) {
            Word::insert(array_map(fn(string $word) => ['word' => $word], $chunk));
        }
    }
}
