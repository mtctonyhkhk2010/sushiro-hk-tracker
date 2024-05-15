<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
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
    public function handle()
    {
        $store_response = Http::get('https://sushipass.sushiro.com.hk/api/2.0/info/storelist?latitude=22&longitude=114&numresults=99&region=HK');

        if ($store_response->failed()) return;

        $store_response = $store_response->collect();

        $stores = Store::all();

        $new_stores_ids = $store_response->pluck('id')->diff($stores->pluck('sushiro_store_id'));

        $closed_stores_ids = $stores->pluck('sushiro_store_id')->diff($store_response->pluck('id'));

        foreach ($store_response->whereIn('id', $new_stores_ids) as $store)
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

        if ($closed_stores_ids->isNotEmpty()) Store::whereIn('id', $closed_stores_ids)->delete();
    }
}
