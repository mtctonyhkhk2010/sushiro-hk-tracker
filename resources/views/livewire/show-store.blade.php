<div wire:poll.60s>
    <div class="flex">
        <div class="flex-none w-14 h-14 ">
            <button class="btn btn-ghost btn-circle">
                <x-heroicon-o-arrow-uturn-left onclick="history.back()"/>
            </button>
        </div>
        <div class="text-xl text-center grow">
            {{ $store->name }}
            <br>
            <span class="text-xs">更新時間: {{ now()->toDateTimeString() }}</span>
        </div>
        <div class="flex-none w-14 h-14 ">
            <button class="btn btn-ghost btn-circle">
                @if(in_array($store->id, session('liked_stores', [])))
                    <x-heroicon-s-heart wire:click="unlike" />
                @else
                    <x-heroicon-o-heart wire:click="like"/>
                @endif
            </button>
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
        <div class="flex gap-3">
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
    @endif
</div>

@script
<script>
    document.addEventListener("visibilitychange", () => {
        if(document.visibilityState === "visible") $wire.$refresh();
    });
</script>
@endscript
