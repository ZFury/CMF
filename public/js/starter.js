/**
 * Created by babich on 12/29/14.
 */
define(['jquery', 'bootstrap', 'form', 'starter.grid'], function ($) {
    $('body').on('click', '.alert', function () {
        $(this).alert('close');
    });

    return $;
});