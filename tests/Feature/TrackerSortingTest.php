<?php

use App\Enums\StoreStatus;
use App\Livewire\Tracker;
use App\Models\Store;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use MatanYadaev\EloquentSpatial\Objects\Point;

uses(DatabaseTransactions::class);

it('sorts region groups and their stores by distance', function () {
    $nearRegion = '測試近區';
    $farRegion = '測試遠區';
    Store::create([
        'sushiro_store_id' => 900001,
        'name' => ['zh_HK' => '遠店'],
        'status' => StoreStatus::Open,
        'address' => 'Far region address',
        'region' => $farRegion,
        'location' => new Point(22.8, 114.5),
        'wait_group' => 0,
        'wait_time' => 0,
    ]);
    $nearbyStore = Store::create([
        'sushiro_store_id' => 900002,
        'name' => ['zh_HK' => '黃店'],
        'status' => StoreStatus::Open,
        'address' => 'Nearby address',
        'region' => $nearRegion,
        'location' => new Point(22.3, 114.17),
        'wait_group' => 0,
        'wait_time' => 0,
    ]);
    $fartherStore = Store::create([
        'sushiro_store_id' => 900003,
        'name' => ['zh_HK' => '一店'],
        'status' => StoreStatus::Open,
        'address' => 'Farther address',
        'region' => $nearRegion,
        'location' => new Point(22.5, 114.4),
        'wait_group' => 0,
        'wait_time' => 0,
    ]);

    $tracker = new Tracker;
    $alphabeticalGroups = $tracker->store_region();
    $alphabeticalRegionOrder = $alphabeticalGroups->keys()->values()->all();

    expect(array_search($farRegion, $alphabeticalRegionOrder, true))
        ->toBeLessThan(array_search($nearRegion, $alphabeticalRegionOrder, true))
        ->and($alphabeticalGroups[$nearRegion]->pluck('id')->values()->all())
        ->toBe([$fartherStore->id, $nearbyStore->id]);

    $tracker->sortByDistance(22.3, 114.17);
    $distanceGroups = $tracker->store_region();
    $distanceRegionOrder = $distanceGroups->keys()->values()->all();

    expect(array_search($nearRegion, $distanceRegionOrder, true))
        ->toBeLessThan(array_search($farRegion, $distanceRegionOrder, true))
        ->and($distanceGroups[$nearRegion]->pluck('id')->values()->all())
        ->toBe([$nearbyStore->id, $fartherStore->id])
        ->and($tracker->sortMethod)->toBe('distance');

    $tracker->sortAlphabetically();

    expect($tracker->sortMethod)->toBe('alphabetical')
        ->and($tracker->latitude)->toBeNull()
        ->and($tracker->longitude)->toBeNull();
});

it('rejects invalid location coordinates', function (mixed $latitude, mixed $longitude) {
    expect(fn () => (new Tracker)->sortByDistance($latitude, $longitude))
        ->toThrow(ValidationException::class);
})->with([
    'latitude above maximum' => [91, 114],
    'longitude below minimum' => [22, -181],
    'non-numeric coordinates' => ['north', 'east'],
]);
