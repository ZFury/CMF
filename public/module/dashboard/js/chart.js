/**
 * Created by babich on 2/23/15.
 */
define(['jquery', 'goog!visualization,1,packages:[corechart]'], function ($) {
    "use strict";
    $(function () {
        var jsonData = $.ajax({
            url: "/dashboard/index/chart",
            dataType: "json",
            async: false
        }).responseText;

        //Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(jsonData);

        var options = {
            width: 1000,
            height: 500,
            vAxis: {
                title: 'Number of users',
                viewWindowMode: 'explicit',
                viewWindow: {
                    min: 0
                }
            },
            hAxis: {
                title: 'Time',
                format: 'MMM d, y'
            },
            pointSize: 7,
            title: 'Registration statistics: users registration in time',
            titleTextStyle: {fontSize: '20'}
        }
        //Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById("registration-chart"));
        chart.draw(data, options);
    });
});