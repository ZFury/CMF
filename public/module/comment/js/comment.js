define(['jquery','bootstrap'], function ($) {
    $('body').on('success.form.fury', '.form-ajax', function() {
        var $this = $(this);
        $this.find('textarea').val('');
        $('p > div > .form-ajax').remove();
        $('[data-spy="grid"]').trigger('reload.fury');
    });

    $('body').on('click', 'a.cancel', function(){
        $(this).closest('form.form-ajax').parent('div').remove();
    });

    $('body').on('click', 'a.cancel-edit', function(){
        $('[data-spy="grid"]').trigger('reload.fury'); //Is it right?
    });

    $('body').on('click', 'a.edit-comment', function(){
        var commentId = $(this).closest($('ul.comment-body')).data('id');
        var mediaBody = $(this).closest($('li.media-body'));
        var mediaHeading = mediaBody.find('h5.media-heading').first();
        if (mediaHeading.find('div.form-send').length == 0) {
            console.log(mediaHeading.find('div > .form-ajax').length == 0);
            var text = mediaBody.find('.comment-text').first().text();
            mediaBody.find('.comment-text').first().remove();
            mediaBody.find('.answer-block').first().remove();

            mediaHeading.append($('.row > div > .form-ajax').first().parent('div').clone());
            mediaBody.find('h5.media-heading > div > .form-ajax').first()
                .attr('action', '/comment/index/edit/' + commentId)
                .attr('method', 'post');
            mediaBody.find('h5.media-heading > div > .form-ajax > div > div > textarea').first().val(text);
            mediaBody.find('h5.media-heading > div > .form-ajax > .form-group > .add-comment-button').first().parent('div')
                .append('<a class="cancel-edit col-sm-3 col-sm-offset-6" href="javascript:;">Cancel</a>');
        }
    });

    $('body').on('click', '.answer-button', function(){
        var commentId = $(this).closest($('.comment-body')).data('id');
        if (!$(this).closest('div.answer-block').find('div > .form-ajax').length) {
            $(this).closest('div.answer-block').append($('.row > div > .form-ajax').parent('div').clone());
            $(this).closest('div.answer-block').find('div > .form-ajax')
                .attr('action', '/comment/index/add?alias=comment&id=' + commentId);
            $(this).closest('div.answer-block').find('div > .form-ajax > .form-group > .add-comment-button').parent('div')
                .append('<a class="cancel col-sm-3 col-sm-offset-6" href="javascript:;">Cancel</a>');
        }
    });
});