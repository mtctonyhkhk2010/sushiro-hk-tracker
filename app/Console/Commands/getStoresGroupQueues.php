<?php

namespace App\Console\Commands;

use App\Models\Record;
use App\Models\Statistic;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
    public function handle()
    {
        $store_response = Http::get('https://sushipass.sushiro.com.hk/api/2.0/info/storelist?latitude=22&longitude=114&numresults=99&region=HK');

        if ($store_response->failed()) return;

        $store_response = $store_response->collect();

        $stores = Store::all();

        foreach ($stores as $store) {
            $response = Http::get('https://sushipass.sushiro.com.hk/api/2.0/remote/groupqueues?region=HK&storeid=' . $store->sushiro_store_id);

            if ($response->failed()) continue;

            $response = $response->collect();

            $target_store = $store_response->firstWhere('id', $store->sushiro_store_id);

            $store->update([
                'status' => $target_store['storeStatus'],
                'store_queue' => $response['storeQueue'],
                'wait_group'  => $target_store['waitingGroup'],
                'wait_time'   => $target_store['wait'],
                'local_ticketing_status' => $target_store['localTicketingStatus'] === "ON",
            ]);

            if (now()->minute % 15 === 0)
            {
                Record::create([
                    'store_id' => $store->id,
                    'wait_group' => $target_store['waitingGroup'],
                    'wait_time' => $target_store['wait'],
                ]);
            }

            //store cutoff and no cutoff record
            if ($target_store['localTicketingStatus'] === "OFF"
                && !Statistic::where('store_id', $store->id)->where('created_at', 'like', now()->toDateString().'%')->exists())
            {
                Statistic::create([
                    'store_id' => $store->id,
                    'cutoff' => now()
                ]);
            }
        }
    }
}
