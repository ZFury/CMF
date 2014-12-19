/**
 * Created by babich on 12/19/14.
 */
define(['jquery', 'tmpl',
    'iframe-transport',
    'fileupload-ui'], function ($) {
    var actionUrl;
    var counter = 1;
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
                },
                complete: function () {
                    if (counter > 1) {
                        'use strict';
                        var idString = '';
                        if (id)
                            idString = '/' + id;
                        // Initialize the jQuery File Upload widget:
                        $('#fileupload').fileupload({
                            url: 'categories/management/start-image-upload' + idString
                        });
                        // Enable iframe cross-domain access via redirect option:
                        $('#fileupload').fileupload(
                            'option',
                            'redirect',
                            window.location.href.replace(
                                /\/[^\/]*$/,
                                '/cors/result.html?%s'
                            )
                        );
                        // Load existing files:
                        $('#fileupload').addClass('fileupload-processing');
                        $.ajax({
                            url: $('#fileupload').fileupload('option', 'url'),
                            dataType: 'json',
                            context: $('#fileupload')[0]
                        }).always(function () {
                            $(this).removeClass('fileupload-processing');
                        }).done(function (result) {
                            $(this).fileupload('option', 'done')
                                .call(this, $.Event('done'), {result: result});
                        });
                    }
                    counter++;
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