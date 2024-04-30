<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use MatanYadaev\EloquentSpatial\Objects\Point;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $response = Http::get('https://sushipass.sushiro.com.hk/api/2.0/info/storelist?latitude=22&longitude=114&numresults=99&region=HK');

        $stores = $response->json();

        foreach ($stores as $store)
        {
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
