define(['jquery','bootstrap'], function ($) {

    $('.entity').each(function() {
        var elem = $(this);
        $.ajax({
            url: elem.attr('data-href'),
            dataType: "html",
            success: function(data) {
                elem.next().html(data);
                elem.next().find('.add-comment').toggleClass("add-comment add-com").attr({'data-toggle':'modal', 'data-target':'#myModal'});
                elem.next().find('.edit-comment').toggleClass("edit-comment add-com").attr({'data-toggle':'modal', 'data-target':'#myModal'});
            }

        });
    });

    $('body').on('click', '.add-com', function (e) {
        var button = $(this);

        $.ajax({
            url: button.attr('data-href'),

            dataType: "html",
            success: function(data) {
                $('.modal-body').html(data);
            }
        });
        e.preventDefault();
    });
});