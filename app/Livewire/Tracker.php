<?php

namespace App\Livewire;

use App\Models\Store;
use Collator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Session;
use Livewire\Component;

class Tracker extends Component
{
    #[Locked]
    #[Session]
    public string $sortMethod = 'alphabetical';

    #[Locked]
    #[Session]
    public ?float $latitude = null;

    #[Locked]
    #[Session]
    public ?float $longitude = null;

    public bool $locationModal = false;

    #[Computed]
    public function store_region(): Collection
    {
        $groups = Store::orderBy('sushiro_store_id')
            ->get()
            ->groupBy('region')
            ->map(fn (Collection $stores) => $this->sortStores($stores));

        if ($this->sortMethod !== 'distance' || $this->latitude === null || $this->longitude === null) {
            return $groups;
        }

        return $groups->sortBy(
            fn (Collection $stores): float => $this->distanceFrom($stores->first())
        );
    }

    #[Computed]
    public function liked_stores(): Collection
    {
        return $this->sortStores(Store::whereIn('id', session('liked_stores', []))->get());
    }

    public function sortAlphabetically(): void
    {
        $this->sortMethod = 'alphabetical';
        $this->latitude = null;
        $this->longitude = null;
    }

    public function sortByDistance(mixed $latitude, mixed $longitude): void
    {
        $coordinates = validator([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ])->validate();

        $this->latitude = (float) $coordinates['latitude'];
        $this->longitude = (float) $coordinates['longitude'];
        $this->sortMethod = 'distance';
    }

    /**
     * @param  Collection<int, Store>  $stores
     * @return Collection<int, Store>
     */
    private function sortStores(Collection $stores): Collection
    {
        if ($this->sortMethod === 'distance' && $this->latitude !== null && $this->longitude !== null) {
            return $stores->sortBy(fn (Store $store): float => $this->distanceFrom($store));
        }

        $collator = new Collator(app()->getLocale());

        return $stores->sort(
            fn (Store $first, Store $second): int => (int) $collator->compare($first->name, $second->name)
        );
    }

    private function distanceFrom(Store $store): float
    {
        $latitude = deg2rad($this->latitude);
        $storeLatitude = deg2rad($store->location->latitude);
        $latitudeDelta = $storeLatitude - $latitude;
        $longitudeDelta = deg2rad($store->location->longitude - $this->longitude);

        $a = sin($latitudeDelta / 2) ** 2
            + cos($latitude) * cos($storeLatitude) * sin($longitudeDelta / 2) ** 2;
        $a = min(1, max(0, $a));

        return 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
