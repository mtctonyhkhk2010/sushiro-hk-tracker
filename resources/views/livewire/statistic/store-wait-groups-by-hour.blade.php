<div x-data="store_wait_group">
    <div class="flex justify-between">
        <div class="font-bold text-sky-300">{{ $this->popularity }}</div>
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
                series: [
                    {
                        name: '輪候人數',
                        data: @js($this->wait_groups_by_hour)
                    }
                ],
                xaxis: {
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
                chart: {
                    height: 350,
                    type: 'bar',
                    toolbar: {
                        show: false
                    },
                },
                plotOptions: {
                    bar: {
                        columnWidth: '100%'
                    }
                },
                colors: ['#00aee3'],
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    markers: {
                        fillColors: ['#00aee3', '#d05da2']
                    }
                },
                fill: {
                    opacity: 1
                }
            },

            init()
            {
                this.show_chart();

                Livewire.on('update_chart', (wait_groups_by_hour) => {
                    this.options.series[0].data = wait_groups_by_hour.value;
                    this.show_chart();
                });
            },

            show_chart()
            {
                const chart = new ApexCharts(document.querySelector("#chart"), this.options);
                chart.render();
            },
        }
    });
</script>
@endscript
