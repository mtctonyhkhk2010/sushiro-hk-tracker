<?php

namespace App\Livewire\Statistic;

use App\Models\Record;
use App\Models\Store;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class LeastPopularStores extends Component
{
    public $time = '全日';

    public function render()
    {
        return view('livewire.statistic.least-popular-stores');
    }

    #[Computed]
    public function least_popular_stores()
    {
        $hour = [];
        if ($this->time == '全日') $hour = [13,19];
        if ($this->time == '午市') $hour = [13];
        if ($this->time == '晚市') $hour = [19];

//        return Cache::remember('least-popular-stores', 60 * 60 * 6, function () use ($hour) {
            return Store::addSelect(['t_wait_group' => Record::select(DB::raw('SUM(wait_group) as t_wait_group'))
                ->whereColumn('store_id', 'stores.id')
                ->whereRaw("DATE(created_at) BETWEEN '" . now()->subDays(15)->toDateString() . "' AND '" . now()->subDays(1)->toDateString() . "'")
                ->whereRaw('HOUR(created_at) in (' . implode(',', $hour) . ')')
            ])->orderBy('t_wait_group', 'asc')
                ->take(5)
                ->get()
                ->map->only(['id', 'name', 't_wait_group']);
//        });
    }
}
