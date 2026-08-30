<div wire:poll.10s x-data="storeSorting">
    <div class="flex">
        <div class="h-14 w-28 flex-none">

        </div>
        <div class="text-xl text-center grow">
            壽記追蹤器
            <br>
            <span class="text-xs">更新時間: {{ now()->toDateTimeString() }}</span>
        </div>
        <div class="flex h-14 w-28 flex-none justify-end">
            <x-dropdown
                icon="o-arrows-up-down"
                class="btn-ghost btn-circle"
                right
                aria-label="選擇分店排序方式"
                title="分店排序"
                data-testid="store-sort-menu"
            >
                <x-menu-item
                    title="名稱排序"
                    icon="o-bars-arrow-down"
                    :active="$sortMethod === 'alphabetical'"
                    wire:click="sortAlphabetically"
                    data-testid="sort-alphabetically"
                />
                <x-menu-item
                    title="距離排序"
                    icon="o-map-pin"
                    :active="$sortMethod === 'distance'"
                    x-on:click="$wire.locationModal = true"
                    data-testid="sort-by-distance"
                />
            </x-dropdown>
            <button class="btn btn-ghost btn-circle" wire:navigate href="/statistic" aria-label="查看統計數據">
                <x-heroicon-o-calculator class="w-5 h-5" />
            </button>
        </div>
    </div>

    <x-modal
        wire:model="locationModal"
        title="使用目前位置排序？"
        subtitle="我們需要你允許位置存取"
        separator
    >
        <p>確認後，瀏覽器會顯示原生位置權限視窗。你的位置只會用來計算分店距離，並只會在今次瀏覽階段保留。</p>

        <x-slot:actions>
            <x-button label="取消" x-on:click="$wire.locationModal = false" data-testid="cancel-location" />
            <x-button label="確認" class="btn-primary" x-on:click="requestLocation" data-testid="confirm-location" />
        </x-slot:actions>
    </x-modal>

    <div
        x-show="locationError"
        x-transition
        style="display: none"
        role="alert"
        class="alert alert-warning mb-3"
    >
        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
        <span x-text="locationError"></span>
        <button type="button" class="btn btn-ghost btn-sm" x-on:click="locationError = null">關閉</button>
    </div>

    @if($this->liked_stores->isNotEmpty())
        <x-header class="!mb-3 mt-3" :size="'text-xl'">
            <x-slot:title class="text-2xl">
                已置頂
            </x-slot:title>
        </x-header>
        <div class="grid grid-cols-2 gap-2 md:grid-cols-3 md:gap-3 xl:grid-cols-4 xl:gap-4">
            @foreach($this->liked_stores as $store)
                <livewire:store-card :store="$store" wire:key="liked-store-{{ $store->id }}"></livewire:store-card>
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
                <livewire:store-card :store="$store" wire:key="region-store-{{ $store->id }}"></livewire:store-card>
            @endforeach
        </div>
    @endforeach
</div>

@script
<script>
    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "visible") $wire.$refresh();
    });

    navigator.serviceWorker.register("sw.js");

    Alpine.data('storeSorting', () => ({
        locationError: null,

        requestLocation() {
            $wire.locationModal = false;
            this.locationError = null;

            if (!navigator.geolocation) {
                this.locationError = '你的瀏覽器不支援位置服務。';

                return;
            }

            navigator.geolocation.getCurrentPosition(
                ({ coords }) => {
                    $wire.sortByDistance(coords.latitude, coords.longitude)
                        .catch(() => this.locationError = '未能按距離排序，請再試一次。');
                },
                ({ code }) => {
                    this.locationError = code === 1
                        ? '你取消或拒絕了位置權限，排序方式沒有更改。'
                        : '未能取得你的位置，請再試一次。';
                },
                {
                    enableHighAccuracy: false,
                    timeout: 10000,
                    maximumAge: 300000,
                },
            );
        },
    }));
</script>
@endscript
