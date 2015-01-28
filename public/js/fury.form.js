/**
 * Created by babich on 12/19/14.
 */
define(['jquery', 'fury.notify'], function ($, notify) {
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
                            var field = $this.find('.form-group .form-control[name="' + key + '"]');
                            field.addClass('form-control error-form-field');
                            field.parent().next().html(jsonData.errors[key][0]);
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