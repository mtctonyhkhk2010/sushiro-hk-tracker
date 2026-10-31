<?php

use App\Enums\StoreStatus;
use App\Models\Record;
use App\Models\Statistic;
use App\Models\Store;
use Database\Seeders\StoreSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MatanYadaev\EloquentSpatial\Objects\Point;

uses(DatabaseTransactions::class);

beforeEach(function () {
    config([
        'services.sushiro.base_url' => 'https://crm.example.test/api/2.0/',
        'services.sushiro.region' => 'HK',
        'services.sushiro.guid' => 'test-client-guid',
    ]);

    Http::preventStrayRequests();
    $this->travelTo(now()->setTime(12, 15));
});

it('refreshes queues and waiting statistics using the CRM endpoints', function () {
    $store = createSushiroTestStore(900001);
    $directory = sushiroStorePayload(900001);
    $directory['localTicketingStatus'] = 'OFF';

    Http::fake([
        'crm.example.test/api/2.0/info/storelist*' => Http::response([$directory]),
        'crm.example.test/api/2.0/remote/groupqueues*' => Http::response([
            'storeQueue' => ['006', '007', '008'],
        ]),
    ]);

    $this->artisan('app:get-stores-group-queues')->assertSuccessful();

    $store->refresh();

    expect($store->store_queue)->toBe(['006', '007', '008'])
        ->and($store->status)->toBe(StoreStatus::Open)
        ->and($store->wait_group)->toBe(9)
        ->and($store->wait_time)->toBe(5)
        ->and((bool) $store->local_ticketing_status)->toBeFalse()
        ->and(Record::where('store_id', $store->id)->first()->wait_group)->toBe(9)
        ->and(Statistic::where('store_id', $store->id)->count())->toBe(1);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://crm.example.test/api/2.0/info/storelist?guid=test-client-guid&region=HK'
    );
    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://crm.example.test/api/2.0/remote/groupqueues?storeid=900001&region=HK'
    );

    $this->artisan('app:get-stores-group-queues')->assertSuccessful();

    expect(Statistic::where('store_id', $store->id)->count())->toBe(1);
});

it('preserves queue data when a grouped queue cannot be read', function (string $failure) {
    $store = createSushiroTestStore(900001);

    Http::fake([
        'crm.example.test/api/2.0/info/storelist*' => Http::response([sushiroStorePayload(900001)]),
        'crm.example.test/api/2.0/remote/groupqueues*' => match ($failure) {
            'http error' => Http::response([], 503),
            'connection error' => Http::failedConnection(),
            'missing queue' => Http::response(['message' => 'Unavailable']),
        },
    ]);

    $this->artisan('app:get-stores-group-queues')->assertSuccessful();

    expect($store->fresh()->store_queue)->toBe(['001'])
        ->and(Record::where('store_id', $store->id)->exists())->toBeFalse();
})->with(['http error', 'connection error', 'missing queue']);

it('accepts an empty queue without losing store status updates', function () {
    $store = createSushiroTestStore(900001);

    Http::fake([
        'crm.example.test/api/2.0/info/storelist*' => Http::response([sushiroStorePayload(900001)]),
        'crm.example.test/api/2.0/remote/groupqueues*' => Http::response(['storeQueue' => []]),
    ]);

    $this->artisan('app:get-stores-group-queues')->assertSuccessful();

    expect($store->fresh()->store_queue)->toBe([])
        ->and($store->fresh()->wait_group)->toBe(9);
});

it('skips stores missing from the directory', function () {
    $store = createSushiroTestStore(900001);

    Http::fake([
        'crm.example.test/api/2.0/info/storelist*' => Http::response([]),
        'crm.example.test/api/2.0/remote/groupqueues*' => Http::response(['storeQueue' => ['006']]),
    ]);

    $this->artisan('app:get-stores-group-queues')->assertSuccessful();

    expect($store->fresh()->store_queue)->toBe(['001']);
});

it('syncs new and removed stores using their remote IDs', function () {
    $retainedStore = createSushiroTestStore(900001);
    $removedStore = createSushiroTestStore(900002);
    $directory = Store::where('sushiro_store_id', '!=', 900002)->get()
        ->map(fn (Store $store): array => sushiroStorePayload($store->sushiro_store_id))
        ->push(sushiroStorePayload(900003))->all();

    Http::fake(['crm.example.test/api/2.0/info/storelist*' => Http::response($directory)]);

    $this->artisan('app:get-new-stores')->assertSuccessful();

    expect($retainedStore->fresh())->not->toBeNull()
        ->and($removedStore->fresh())->toBeNull();

    $newStore = Store::where('sushiro_store_id', 900003)->firstOrFail();

    expect($newStore->getTranslation('name', 'zh_HK'))->toBe('測試店')
        ->and($newStore->location->latitude)->toBe(22.3711547)
        ->and($newStore->location->longitude)->toBe(114.1111349);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://crm.example.test/api/2.0/info/storelist?guid=test-client-guid&region=HK'
    );
});

it('preserves stores when the directory is unavailable or invalid', function (mixed $body, int $status) {
    $store = createSushiroTestStore(900001);

    Http::fake(['crm.example.test/api/2.0/info/storelist*' => Http::response($body, $status)]);

    $this->artisan('app:get-new-stores')->assertFailed();

    expect($store->fresh())->not->toBeNull();
})->with([
    'http error' => [[], 503],
    'error object' => [['message' => 'Invalid GUID'], 200],
    'invalid json' => ['unavailable', 200],
    'empty directory' => [[], 200],
]);

it('stops queue polling when the directory fails', function () {
    Http::fake(['crm.example.test/api/2.0/info/storelist*' => Http::response([], 503)]);

    $this->artisan('app:get-stores-group-queues')->assertFailed();

    Http::assertSentCount(1);
});

it('seeds stores using a generated GUID when none is configured', function () {
    config(['services.sushiro.guid' => null]);

    Http::fake(['crm.example.test/api/2.0/info/storelist*' => Http::response([sushiroStorePayload(900004)])]);

    $this->seed(StoreSeeder::class);

    expect(Store::where('sushiro_store_id', 900004)->exists())->toBeTrue();

    Http::assertSent(function (Request $request): bool {
        parse_str(parse_url($request->url(), PHP_URL_QUERY), $query);

        return $request->method() === 'GET'
            && Str::isUuid($query['guid'])
            && $query['region'] === 'HK'
            && ! isset($query['latitude'], $query['longitude'], $query['numresults']);
    });
});

it('does not seed stores when the API fails', function () {
    $storeCount = Store::count();

    Http::fake(['crm.example.test/api/2.0/info/storelist*' => Http::response([], 503)]);

    expect(fn () => $this->seed(StoreSeeder::class))->toThrow(RequestException::class)
        ->and(Store::count())->toBe($storeCount);
});

/**
 * @return array{id: int, name: string, address: string, region: string, storeStatus: string, latitude: float, longitude: float, waitingGroup: int, wait: int, localTicketingStatus: string}
 */
function sushiroStorePayload(int $storeId): array
{
    return [
        'id' => $storeId,
        'name' => '測試店',
        'address' => 'Test address',
        'region' => '新界',
        'storeStatus' => 'OPEN',
        'latitude' => 22.3711547,
        'longitude' => 114.1111349,
        'waitingGroup' => 9,
        'wait' => 5,
        'localTicketingStatus' => 'ON',
    ];
}

function createSushiroTestStore(int $storeId): Store
{
    return Store::create([
        'sushiro_store_id' => $storeId,
        'name' => ['zh_HK' => '測試店'],
        'address' => 'Test address',
        'region' => '新界',
        'status' => StoreStatus::Open,
        'location' => new Point(22.3711547, 114.1111349),
        'store_queue' => ['001'],
        'wait_group' => 0,
        'wait_time' => 0,
    ]);
}
