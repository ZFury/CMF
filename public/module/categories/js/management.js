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
                if (data.success == true) {
                    var html = '<div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>Order has been successfully saved!</div>';
                    $('#dialog').html(html);
                } else {
                    var html = '<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a>Order has been failed!</div>';
                    $('#dialog').html(html);
                }
            },
            dataType: 'json',
            data: {
                tree: JSON.stringify(arraied),
                treeParent: $('.tree-header').attr('data-parent-id')
            }
        });
    });
    $('.tree-container').on('click', '.btn.delete-category', function (e) {
        var path = $(this).attr("href");
        if (confirm('Are you sure you want to delete?')) {
            window.location = path;
        }
        return false;
    });
    var actionUrl;
    $('.category-page-wrapper').on('click', '.addAjax', function () {
        actionUrl = $(this).data('url');
        $("#myModalLabel").html($(this).data('action'));
        $("#popupBody").load(actionUrl);
    });

    var modals = [];
    var createModal = function (content, style) {

        var $div = $('<div>', {'class':'modal fade'});
        var $divDialog = $('<div>', {'class':'modal-dialog', 'style':style});
        var $divContent = $('<div>', {'class':'modal-content'});

        $divContent.html(content);
        $divDialog.append($divContent);
        $div.append($divDialog);
        $div.modal();

        modals.push($div);

        return $div;
    };
    var closeModals = function () {
        for (var i = 0; i < modals.length; i++) {
            modals[i].modal('hide');
            modals[i].data('modal', null);
        }
        modals = [];
    };

    $('.container-fluid').on('click', '#submit-form-button', function () {
        //var formData = new FormData($('#crudForm')[0]);
        var formData = $('#crudForm').serializeArray();
        //console.log(createModal('<p>a</p>'));
        $.ajax({
            url: actionUrl,
            type: 'POST',
            success: function (jsonData) {
                console.log(jsonData.errors);//[0].errors[0]
                //if (!jsonData.result) {
                //    $(".exception-box").html(jsonData.exception);
                //    $(".exception-box").css("display", "block");
                //} else {
                //    $(".exception-box").html('');
                //    $('#close-form-button').trigger('click');
                //    location.reload();
                //}
            },
            data: formData,
            dataType: 'json'
            //Options to tell jQuery not to process data or worry about content-type.
            //contentType: false,
            //processData: false
        });
        event.preventDefault();
    });

});