/**
 * Created by babich on 12/19/14.
 */
define(['jquery', 'fury.notify', 'bootstrap'], function ($, notify) {
    $(function () {
        $("[rel='tooltip']").tooltip();
    });
    $('body')
        .on('click', '.submit-ajax-button', function () {
            $(this).closest('.modal').find('form.form-ajax').trigger('submit.ajax');
        })
        .on('submit.ajax', '.form-ajax', function (e) {
            e.preventDefault();
            var $this = $(this);
            var formData = $this.serializeArray();
            $.ajax({
                url: $this.attr('action'),
                type: 'POST',
                beforeSend: function () {
                    $('.modal').find('a, .btn').addClass('disabled');
                    $this.find('input[type=submit]').addClass('disabled');
                    $('.error-form-field').each(function (i, element) {
                        $(element).removeClass('error-form-field');
                    });
                    $('.error-form-message').each(function (i, element) {
                        $(element).html('');
                    });
                },
                success: function (jsonData) {
                    $('.modal').find('a, .btn').removeClass('disabled');
                    $this.find('input[type=submit]').removeClass('disabled');
                    if (!$.isEmptyObject(jsonData.errors)) {
                        for (var key in jsonData.errors) {
                            var field = $this.find('.form-group .form-control[name="' + key + '"]');
                            field.addClass('form-control error-form-field');
                            field.attr("rel", "tooltip");
                            field.tooltip({
                                html: true,
                                title: jsonData.errors[key].join('<br/>'),
                                trigger: 'manual',
                                // change position for long messages, and for hidden fields
                                placement: (field.width() < 220) || field.is('label') ? 'right' : 'top',
                                animation: true
                            });
                            field.tooltip('show');
                            field.click(function () {
                                $(this).removeClass('error-form-field');
                                $(this).tooltip('destroy');
                            });
                        }
                    } else {
                        $this.trigger('success.form.fury', []);
                        $('.close-form-button').trigger('click');
                        jsonData.success.forEach(function (entry) {
                            notify.addSuccess(entry);
                        });
                    }
                },
                data: formData,
                dataType: 'json'
            });
            return false;
        });
});