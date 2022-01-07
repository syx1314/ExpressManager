jQuery(document).ready(function() {
    ajaxAdSense(aKeyForAdSense, mustDisplayAdSense);
});
function ajaxAdSense(aKeyForAdSense, mustDisplayAdSense) {
    $.ajax({
        type : "POST",
        url  : "/app/getAdAdsense",
        data  :{'aKey': aKeyForAdSense, 'mustDisplayAdSense': mustDisplayAdSense, 'showAppAd':showAppAd},
        dataType: 'html',
        beforeSend: function( xhr ) {
        },
        success : function(result, textStatus, jqXHR) {
            if (result) {
                $('body').append(result);
                $('.copyright').css("margin-bottom", "90px");
                $('.footer-common').css("margin-bottom", "90px");
            }
        },
            error : function(jqXHR, textStatus, errorThrown) {
        }
    });
}

