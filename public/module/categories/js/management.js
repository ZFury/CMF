/**
 * Created by babich on 24.11.14.
 */
define(['jquery', 'fury.notify', 'jquery-ui', 'jquery-nestedSortable'], function ($, notify) {
    $(function () {
        function sortableInit() {
            $('.sortable').nestedSortable({
                handle: 'div',
                items: 'li',
                toleranceElement: '> div'
            });
        }

        sortableInit();

        $('body')
            .on('change', '.category-page-wrapper .select-tree select', function () {
                var $this = $(this);
                var $grid = $('[data-spy="grid"]');
                    $.ajax({
                        url: '/categories/management/index/' + $this.val(),
                        type: 'get',
                        dataType: 'html',
                        beforeSend: function () {
                            $grid.find('a, .btn').addClass('disabled');
                        },
                        success: function (html) {
                            $grid.html($(html).children().unwrap());
                            $('body').trigger('grid.loaded', []);
                        }
                    });
            })
            .on('click', '#save', function (e) {
                $('.sortable li').each(function (key, value) {
                    $(this).attr('data-order', key + 1);
                });

                var arraied = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});

                $.ajax({
                    url: '/categories/management/order',
                    type: 'post',
                    success: function (data) {
                        notify.set(data);
                    },
                    dataType: 'json',
                    data: {
                        tree: JSON.stringify(arraied),
                        treeParent: $('.tree-header').attr('data-parent-id')
                    }
                });
            })
            .on('grid.loaded', function () {
                sortableInit();
            });
    });
});