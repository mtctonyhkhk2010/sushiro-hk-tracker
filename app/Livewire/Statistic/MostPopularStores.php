<?php

namespace App\Livewire\Statistic;

use App\Models\Record;
use App\Models\Store;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MostPopularStores extends Component
{
    public $time = '全日';

    public function render()
    {
        return view('livewire.statistic.most-popular-stores');
    }

    #[Computed]
    public function most_popular_stores()
    {
        $hour = [];
        if ($this->time == '全日') $hour = [13,19];
        if ($this->time == '午市') $hour = [13];
        if ($this->time == '晚市') $hour = [19];

//        return Cache::remember('most-popular-stores', 60 * 60 * 6, function () use ($hour) {
            return Store::addSelect(['t_wait_group' => Record::select(DB::raw('SUM(wait_group) as t_wait_group'))
                ->whereColumn('store_id', 'stores.id')
                ->whereRaw("DATE(created_at) BETWEEN '" . now()->subDays(15)->toDateString() . "' AND '" . now()->subDays(1)->toDateString() . "'")
                ->whereRaw('HOUR(created_at) in (' . implode(',', $hour) . ')')
            ])->orderBy('t_wait_group', 'desc')
                ->take(5)
                ->select(['id', 'name', 't_wait_group'])
                ->get();
//        });
    }
}
