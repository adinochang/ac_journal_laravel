@extends('layout')

@section('pagejs')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
@endsection



@section('pagecss')
    <style>
        .highcharts-figure, .highcharts-data-table table {
            min-width: 360px;
            max-width: 800px;
            margin: 1em auto;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #EBEBEB;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }
        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }
        .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
            padding: 0.5em;
        }
        .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }
        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>
@endsection



@section('page_title')
    <h2>Reports</h2>
@endsection



@section('content')
    <section id="report-section" class="wrapper">
        <div class="container">
            <figure class="highcharts-figure">
                <div id="chart_1"></div>
                <p class="highcharts-description"></p>
            </figure>
        </div>
    </section>

    <section id="report-section" class="wrapper">
        <div class="container">
            <figure class="highcharts-figure">
                <div id="chart_2"></div>
                <p class="highcharts-description"></p>
            </figure>
        </div>
    </section>

    <script>
        var monthly_entries = JSON.parse('@json($monthly_entries)');
        var average_words = JSON.parse('@json($average_words)');

        Highcharts.chart('chart_1', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Monthly Number of Entries'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: Object.keys(monthly_entries)
            },
            yAxis: {
                title: {
                    text: 'Count'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: 'Number of Entries',
                data: Object.values(monthly_entries)
            }]
        });

        Highcharts.chart('chart_2', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthly Average Words Per Post'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: Object.keys(average_words),
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Words'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} words</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Words per entry',
                data: Object.values(average_words)
            }]
        });
    </script>
@endsection
