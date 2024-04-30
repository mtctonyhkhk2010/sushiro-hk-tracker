<x-card class="!p-3" shadow separator>
    <x-slot:title class="text-base">
        {{ $store->name }}
    </x-slot:title>
    <x-slot:subtitle>
        @if($store->wait_group)
            {{ $store->wait_group }}組人等緊
        @else
            無人等緊
        @endif
    </x-slot:subtitle>
    @if($store->status === \App\Enums\StoreStatus::Open)
        @forelse($store->store_queue as $number)
            <x-badge :value="$number" class="badge-neutral" />
        @empty
            <x-badge value="無人排隊" class="badge-accent" />
        @endforelse
    @else
        <x-badge value="檔已收" class="badge-accent" />
    @endif
</x-card>
