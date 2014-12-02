/**
 * Created by alexander on 12/1/14.
 */
define(['jquery', 'jquery-ui', 'jquery-nestedSortable'], function ($) {
    $(function() {
        var url=document.location.href;
        $.each($('#side-nav li ul li a'),function(){
            if (this.href==url) {
                $(this).parent().addClass('active');
                if ($(this).parent().parent().parent().find('ul').is(':hidden')) {
                    $(this).parent().parent().parent().find('ul').slideToggle('slow');
                }
                $(this).parent().parent().parent().addClass('active');
            }
        });
    });
});