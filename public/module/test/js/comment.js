define(['jquery','bootstrap'], function ($) {
    var displayComments = function() {
        $('.entity').each(function() {
            var elem = $(this);
            $.ajax({
                url: elem.attr('data-href'),
                dataType: "html",
                success: function(data) {
                    elem.next().html(data);
                }
            });
        });
    };
    displayComments();
    $('body')
        .on('form.success', function () {
            displayComments();
        });

    var answerButton = $('#answer-button');
    answerButton.click(function(){
        answerButton.closest('ul').append($('#crudForm').clone());
    });
});