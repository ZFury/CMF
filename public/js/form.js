/**
 * Created by babich on 12/19/14.
 */
define(['jquery', 'tmpl',
    'iframe-transport',
    'fileupload-ui'], function ($) {
    var actionUrl;
    $('body')
        .on('click.ajax', '.dialog', function (event) {
            $('#formModalLabel').html($(this).data('action'));
            var id = $(this).data('id');
            actionUrl = $(this).data('url');
            $.ajax({
                url: actionUrl,
                dataType: 'html',
                success: function (data) {
                    $('#popupBody').html(data);
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
                        location.reload();
                    }
                },
                data: formData,
                dataType: 'json'
            });
            event.preventDefault();
        });
});