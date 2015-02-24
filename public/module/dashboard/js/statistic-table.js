/**
 * Created by babich on 2/23/15.
 */
define(['jquery', 'goog!visualization,1,packages:[table]'], function ($) {
    "use strict";
    $(function () {
        var jsonData = $.ajax({
            url: "/dashboard/index/table",
            dataType: "json",
            async: false
        }).responseText;

        //Create our data table out of JSON data loaded from server.
        var data = new google.visualization.DataTable(jsonData);

        var options = {
            width: 1000,
            height: 200
        }
        //Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.Table(document.getElementById("last-registrations"));
        chart.draw(data, options);
    });
});