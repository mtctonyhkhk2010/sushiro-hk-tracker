<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Component;

class Tracker extends Component
{
    public function render()
    {
        $store_region = Store::all()->groupBy('region');
        return view('livewire.tracker', compact('store_region'));
    }
}
