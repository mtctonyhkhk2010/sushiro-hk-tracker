<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Tracker extends Component
{
    #[Computed]
    public function store_region()
    {
        return Store::orderBy('sushiro_store_id')->get()->groupBy('region');
    }

    #[Computed]
    public function liked_stores()
    {
        return Store::whereIn('id', session('liked_stores', []))->get();
    }
}
