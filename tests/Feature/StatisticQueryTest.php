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

it('shares rankings across visits for each meal period until midnight', function () {
    $loadRankings = function (string $period = '全日'): array {
        return DB::connection()->pretend(function () use ($period): void {
            $leastPopular = new LeastPopularStores;
            $leastPopular->time = $period;
            $leastPopular->least_popular_stores();

            $mostPopular = new MostPopularStores;
            $mostPopular->time = $period;
            $mostPopular->most_popular_stores();
        });
    };

    $today = now()->toImmutable()->startOfDay();
    $this->travelTo($today->setHour(8));

    expect($loadRankings())->toHaveCount(2)
        ->and($loadRankings('午市'))->toHaveCount(2)
        ->and($loadRankings('晚市'))->toHaveCount(2);

    $this->travelTo($today->setTime(23, 59, 59));

    expect($loadRankings())->toBeEmpty()
        ->and($loadRankings('午市'))->toBeEmpty()
        ->and($loadRankings('晚市'))->toBeEmpty();

    $this->travelTo($today->addDay());

    expect($loadRankings())->toHaveCount(2);
});
