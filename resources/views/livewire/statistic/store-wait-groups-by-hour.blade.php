<div id="chart"></div>

@assets
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endassets

@script
<script>
    const options = {
        series: [{
            name: '輪候人數',
            data: @js($this->wait_groups_by_hour->pluck('t_wait_group')->map(function ($value) {return round($value/14);}))
        }],
        chart: {
            type: 'bar',
            height: 350
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
