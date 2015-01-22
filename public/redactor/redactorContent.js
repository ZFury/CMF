/**
 * Created by babich on 12/10/14.
 */
define(['jquery', 'redactor'], function ($) {
    $('.redactor-content').redactor({
        //            plugins: ['fullscreen'],
        //            scrollTarget: '.container',
        minHeight: 300,
        placeholder: 'Content'
    });
});