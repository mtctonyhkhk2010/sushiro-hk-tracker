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
    <x-card title="最少人分店" shadow separator class="mb-3">
        @foreach($this->least_popular_stores as $store)
            <x-list-item :item="$store" link="/store/{{$store->id}}" :no-separator="$loop->last" >
                <x-slot:value>
                    {{ $loop->iteration }}. {{ $store->name }}
                </x-slot:value>
            </x-list-item>
        @endforeach
    </x-card>
    <x-card title="最多人分店" shadow separator class="mb-3">
        @foreach($this->most_popular_stores as $store)
            <x-list-item :item="$store" link="/store/{{$store->id}}" :no-separator="$loop->last" >
                <x-slot:value>
                    {{ $loop->iteration }}. {{ $store->name }}
                </x-slot:value>
            </x-list-item>
        @endforeach
    </x-card>
</div>
