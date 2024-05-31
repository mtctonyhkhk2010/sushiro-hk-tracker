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

    #[Computed]
    public function least_popular_stores()
    {
         return Store::addSelect(['t_wait_group' => Record::select(DB::raw('SUM(wait_group) as t_wait_group'))
             ->whereColumn('store_id', 'stores.id')
             ->whereRaw("DATE(created_at) BETWEEN '" . now()->subDays(8)->toDateString(). "' AND '" . now()->subDays(1)->toDateString(). "'")
             ->whereRaw('HOUR(created_at) = ?', [19])
         ])->orderBy('t_wait_group', 'asc')
             ->take(5)
             ->get();
    }

    #[Computed]
    public function most_popular_stores()
    {
        return Store::addSelect(['t_wait_group' => Record::select(DB::raw('SUM(wait_group) as t_wait_group'))
            ->whereColumn('store_id', 'stores.id')
            ->whereRaw("DATE(created_at) BETWEEN '" . now()->subDays(8)->toDateString(). "' AND '" . now()->subDays(1)->toDateString(). "'")
            ->whereRaw('HOUR(created_at) = ?', [19])
        ])->orderBy('t_wait_group', 'desc')
            ->take(5)
            ->get();
    }
}
