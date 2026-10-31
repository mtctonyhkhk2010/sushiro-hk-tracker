<?php

namespace App\Console\Commands;

use App\Models\Record;
use App\Models\Statistic;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
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

        $store_response = $store_response->collect();

        $stores = Store::all();

        $responses = Http::pool(function (Pool $pool) use ($stores, $base_url, $region): void {
            foreach ($stores as $store) {
                $pool->as($store->sushiro_store_id)->get($base_url.'/remote/groupqueues', [
                    'storeid' => $store->sushiro_store_id,
                    'region' => $region,
                ]);
            }
        });

        foreach ($responses as $store_id => $response) {
            if ($response instanceof \Throwable || $response->failed() || ! is_array($response->json('storeQueue'))) {
                continue;
            }

            $response = $response->collect();

            $store = $stores->where('sushiro_store_id', $store_id)->first();

            $target_store = $store_response->firstWhere('id', $store->sushiro_store_id);

            if ($target_store === null) {
                continue;
            }

            $store->update([
                'status' => $target_store['storeStatus'],
                'store_queue' => $response['storeQueue'],
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
