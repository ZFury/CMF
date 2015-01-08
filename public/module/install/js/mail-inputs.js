/**
 * Created by alexander on 1/6/15.
 */
define(['jquery', 'notify'], function ($, notify) {
    $(document).ready(function() {
        var addButtonEmails      = $(".add_field_button_emails"); //Add button ID
        $('.emails-fieldset > fieldset').addClass('fieldset-wrapper-email');
        $('.fieldset-wrapper-email > fieldset > input').addClass('form-control');
        var templateEmails = $('.fieldset-wrapper-email > span').data('template');

        var addButtonFrom      = $(".add_field_button_from"); //Add button ID
        $('.from-fieldset > fieldset').addClass('fieldset-wrapper-from');
        $('.fieldset-wrapper-from > fieldset > input').addClass('form-control');
        var templateFrom = $('.fieldset-wrapper-from > span').data('template');

        addButtonEmails.click(function(e){ //on add input button click
            e.preventDefault();
            var currentCount = $('.fieldset-wrapper-email > fieldset').length;
            $('.fieldset-wrapper-email').append(templateEmails.replace(/__index__/g, currentCount));
            $('.fieldset-wrapper-email > fieldset > input').addClass('form-control');
            return false;
        });

        addButtonFrom .click(function(e){ //on add input button click
            e.preventDefault();
            var currentCount = $('.fieldset-wrapper-from > fieldset').length;
            $('.fieldset-wrapper-from').append(templateFrom.replace(/__index__/g, currentCount));
            $('.fieldset-wrapper-from > fieldset > input').addClass('form-control');
            return false;
        });

        //$(wrapper_emails).on("click", ".remove_field_emails", function(e){ //user click on remove text
        //    e.preventDefault(); $(this).parent('div').remove();
        //})
    });
});