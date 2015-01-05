/**
 * Created by alexander on 12/17/14.
 */
define(['jquery'], function ($) {
    var refreshIntervalId = setInterval(function(){
        if ("100" == $('.progress.progress-striped.active').attr('aria-valuenow')) {
            $('.uploading').remove();
            $('.template-upload.fade.in>td>p.name').append('<div class="conversion">It has been uploaded. Processing, please wait.</div>');
            clearInterval(refreshIntervalId);
        }
        if ("100" > $('.progress.progress-striped.active').attr('aria-valuenow') && "0" < $('.progress.progress-striped.active').attr('aria-valuenow')) {
        $('.template-upload.fade.in>td>p.name').append('<div class="uploading">File is being uploaded...</div>');
        }
    }, 300)
});