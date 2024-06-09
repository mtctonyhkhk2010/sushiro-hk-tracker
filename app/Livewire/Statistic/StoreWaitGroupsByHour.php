<?php

namespace App\Livewire\Statistic;

use App\Models\Record;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StoreWaitGroupsByHour extends Component
{
    public $store;

    public function render()
    {
        return view('livewire.statistic.store-wait-groups-by-hour');
    }

    #[Computed]
    public function wait_groups_by_hour()
    {
        return Record::where('store_id', $this->store->id)
            ->whereRaw("DATE(created_at) BETWEEN '" . now()->subDays(15)->toDateString(). "' AND '" . now()->subDays(1)->toDateString(). "'")
            ->whereRaw('HOUR(created_at) BETWEEN 10 AND 22')
            ->whereRaw('MINUTE(created_at) BETWEEN 0 AND 5')
            ->select([DB::raw('SUM(wait_group) as t_wait_group'), DB::raw('HOUR(created_at) as hour')])
            ->groupBy('hour')
            ->get();
    }
}
