<?php

namespace App\Livewire\Statistic;

use App\Models\Record;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StoreWaitGroupsByHour extends Component
{
    public $store;

    public $wait_group;

    public $day_of_week;

    public function mount()
    {
        $this->day_of_week = date('w');
    }

    public function render()
    {
        return view('livewire.statistic.store-wait-groups-by-hour');
    }

    public function get_day_of_week_name($day_of_week)
    {
        setlocale(LC_TIME,  'zh-Hant-TW');
        return Carbon::create('Sunday +' . $day_of_week . ' days')->localeDayOfWeek;
    }

    public function update_day_of_week($day_of_week)
    {
        $this->day_of_week = $day_of_week;
        $record = $this->wait_groups_by_hour();
        $this->dispatch('update_chart', value: $record);
    }

    #[Computed]
    public function popularity()
    {
        $t_wait_group = ($this->wait_groups_by_hour()->where('hour', now()->hour)->first()?->t_wait_group ?? 0) / 4;

        if ($t_wait_group > $this->wait_group + 5) return '比平時少人';
        if (abs($t_wait_group - $this->wait_group) <= 5) return '同平時差唔多人';
        if ($t_wait_group < $this->wait_group - 5) return '比平時多人';
    }

    #[Computed]
    public function wait_groups_by_hour()
    {
        //get the last 4 dates of same day of week
        $dates = [];
        $date = now()->subDay();
        while (count($dates) < 4) {
            if ($date->dayOfWeek() == $this->day_of_week) $dates[] = $date->toDateString();
            $date = $date->subDay();
        }

        $records = Cache::remember('1wait_groups_by_hour_'. $this->day_of_week . '_' . $this->store->id, 60 * 60 * 12, function () use ($dates) {
            return Record::where('store_id', $this->store->id)
                ->whereIn(DB::raw('DATE(created_at)'), $dates)
                ->whereRaw('HOUR(created_at) BETWEEN 10 AND 22')
                ->whereRaw('MINUTE(created_at) BETWEEN 0 AND 5')
                ->select([DB::raw('SUM(wait_group) as t_wait_group'), DB::raw('HOUR(created_at) as hour')])
                ->groupBy('hour')
                ->get();
        });

        foreach ($records as $record)
        {
            $record->x = "$record->hour";
            $record->y = round($record->t_wait_group / 4);
            if ($record->hour == now()->hour)
            {
                $record->fillColor = '#EB8C87';
                $record->goals = [
                    'name' => 'Now',
                    'value' => $this->wait_group,
                    'strokeHeight' => 2,
                    'strokeColor' => '#775DD0'
                ];
            }
        }

        return $records;
    }
}
