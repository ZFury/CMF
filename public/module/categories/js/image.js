/*
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */
define([
    'jquery',
    'tmpl',
    'iframe-transport',
    'fileupload-ui'
], function ($) {
    'use strict';
    $(function () {
        if ($('#fileupload').length) {
            init();
        }
        $('body')
            .on('shown.bs.modal', function () {
                init();
            });
    });


    function init() {
        var id = $('#entityId').val();
        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            url: 'categories/management/start-image-upload' + (id ? "/" + id : "")
        });
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            url: $('#fileupload').fileupload('option', 'url'),
            //dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
    };
});