<div x-data="store_wait_group">
    <div class="flex justify-between">
        <div></div>
        <x-dropdown label="{{ $this->get_day_of_week_name($day_of_week) }}" class="btn-warning btn-sm" right>
            @for($i = 0; $i < 7; $i++)
                <x-menu-item :title="$this->get_day_of_week_name($i)" wire:click="update_day_of_week({{ $i }})" />
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
    Alpine.data('store_wait_group', () => {
        return {
            options: {
                series: [{
                    name: '輪候人數' + $wire.$get('day_of_week'),
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
            },

            init()
            {

                this.show_chart();

                Livewire.on('update_chart', (wait_groups_by_hour) => {
                    console.log('update_chart');
                    this.options.series[0].data = wait_groups_by_hour.value;
                    this.show_chart();
                });
            },

            show_chart()
            {
                const chart = new ApexCharts(document.querySelector("#chart"), this.options);
                chart.render();

                document.addEventListener('livewire:navigating', () => {
                    console.log('destroy');
                    chart.destroy();
                })
            },
        }
    });
</script>
@endscript
