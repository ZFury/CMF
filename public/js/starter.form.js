/**
 * Created by babich on 12/19/14.
 */
define(['jquery', 'notify'], function ($, notify) {
    var modals = [];
    var createModal = function (content, title) {

        var $div = $('<div>', {'class': 'modal fade', 'id': 'formModal'});
        var $divDialog = $('<div>', {'class': 'modal-dialog modal-lg'});
        var $divContent = $('<div>', {'class': 'modal-content'});
        var $divBody = $('<div>', {'class': 'modal-body', 'id': 'popupBody'});
        var $divHeader = $('<div>', {'class': 'modal-header'});
        var $divFooter = $('<div>', {'class': 'modal-footer'});
        var $saveButton = $('<button>', {
            'class': 'btn btn-primary',
            'type': 'button',
            'id': 'submit-form-button'
        });
        var $closeButton = $('<button>', {
            'class': 'btn btn-default',
            'type': 'button',
            'id': 'close-form-button',
            'data-dismiss': 'modal'
        });
        $divBody.append(content);
        $divHeader.html('<button type="button" class="close" data-dismiss="modal">'
            + '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'
            + '<h4 class="modal-title" id="formModalLabel">' + title + '</h4>'
        );
        $saveButton.html('Save');
        $closeButton.html('Close');
        $divFooter.html($saveButton);
        $divFooter.append($closeButton);

        $divContent.html($divHeader);
        $divContent.append($divBody);
        $divContent.append($divFooter);
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

    var actionUrl;
    $('body')
        .on('click.ajax', '.dialog', function (event) {
            var title = $(this).data('title');
            if (!title) {
                title = 'Create/edit';
            }
            actionUrl = $(this).attr('href');
            $.ajax({
                url: actionUrl,
                success: function (data) {
                    createModal(data, title);
                    $('body').trigger('modal.loaded', []);
                }
            });
            event.preventDefault();
        })
        .on('click', '#submit-form-button', function () {
            var formData = $('#crudForm').serializeArray();
            $.ajax({
                url: actionUrl,
                type: 'POST',
                beforeSend: function () {
                    $('.error-form-field').each(function (i, element) {
                        $(element).removeClass('error-form-field');
                    });
                    $('.error-form-message').each(function (i, element) {
                        $(element).html('');
                    });
                },
                success: function (jsonData) {
                    if (!$.isEmptyObject(jsonData.errors)) {
                        for (var key in jsonData.errors) {
                            var field = $('#crudForm .form-group .form-control[name="' + key + '"]');
                            field.addClass('form-control error-form-field');
                            field.parent().next().html(jsonData.errors[key][0]);
                        }
                    } else {
                        $('#close-form-button').trigger('click');
                        jsonData.success.forEach(function (entry) {
                            notify.addSuccess(entry);
                        });
                    }
                },
                data: formData,
                dataType: 'json'
            });
            event.preventDefault();
        })
        .on('hidden.bs.modal', function () {
            $('#content').load(location.pathname, function () {
                $('body').trigger('form.success', []);
                $('body').trigger('grid.reload', []);
                closeModals();
                $('.modal').remove();
            });
        });
});