<?php

namespace App\Livewire\Statistic;

use App\Models\Record;
use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;
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
    public function least_popular_stores(): Collection
    {
        $hours = match ($this->time) {
            '午市' => [13],
            '晚市' => [19],
            default => [13, 19],
        };
        $today = now()->toImmutable()->startOfDay();
        $cacheKey = 'statistics:least-popular-stores:'.$today->toDateString().':'.implode(',', $hours);

        return Cache::remember($cacheKey, $today->addDay(), fn (): Collection => Store::query()->select(['stores.id', 'stores.name'])->addSelect([
            't_wait_group' => Record::select(DB::raw('SUM(wait_group) as t_wait_group'))
                ->whereColumn('store_id', 'stores.id')
                ->whereBetween('created_at', [$today->subDays(15), $today->subDay()->endOfDay()])
                ->whereIn(DB::raw('HOUR(created_at)'), $hours),
        ])->orderBy('t_wait_group', 'asc')
            ->take(5)
            ->get());
    }
}
