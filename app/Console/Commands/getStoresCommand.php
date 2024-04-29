<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use MatanYadaev\EloquentSpatial\Objects\Point;

class getStoresCommand extends Command
{
    protected $signature = 'get:stores';

    protected $description = 'Command description';

    public function handle(): void
    {
        $response = Http::get('https://sushipass.sushiro.com.hk/api/2.0/info/storelist?latitude=22&longitude=114&numresults=99&region=HK');

        $stores = $response->json();

        foreach ($stores as $store)
        {
            Store::create([
                'name' => $store['name'],
                'address' => $store['address'],
                'region' => $store['region'],
                'status' => $store['storeStatus'],
                'net_ticket_status' => $store['netTicketStatus'],
                'location' => new Point($store['latitude'], $store['longitude']),
            ]);
        }
    }
}
