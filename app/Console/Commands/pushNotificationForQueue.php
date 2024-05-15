<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use App\Models\Store;
use Illuminate\Console\Command;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class pushNotificationForQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:push-notification-for-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify user for queue number';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $push_subs = PushSubscription::where('done', false)->get();

        foreach ($push_subs as $push_sub)
        {
            $store = Store::find($push_sub->store_id);

            if (!isset($store->store_queue[0])) continue;

            if ($store->store_queue[0] < $push_sub->queue_number - 5) continue;

            $webPush = new WebPush([
                "VAPID" => [
                    "publicKey" => "BDTBZSCdaa_airwORtBHlMrKE2HO4QasA1T0ZEONmsrh9qnUVQ7uKCgglVgwLHL46d-J69SscQCr81iNUhsVrMw",
                    "privateKey" => "mEgQn6vxtALs9CiqgEIi01z_wMvlkzMYrw8PLfnJeFc",
                    "subject" => config('app.url')."/store/".$push_sub->store_id,
                ]
            ]);

            $result = $webPush->sendOneNotification(
                Subscription::create(json_decode($push_sub->data ,true)),
                json_encode([
                    'title' => '就到你!',
                    'body' => '叫到'.$store->store_queue[0].'啦, 快d埋位啦',
                    'url' => config('app.url')."/store/".$push_sub->store_id,
                ])
            );

            if ($result->isSuccess())
            {
                $push_sub->done = true;
                $push_sub->save();
            }
        }
    }
}
