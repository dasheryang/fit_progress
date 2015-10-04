<?php


function vals_arr_to_js_str( $vals_arr, $is_string = false ){
    $str = '';
    if( empty( $vals_arr ) ){
        return $str;
    }

    foreach ( $vals_arr as $val ){
        if( !empty( $str ) ){
            $str .= ' ,';
        }

        if($is_string){
            $str .= "'{$val}'";
        }else{
            $str .= "{$val}";
        }

    }

    $str = "[{$str}]";
    return $str;
}

function dump_home_page_html( $date_spots, $weight_vals, $avg_weight_vals, $energy_vals ){
    $page_html = '';

    $date_str = vals_arr_to_js_str( $date_spots, true );
    $weight_str = vals_arr_to_js_str( $weight_vals );
    $avg_weight_vals = vals_arr_to_js_str( $avg_weight_vals );
    $energy_vals = vals_arr_to_js_str( $energy_vals );

    $page_html =<<<EOF
<html>
<body>
    <div id="container" style="width:100%; height:400px;">
    </div>
</body>

<script src="/res/js/jquery.min.js"></script>
<script src="/res/js/highcharts.js"></script>

<script>
setTimeout(function(){
//                location.reload();
           }, 10000);
</script>

<script>
$(function () {
    $('#container').highcharts({
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: 'body weight record column'
        },
        xAxis: [{
            categories: {$date_str},
            crosshair: true
        }],
        yAxis: [{ // Weight 5day Avg
            labels: {
                format: '{value} Kg',
                style: {
                    color: Highcharts.getOptions().colors[2]
                }
            },
            title: {
                text: 'Weight 5day Avg',
                style: {
                    color: Highcharts.getOptions().colors[2]
                }
            },
            min: 72.0,
            max: 76.0,
            opposite: true

        }, { //
            gridLineWidth: 0,
            title: {
                text: 'Energy Lost',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value} Kcal',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            }

        }, { // Tertiary yAxis
            gridLineWidth: 0,
            title: {
                text: 'Weight by day',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            labels: {
                format: '{value} Kg',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            min: 72.0,
            max: 76.0,
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 80,
            verticalAlign: 'top',
            y: 55,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: [{
            name: 'Energy Lost',
            type: 'column',
            yAxis: 1,
            data: {$energy_vals},
            tooltip: {
                valueSuffix: ' Kcal'
            }

        }, {
            name: 'Weight by Day',
            type: 'spline',
            yAxis: 2,
            data: {$weight_str},
            marker: {
                enabled: false
            },
            dashStyle: 'shortdot',
            tooltip: {
                valueSuffix: ' Kg'
            }

        }, {
            name: 'Weight 5 Days Avg',
            type: 'spline',
            data: {$avg_weight_vals},
            tooltip: {
                valueSuffix: ' Kg'
            }
        }]
    });
});
</script>
</html>
EOF;
    return $page_html;
}

function parse_init_data(){
    $data_set = array();
    $data_set[] = ['2015-09-21', 75.6, 350];
    $data_set[] = ['2015-09-22', 75.5, 340];
    $data_set[] = ['2015-09-23', 74.1, 1000];
    $data_set[] = ['2015-09-24', 74.3, 500];
    $data_set[] = ['2015-09-25', 73.9, 850];
    $data_set[] = ['2015-09-26', 74.3, 700];
    $data_set[] = ['2015-09-27', 74.4, 350];
    $data_set[] = ['2015-09-28', 73.7, 800];
    $data_set[] = ['2015-09-29', 73.8, 550];
    $data_set[] = ['2015-09-30', 74.6, 100];
    $data_set[] = ['2015-10-01', 74.0, 800];
    $data_set[] = ['2015-10-02', 73.5, 700];
    $data_set[] = ['2015-10-03', 74.0, 550];

    return $data_set;
}

function get_avg_weight( $data_set, $index ,$scale = 5 ){
    $cur_weight = $data_set[$index];

    $sum_weight = 0;
    for( $i = $index; $i > ($index - $scale) ;--$i ){
        if( $i < 0 || empty($data_set[$i]) ){
            $sum_weight += $cur_weight;
        }
        else{
            $sum_weight += $data_set[$i];
        }
    }

    $avg_weight = floatval($sum_weight)/ floatval( $scale );
    return $avg_weight;
}


function main(){
    $date_spots = $weight_vals = $avg_weight_vals = $energy_vals = [];

    $data_set = parse_init_data();

    $sum_energy = 0;
    if( !empty( $data_set ) ){
        foreach( $data_set as $index => $record ){
            $date_spots[] = $record[0];
            $weight_vals[] = $record[1];

            $sum_energy += $record[2];
            $energy_vals[] = $sum_energy;

            $avg_weight_vals[] = get_avg_weight( $weight_vals, $index );


        }
    }


    echo dump_home_page_html( $date_spots, $weight_vals, $avg_weight_vals, $energy_vals );
}

main();

