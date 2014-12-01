/**
 * Created by babich on 24.11.14.
 */
define(['jquery', 'jquery-ui', 'jquery-nestedSortable'], function ($) {

    $('.sortable').nestedSortable({
        handle: 'div',
        items: 'li',
        toleranceElement: '> div'
    });

    $('.category-page-wrapper').on('change', '.select-tree select', function () {
        var $this = $(this);
        window.location = '/categories/management/index/' + $this.val();
    }).on('click', '#save', function (e) {

        $('.sortable li').each(function (key, value) {
            $(this).attr('data-order', key + 1);
        });

        var arraied = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});

        $.ajax({
            url: '/categories/management/order',
            type: 'post',
            success: function (data) {
                window.location = '/categories/management/index';
            },
            dataType: 'json',
            data: {
                tree: JSON.stringify(arraied),
                treeParent: $('.tree-header').attr('data-parent-id')
            }
        });
    });
});