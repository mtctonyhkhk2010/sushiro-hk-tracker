<div wire:poll.60s>
    <div class="flex">
        <div class="flex-none w-14 h-14 ">

        </div>
        <div class="text-xl text-center grow">
            壽記追蹤器
            <br>
            <span class="text-xs">更新時間: {{ now()->toDateTimeString() }}</span>
        </div>
        <div class="flex-none w-14 h-14 ">
            <button class="btn btn-ghost btn-circle" wire:navigate href="/statistic">
                <x-heroicon-o-calculator class="w-5 h-5" />
            </button>
        </div>
    </div>
    @if($this->liked_stores->isNotEmpty())
        <x-header class="!mb-3 mt-3" :size="'text-xl'">
            <x-slot:title class="text-2xl">
                已置頂
            </x-slot:title>
        </x-header>
        <div class="grid grid-cols-2 gap-2 md:grid-cols-3 md:gap-3 xl:grid-cols-4 xl:gap-4">
            @foreach($this->liked_stores as $store)
                <livewire:store-card :store="$store" wire:key="{{ $store->id }}_{{ time() }}"></livewire:store-card>
            @endforeach
        </div>
    @endif
    @foreach($this->store_region as $region => $stores)
        <x-header class="!mb-3 mt-3" :size="'text-xl'">
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

    navigator.serviceWorker.register("sw.js");
</script>
@endscript
