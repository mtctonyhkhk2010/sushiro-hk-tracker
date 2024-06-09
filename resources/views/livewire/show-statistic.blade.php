<div>
    <div class="flex">
        <div class="flex-none w-14 h-14 ">
            <button class="btn btn-ghost btn-circle" wire:navigate href="/">
                <x-heroicon-o-arrow-uturn-left class="w-5 h-5" />
            </button>
        </div>
        <div class="text-xl text-center grow">
            統計數據
        </div>
        <div class="flex-none w-14 h-14 ">

        </div>
    </div>
    <livewire:statistic.least-popular-stores></livewire:statistic.least-popular-stores>
    <livewire:statistic.most-popular-stores></livewire:statistic.most-popular-stores>
</div>
