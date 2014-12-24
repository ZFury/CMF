define(['jquery'], function ($) {
    "use strict";
    $(function () {
        $('[data-spy="grid"]').each(function () {
            var $grid = $(this);
            if (!$grid.data('url')) {
                $grid.data('url', window.location.pathname);
            }

            $grid.on('click', '.pagination li a, thead a', function () {
                var $link = $(this),
                    href = $link.attr('href');

                if (href === '#') {
                    return false;
                }

                $.ajax({
                    url: href,
                    type: 'get',
                    dataType: 'html',
                    beforeSend: function () {
                        $link.addClass('active');
                        $grid.find('a, .btn').addClass('disabled');
                    },
                    success: function (html) {
                        $grid.data('url', href);
                        $grid.html($(html).children().unwrap());
                    }
                });
                return false;
            });
        });


    });
});
