define(['jquery'], function ($) {
    $('a.del').on('click', function (e) {
        if (confirm('Are you sure you want to delete?')) {
            window.location = $(this).attr("href");;
        }
        return false;
    });
});
