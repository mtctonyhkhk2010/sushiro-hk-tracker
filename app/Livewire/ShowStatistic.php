<?php

namespace App\Livewire;

use App\Models\Record;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShowStatistic extends Component
{
    public function render()
    {
        return view('livewire.show-statistic');
    }
}
