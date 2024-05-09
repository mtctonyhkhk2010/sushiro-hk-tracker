<div wire:poll.2s>
    <div class="text-xl text-center">
        壽記追蹤器
        <br>
        <span class="text-xs">更新時間: {{ now()->toDateTimeString() }}</span>
    </div>
    @if($liked_stores->isNotEmpty())
        <x-header class="mb-3 mt-3" :size="'text-xl'">
            <x-slot:title class="text-2xl">
                已置頂
            </x-slot:title>
        </x-header>
        <div class="grid grid-cols-2 gap-2 md:grid-cols-3 md:gap-3 xl:grid-cols-4 xl:gap-4">
            @foreach($liked_stores as $store)
                <livewire:store-card :store="$store" wire:key="{{ $store->id }}_{{ time() }}"></livewire:store-card>
            @endforeach
        </div>
    @endif
    @foreach($store_region as $region => $stores)
        <x-header class="mb-3 mt-3" :size="'text-xl'">
            <x-slot:title class="text-2xl">
                {{ $region }}
            </x-slot:title>
        </x-header>
        <div class="grid grid-cols-2 gap-2 md:grid-cols-3 md:gap-3 xl:grid-cols-4 xl:gap-4">
            @foreach($stores as $store)
                <livewire:store-card :store="$store" wire:key="{{ $store->id }}_{{ time() }}"></livewire:store-card>
            @endforeach
        </div>
    @endforeach
</div>

@script
<script>
    document.addEventListener("visibilitychange", () => {
        if(document.visibilityState === "visible") $wire.$refresh();
    });
</script>
@endscript
