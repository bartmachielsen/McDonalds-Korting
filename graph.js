function getQueryParams(qs) {
    qs = qs.split('+').join(' ');

    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}


function draw_Chart(data){
    var chartData = [];
    var last_diff = -1;
    var index = 0;
    data.forEach(function(element) {
        if(last_diff === -1){
            last_diff = element.DAY_DIFF;
        }
        chartData.push({
            "date":element.END,
            "visits":last_diff-element.DAY_DIFF
        })
        last_diff = element.DAY_DIFF;
        index++;
    }, this);

    console.log(chartData);

    var chart = AmCharts.makeChart("chartdiv", {
        "type": "serial",
        "theme": "light",
        "marginRight": 80,
        "autoMarginOffset": 20,
        "marginTop": 7,
        "dataProvider": chartData,
        "valueAxes": [{
            "axisAlpha": 0.2,
            "dashLength": 1,
            "position": "left"
        }],
        "mouseWheelZoomEnabled": true,
        "graphs": [{
            "id": "g1",
            "balloonText": "[[value]]",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "hideBulletsCount": 50,
            "title": "red line",
            "valueField": "visits",
            "useLineColorForBulletBorder": true,
            "balloon":{
                "drop":true
            }
        }],
        "chartScrollbar": {
            "autoGridCount": true,
            "graph": "g1",
            "scrollbarHeight": 40
        },
        "chartCursor": {
           "limitToGraph":"g1"
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": true,
            "axisColor": "#DADADA",
            "dashLength": 1,
            "minorGridEnabled": true
        },
        "export": {
            "enabled": true
        },
        "responsive": {
          "enabled": true
        }
    });
    
    chart.addListener("rendered", zoomChart);
    zoomChart();
    
    // this method is called when chart is first inited as we listen for "rendered" event
    function zoomChart() {
        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
        chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
    }
}

$(document).ready(function() {
    var query = getQueryParams(document.location.search);
    $.ajax({
        url: "http://mcdonalds-korting.nl/service/expected.php?code="+query.code
    }).then(function(data) {
        draw_Chart(data);
    });
});
