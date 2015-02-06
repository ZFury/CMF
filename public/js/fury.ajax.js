/**
 * Declarative AJAX development
 *
 * <code>
 *    <a href="/get" class="ajax">Click Me!</a>
 *    <a href="/dialog" class="dialog">Click Me!</a>
 *    <a href="/delete" class="confirm" data-confirm="Are you sure?">Click Me!</a>
 *    <a href="/delete" class="ajax confirm" data-id="3" data-ajax-method="DELETE">Click Me!</a>
 *    <form action="/save/" class="ajax">
 *        ...
 *    </form>
 *    <source>
 *        // disable event handlers
 *        $('li a').off('.bluz');
 *        // or
 *        $('li a').off('.ajax');
 *    </source>
 * </code>
 * @author   Anton Shevchuk
 */
/*global define,require*/
define(['jquery', 'fury.notify'], function ($, notify) {
    "use strict";
    // on DOM ready state
    $(function () {

        // Ajax global events
        $(document)
            .ajaxStart(function () {
                $('#loading').show();
            })
            .ajaxError(function (event, jqXHR, options, thrownError) {
                console.log(event);
                console.log(jqXHR);
                console.log(jqXHR.getResponseHeader('Fury-Notify'));
                // show error messages
                if (options.dataType === 'json' || jqXHR.getResponseHeader('Content-Type') === 'application/json') {
                    var notifications = $.parseJSON(jqXHR.getResponseHeader('Fury-Notify'));
                    notify.set(notifications);
                }

                // try to get error message from JSON response
                if (!(options.dataType === 'json' ||
                    jqXHR.getResponseHeader('Content-Type') === 'application/json')) {
                    var $div = createModal(jqXHR.responseText, 'width:800px');
                    $div.modal('show');
                }
            })
            .ajaxComplete(function () {
                $('#loading').hide();
            });

        var modals = [];
        var createModal = function (content, title) {

            var $div = $('<div>', {'class': 'modal fade', 'id': 'formModal'});
            var $divDialog = $('<div>', {'class': 'modal-dialog modal-lg'});
            var $divContent = $('<div>', {'class': 'modal-content'});
            var $divBody = $('<div>', {'class': 'modal-body', 'id': 'popupBody'});
            var $divHeader = $('<div>', {'class': 'modal-header'});
            var $divFooter = $('<div>', {'class': 'modal-footer'});
            var $saveButton = $('<button>', {
                'class': 'btn btn-primary submit-ajax-button',
                'type': 'button'
            });
            var $closeButton = $('<button>', {
                'class': 'btn btn-default close-form-button',
                'type': 'button',
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

        // get only plain data
        var processData = function (el) {
            var data = el.data();
            var plain = {};

            $.each(data, function (key, value) {
                if (!(typeof value === 'function' ||
                    typeof value === 'object' ||
                    key === 'ajaxMethod' ||
                    key === 'ajaxSource' ||
                    key === 'ajaxTarget' ||
                    key === 'ajaxType')) {
                    plain[key] = value;
                }
            });
            return plain;
        };

        // live event handlers
        $('body')
            // Confirmation dialog
            .on('click.fury.confirm', '.confirm', function (event) {
                event.preventDefault();

                var $this = $(this);

                var message = $this.data('confirm') ? $this.data('confirm') : 'Are you sure?';
                if (!window.confirm(message)) {
                    event.stopImmediatePropagation();
                }
            })
            // Ajax links
            .on('click.fury.ajax', 'a.ajax', function (event) {
                event.preventDefault();

                var $this = $(this);
                if ($this.hasClass('disabled')) {
                    // request in progress
                    return false;
                }

                var method = $this.data('ajax-method');
                var type = $this.data('ajax-type');
                type = (type ? type : 'json');

                var data = processData($this);
                $.ajax({
                    url: $this.attr('href'),
                    type: (method ? method : 'post'),
                    data: data,
                    dataType: type,
                    beforeSend: function () {
                        $this.addClass('disabled');
                    },
                    success: function (data, textStatus, jqXHR) {
                        $this.trigger('success.ajax.fury', arguments);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $this.trigger('error.ajax.fury', arguments);
                    },
                    complete: function () {
                        $this.removeClass('disabled');
                    }
                });
            })
            // Ajax load
            .on('click.fury.ajax', '.load', function (event) {
                event.preventDefault();

                var $this = $(this);
                if ($this.hasClass('disabled')) {
                    // request in progress
                    return false;
                }

                var method = $this.data('ajax-method');
                var target = $this.data('ajax-target');
                var source = $this.attr('href') || $this.data('ajax-source');

                if (!target) {
                    throw "Undefined 'data-ajax-target' attribute";
                }

                if (!source) {
                    throw "Undefined 'data-ajax-source' attribute (and href is missing)";
                }

                $.ajax({
                    url: source,
                    type: (method ? method : 'post'),
                    data: processData($this),
                    dataType: 'html',
                    beforeSend: function () {
                        $this.addClass('disabled');
                    },
                    success: function (data) {
                        $this.trigger('success.ajax.fury', arguments);
                        var $target = $(target);
                        if ($target.length === 0) {
                            throw "Element defined by 'data-ajax-target' not found";
                        }
                        $target.html(data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $this.trigger('error.ajax.fury', arguments);
                    },
                    complete: function () {
                        $this.removeClass('disabled');
                    }
                });
                return false;
            })
            // Ajax modal dialog
            .on('click.fury.ajax', '.dialog', function (event) {
                event.preventDefault();

                var $this = $(this);
                if ($this.hasClass('disabled')) {
                    // request in progress
                    return false;
                }
                var method = $this.data('ajax-method');
                //var style = $this.data('modal-style');
                var title = $(this).data('title');
                if (!title) {
                    title = 'Create/edit';
                }

                $.ajax({
                    url: $this.attr('href'),
                    type: (method ? method : 'post'),
                    data: processData($this),
                    dataType: 'html',
                    beforeSend: function () {
                        $this.addClass('disabled');
                    },
                    success: function (data) {
                        var $div = createModal(data, title);
                        $('body').trigger('modal.loaded', []);
                        $div.on('shown.bs.modal', function () {
                            // you can handle event "shown.fury.modal" on button
                            $this.trigger('shown.fury.modal');
                        })
                            .on('hidden.bs.modal', function () {
                                closeModals();
                                $('.modal').remove();
                                // you can handle event "hidden.fury.modal" on button
                                $this.trigger('hidden.fury.modal');
                            })
                            .on('success.form.fury', function () {
                                $this.trigger('complete.ajax.fury', arguments);
                            })
                        ;
                        $div.modal('show');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $this.trigger('error.ajax.fury', arguments);
                    },
                    complete: function () {
                        $this.removeClass('disabled');
                    }
                });
            })
            // Image popup preview
            .on('click.fury.preview', '.fury-preview', function (event) {
                event.preventDefault();

                var url, $this = $(this);
                // get image source
                if ($this.is('a')) {
                    url = $this.attr('href');
                } else {
                    url = $this.data('preview');
                }

                if (!url) {
                    return false;
                }
                var $img = $('<img>', {'src': url, 'class': 'img-polaroid'});
                $img.css({
                    width: '100%',
                    margin: '0 auto',
                    display: 'block'
                });

                var $span = $('<span>', {'class': 'thumbnail'});
                $span.append($img);

                var $div = createModal($span, '');
                $div.modal('show');
            });
    });
});