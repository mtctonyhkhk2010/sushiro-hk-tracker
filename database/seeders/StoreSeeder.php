<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MatanYadaev\EloquentSpatial\Objects\Point;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $response = Http::get(rtrim(config('services.sushiro.base_url'), '/').'/info/storelist', [
            'guid' => config('services.sushiro.guid') ?: (string) Str::uuid(),
            'region' => config('services.sushiro.region'),
        ])->throw();

        $stores = $response->json();

        foreach ($stores as $store) {
            Store::create([
                'sushiro_store_id' => $store['id'],
                'name' => $store['name'],
                'address' => $store['address'],
                'region' => $store['region'],
                'status' => $store['storeStatus'],
                'location' => new Point($store['latitude'], $store['longitude']),
            ]);
        }
    }
}
