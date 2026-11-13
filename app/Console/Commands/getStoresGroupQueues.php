<?php

namespace App\Console\Commands;

use App\Enums\StoreStatus;
use App\Models\Record;
use App\Models\Statistic;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class getStoresGroupQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-stores-group-queues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $base_url = rtrim(config('services.sushiro.base_url'), '/');
        $region = config('services.sushiro.region');
        $store_response = Http::get($base_url.'/info/storelist', [
            'guid' => config('services.sushiro.guid') ?: (string) Str::uuid(),
            'region' => $region,
        ]);

        if ($store_response->failed() || ! is_array($store_response->json()) || ! array_is_list($store_response->json())) {
            return self::FAILURE;
        }

        $store_directory = $store_response->collect()
            ->whereIn('storeStatus', [StoreStatus::Open->value, StoreStatus::Closed->value])
            ->keyBy('id');

        $stores = Store::query()->whereIn('sushiro_store_id', $store_directory->keys())->get();
        $open_stores = $stores->filter(fn (Store $store): bool => $store_directory[$store->sushiro_store_id]['storeStatus'] === StoreStatus::Open->value
        );

        $responses = $open_stores->isEmpty() ? [] : Http::pool(function (Pool $pool) use ($open_stores, $base_url, $region): void {
            foreach ($open_stores as $store) {
                $pool->as($store->sushiro_store_id)
                    ->withUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36')
                    ->get($base_url.'/remote/groupqueues', [
                        'storeid' => $store->sushiro_store_id,
                        'region' => $region,
                    ]);
            }
        });

        foreach ($stores as $store) {
            $target_store = $store_directory[$store->sushiro_store_id];
            $store_queue = [];

            if ($target_store['storeStatus'] === StoreStatus::Open->value) {
                $response = $responses[$store->sushiro_store_id] ?? null;
                if (! $response instanceof Response || $response->failed() || ! is_array($response->json('mixedQueue'))) {
                    continue;
                }

                $store_queue = $response->json('mixedQueue');
            }

            $store->update([
                'status' => $target_store['storeStatus'],
                'store_queue' => $store_queue,
                'wait_group' => $target_store['waitingGroup'],
                'wait_time' => $target_store['wait'],
                'local_ticketing_status' => $target_store['localTicketingStatus'] === 'ON',
            ]);

            if (now()->minute % 15 === 0) {
                Record::create([
                    'store_id' => $store->id,
                    'wait_group' => $target_store['waitingGroup'],
                    'wait_time' => $target_store['wait'],
                ]);
            }

            // store cutoff and no cutoff record
            if ($target_store['localTicketingStatus'] === 'OFF'
                && ! Statistic::where('store_id', $store->id)->where('created_at', 'like', now()->toDateString().'%')->exists()) {
                Statistic::create([
                    'store_id' => $store->id,
                    'cutoff' => now(),
                ]);
            }
        }

        return self::SUCCESS;
    }
}
