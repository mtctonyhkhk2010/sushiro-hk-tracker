<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MatanYadaev\EloquentSpatial\Objects\Point;

class getNewStores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-new-stores';

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
        $store_response = Http::get(rtrim(config('services.sushiro.base_url'), '/').'/info/storelist', [
            'guid' => config('services.sushiro.guid') ?: (string) Str::uuid(),
            'region' => config('services.sushiro.region'),
        ]);

        if ($store_response->failed() || ! is_array($store_response->json()) || ! array_is_list($store_response->json()) || $store_response->json() === []) {
            return self::FAILURE;
        }

        $store_response = $store_response->collect();

        $stores = Store::all();

        $new_stores_ids = $store_response->pluck('id')->diff($stores->pluck('sushiro_store_id'));

        $closed_stores_ids = $stores->pluck('sushiro_store_id')->diff($store_response->pluck('id'));

        foreach ($store_response->whereIn('id', $new_stores_ids) as $store) {
            Store::create([
                'sushiro_store_id' => $store['id'],
                'name' => $store['name'],
                'address' => $store['address'],
                'region' => $store['region'],
                'status' => $store['storeStatus'],
                'location' => new Point($store['latitude'], $store['longitude']),
            ]);
        }

        if ($closed_stores_ids->isNotEmpty()) {
            Store::whereIn('sushiro_store_id', $closed_stores_ids)->delete();
        }

        return self::SUCCESS;
    }
}
