<?php

use App\Livewire\Statistic\LeastPopularStores;
use App\Livewire\Statistic\MostPopularStores;
use Illuminate\Support\Facades\DB;

it('uses an indexable date range for statistic rankings', function (string $component, string $method) {
    $queries = DB::connection()->pretend(function () use ($component, $method) {
        (new $component)->{$method}();
    });

    expect($queries)->toHaveCount(1)
        ->and($queries[0]['query'])
        ->toContain('`created_at` between ')
        ->not->toContain('DATE(');
})->with([
    'most popular stores' => [MostPopularStores::class, 'most_popular_stores'],
    'least popular stores' => [LeastPopularStores::class, 'least_popular_stores'],
]);
