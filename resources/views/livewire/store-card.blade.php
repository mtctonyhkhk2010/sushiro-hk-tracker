<x-card shadow separator>
    <x-slot:title class="text-base">
        {{ $store->name }}
    </x-slot:title>
    @if($store->status === \App\Enums\StoreStatus::Open)
        open
    @else
        closed
    @endif
</x-card>
