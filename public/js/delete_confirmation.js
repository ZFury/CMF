/**
 * Created by babich on 22.11.14.
 */
define(['jquery'], function ($) {
    $('.container').on('click', '.btn.delete', function (e) {
        var path = $(this).attr("href");
        if (confirm('Are you sure you want to delete?')) {
            window.location = path;
        }
        return false;
    });
});