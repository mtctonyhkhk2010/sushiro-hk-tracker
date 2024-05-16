<div wire:poll.60s>
    <div class="flex">
        <div class="flex-none w-14 h-14 ">
            <button class="btn btn-ghost btn-circle" onclick="history.back()">
                <x-heroicon-o-arrow-uturn-left class="w-5 h-5" />
            </button>
        </div>
        <div class="text-xl text-center grow">
            {{ $store->name }}
            <br>
            <span class="text-xs">更新時間: {{ now()->toDateTimeString() }}</span>
        </div>
        <div class="flex-none w-14 h-14 ">
            @if(in_array($store->id, session('liked_stores', [])))
                <button class="btn btn-ghost btn-circle" wire:click="unlike" >
                    <x-heroicon-s-heart class="w-5 h-5" />
                </button>
            @else
                <button class="btn btn-ghost btn-circle" wire:click="like">
                    <x-heroicon-o-heart class="w-5 h-5" />
                </button>
            @endif
        </div>
    </div>

    <div class="card bg-base-100 mb-3 shadow-xl">
        <div class="card-body">
            <a target="_blank" href="https://www.google.com/maps/search/?api=1&query={{ $store->location->latitude }},{{ $store->location->longitude }}">
                <p><x-icon name="o-map" /> {{ $store->address }}</p>
            </a>
        </div>
    </div>
    <x-card title="下一組顧客" class="mb-3" shadow separator>
        @if(!$store->local_ticketing_status)
        <x-slot:subtitle>
            <x-badge value="已停飛" class="badge-secondary" />
        </x-slot:subtitle>
        @endif
        <div class="flex gap-3">
            @if($store->status === \App\Enums\StoreStatus::Open)
                @forelse($store->store_queue as $number)
                    <x-badge :value="$number" class="badge-neutral" />
                @empty
                    <x-badge value="無人排隊" class="badge-accent" />
                @endforelse
            @else
                <x-badge value="檔已收" class="badge-accent" />
            @endif
        </div>
    </x-card>

    @if($store->status === \App\Enums\StoreStatus::Open)
        <div class="flex gap-3 mb-3">
            <x-card title="輪候人數" class="basis-1/2" shadow separator>
                @if($store->wait_group > 0)
                    {{ $store->wait_group }}
                @else
                    無人排隊
                @endif
            </x-card>
            <x-card title="輪候時間" class="basis-1/2" shadow separator>
                @if($store->wait_time > 0)
                    大約 {{ $store->wait_time }} 分鐘
                @else
                    無人排隊
                @endif
            </x-card>
        </div>

        <div x-data="notification">
            @if(isset($this->pushSubscription))
                <x-alert class="alert-info" icon="o-exclamation-triangle">
                    當{{ \App\Models\Store::find($this->pushSubscription->store_id)->name }}差唔多叫到{{ $this->pushSubscription->queue_number }}號就會提你
                    <x-slot:actions>
                        <x-button class="btn-warning btn-sm" label="按此取消" wire:click="cancelSubscription" />
                    </x-slot:actions>
                </x-alert>
            @else
                <x-button class="btn-warning btn-sm" label="叫號提醒" @click="$wire.queueModal = true" />
            @endif
            <x-modal wire:model="queueModal" title="你張飛幾多號？" subtitle="就到個陣提你">
                <x-input x-model="queue_number" label="號碼" inline />
                <x-slot:actions>
                    <x-button label="取消" @click="$wire.queueModal = false" />
                    <x-button label="確認" class="btn-primary" x-on:click="requestNotification" />
                </x-slot:actions>
            </x-modal>
        </div>
    @endif
</div>

@script
<script>
    document.addEventListener("visibilitychange", () => {
        if(document.visibilityState === "visible") $wire.$refresh();
    });

    Alpine.data('notification', () => {
        return {
            queue_number: null,

            requestNotification() {
                Notification.requestPermission().then((permission) => {
                    if (permission === 'granted') {

                        //get service worker
                        navigator.serviceWorker.ready.then((sw) => {

                            //subscribe
                            sw.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: "BDTBZSCdaa_airwORtBHlMrKE2HO4QasA1T0ZEONmsrh9qnUVQ7uKCgglVgwLHL46d-J69SscQCr81iNUhsVrMw"
                            }).then((subscription) => {
                                //subscription successful
                                fetch("/api/push-subscribe", {
                                    method: "post",
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        store_id: @js($store->id),
                                        queue_number: this.queue_number,
                                        session_id: @js(session()->getId()),
                                        data: subscription,
                                    })
                                }).then(() => {
                                    $wire.queueModal = false;
                                    $wire.$refresh();
                                });
                            });
                        });
                    }
                });
            },
        }
    })
</script>
@endscript
