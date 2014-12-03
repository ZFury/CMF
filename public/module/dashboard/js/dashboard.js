/**
 * Created by alexander on 12/1/14.
 */
define(function ($) {
        var url=document.location.href;
        $.each($('#side-nav li ul li a'),function(){
            if (this.href==url) {
                $(this).parent().addClass('active');
                $(this).closest('.dashboard-menu').collapse('show');
            }
        });
});