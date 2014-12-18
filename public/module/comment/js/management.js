define(['jquery'], function ($) {
    $('a.del-comment').on('click', function (e) {
        if (confirm('Are you sure you want to delete?')) {
            window.location = $(this).attr("href");
        }
        return false;
    });
    $('a.add-comment, a.edit-comment').on('click', function (e) {
        window.location = $(this).attr('data-href');
        e.preventDefault();
    });
});
