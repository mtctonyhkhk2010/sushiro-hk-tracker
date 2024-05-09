<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Component;

class Tracker extends Component
{
    public function render()
    {
        $store_region = Store::all()->groupBy('region');
        $liked_stores = Store::whereIn('id', session('liked_stores', []))->get();
        return view('livewire.tracker', compact('store_region', 'liked_stores'));
    }
}
