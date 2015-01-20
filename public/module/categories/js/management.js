/**
 * Created by babich on 24.11.14.
 */
define(['jquery', 'notify', 'jquery-ui', 'jquery-nestedSortable'], function ($, notify) {
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
                $('#content').load('/categories/management/index/' + $this.val(), function () {
                    $('body').trigger('form.success', []);
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
            .on('click', '.tree-container .btn.delete-entity', function (e) {
                if (confirm('Are you sure you want to delete?')) {
                    $.get($(this).attr("href"), [], function () {
                        $('#content').load(location.pathname, function () {
                            $('body').trigger('form.success', []);
                        });
                    }, 'json');
                }
                return false;
            })
            .on('form.success', function () {
                sortableInit();
            });
    });
});