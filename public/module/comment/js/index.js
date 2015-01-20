define(['jquery'], function ($) {
    $('body').on('click', 'a.del-comment', function (e) {
        if (confirm('Are you sure you want to delete?')) {
            window.location = $(this).attr("href");
        }
        return false;
    });
});
