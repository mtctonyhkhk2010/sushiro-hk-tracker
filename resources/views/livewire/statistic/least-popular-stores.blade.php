<x-card title="最少人分店" subtitle="以最近14天數據計算" shadow separator class="mb-3">
    <x-slot:menu>
        <x-dropdown label="{{ $time }}" class="btn-warning btn-sm" right>
            <x-menu-item title="全日" wire:click="$set('time', '全日')" />
            <x-menu-item title="午市" wire:click="$set('time', '午市')" />
            <x-menu-item title="晚市" wire:click="$set('time', '晚市')" />
        </x-dropdown>
    </x-slot:menu>
    @foreach($this->least_popular_stores as $store)
        <x-list-item :item="$store" link="/store/{{$store->id}}" :no-separator="$loop->last" >
            <x-slot:value>
                {{ $loop->iteration }}. {{ $store->name }}
            </x-slot:value>
        </x-list-item>
    @endforeach
</x-card>
