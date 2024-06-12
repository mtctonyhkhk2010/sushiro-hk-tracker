<div>
    <div class="flex justify-between">
        <div></div>
        <x-dropdown label="{{ $this->get_day_of_week_name($day_of_week) }}" class="btn-warning btn-sm" right>
            @for($i = 0; $i < 7; $i++)
                <x-menu-item :title="$this->get_day_of_week_name($i)" wire:click="$set('day_of_week', '{{ $i }}')" />
            @endfor
        </x-dropdown>
    </div>

    <div id="chart"></div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endassets

@script
<script>
    const options = {
        series: [{
            name: '輪候人數',
            data: @js($this->wait_groups_by_hour->pluck('t_wait_group')->map(function ($value) {return round($value/4);}))
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '100%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: @js($this->wait_groups_by_hour->pluck('hour')),
            title: {
                text: '時間',
                style: {
                    color: '#a6adbb',
                }
            },
            labels: {
                style: {
                    colors: '#a6adbb',
                },
            }
        },
        yaxis: {
            title: {
                text: '人數',
                style: {
                    color: '#a6adbb',
                }
            },
            labels: {
                style: {
                    colors: '#a6adbb',
                },
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>
@endscript
