define(['jquery','bootstrap'], function ($) {
    var answerButton = $('#answer-button');
    answerButton.click(function(){
        answerButton.closest('ul').append($('#crudForm').clone());
    });
});