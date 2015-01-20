define(['jquery'], function ($) {
    $('a.del-entity').on('click', function (e) {
        if (confirm('All comments on this entity will also be deleted.\nAre you sure you want to delete?')) {
            window.location = $(this).attr("href");
        }
        return false;
    });
});
