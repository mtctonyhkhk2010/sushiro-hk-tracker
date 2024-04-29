<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Component;

class StoreCard extends Component
{
    public Store $store;

    public function render()
    {
        return view('livewire.store-card');
    }
}
