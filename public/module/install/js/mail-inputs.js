/**
 * Created by alexander on 1/6/15.
 */
define(['jquery', 'notify'], function ($, notify) {
    $(document).ready(function() {
        var max_fields      = 10; //maximum input boxes allowed
        var wrapper_from         = $(".input_fields_wrap_from"); //Fields wrapper
        var add_button_from      = $(".add_field_button_from"); //Add button ID
        var wrapper_emails         = $(".input_fields_wrap_emails"); //Fields wrapper
        var add_button_emails      = $(".add_field_button_emails"); //Add button ID
        var x = 1; //initlal text box count
        $(add_button_from).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                x++; //text box increment
                $(wrapper_from).append('<div class="textbox"><input type="email" name="from[]" class="form-control textboxinput" placeholder="From"/><a href="#" class="remove_field_from glyphicon glyphicon-remove"></a></div>'); //add input box
            } else {
                notify.addError('It is not allowed to add more then 10 fields!');
            }
        });

        $(wrapper_from).on("click",".remove_field_from", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
        })

        $(add_button_emails).click(function(e){ //on add input button click
            e.preventDefault();
            if(x < max_fields){ //max input box allowed
                x++; //text box increment
                $(wrapper_emails).append('<div class="textbox"><input type="email" name="emails[]" class="form-control textboxinput" placeholder="Emails"/><a href="#" class="remove_field_emails glyphicon glyphicon-remove"></a></div>'); //add input box
            } else {
                notify.addError('It is not allowed to add more then 10 fields!');
            }
        });

        $(wrapper_emails).on("click",".remove_field_emails", function(e){ //user click on remove text
            e.preventDefault(); $(this).parent('div').remove(); x--;
        })
    });
});