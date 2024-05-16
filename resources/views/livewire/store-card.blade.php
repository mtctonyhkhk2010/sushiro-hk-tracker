<div class="card bg-base-100 rounded-lg p-3">
    <a href="/store/{{ $store->id }}" wire:navigate>
        <div>
            <div class="text-base font-bold">
                {{ $store->name }}
            </div>
            <div class="text-gray-500 text-sm mt-1">
                @if($store->status === \App\Enums\StoreStatus::Open)
                    @if(!$store->local_ticketing_status)
                        <x-badge value="已停飛" class="badge-secondary" />
                    @endif
                    @if($store->wait_group)
                        {{ $store->wait_group }}組人等緊
                    @else
                        無人等緊
                    @endif
                @endif
            </div>
            <hr class="my-3" />
            @if($store->status === \App\Enums\StoreStatus::Open)
                @forelse($store->store_queue ?? [] as $number)
                    <x-badge :value="$number" class="badge-neutral" />
                @empty
                    <x-badge value="無人排隊" class="badge-accent" />
                @endforelse
            @else
                <x-badge value="檔已收" class="badge-accent" />
            @endif
        </div>
    </a>
</div>
