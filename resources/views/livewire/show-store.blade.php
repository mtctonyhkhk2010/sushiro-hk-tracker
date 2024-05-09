<div wire:poll.60s>
    <div class="flex">
        <div class="flex-none w-14 h-14 ">
            <button class="btn btn-ghost btn-circle">
                <x-heroicon-o-arrow-uturn-left wire:navigate href="/"/>
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
            <p>{{ $store->address }}</p>
        </div>
    </div>
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <p>下一組顧客</p>
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
        </div>
    </div>
</div>

@script
<script>
    document.addEventListener("visibilitychange", () => {
        if(document.visibilityState === "visible") $wire.$refresh();
    });
</script>
@endscript
