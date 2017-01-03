/*function scrollDown(){
    $('html, body').animate({scrollTop:$(document).height()}, 'slow');
}*/

function showFloatBlock($) {
    $.browser.isMobileDevice = /android|webos|iphone|ipad|ipod|blackberry/i.test(navigator.userAgent.toLowerCase());
    if ($.browser.isMobileDevice || $(window).width() < 1024 || $(window).height() < 500 || screen.width < 1024) {
        return
    }
    var showFloat = false;
    var reinit = function () {
        var floatBlock = $('.block-type-stream');
        if ($(window).width() < 1024 || $(window).height() < 500 || screen.width < 1024) {
            if (showFloat) {
                floatBlock.removeClass('stream-fixed');
                showFloat = false;
            }
            return;
        }
        var sidebar = $('#sidebar');
        var bottomPos = sidebar.offset().top + sidebar.outerHeight();
        if (showFloat) {
            bottomPos += floatBlock.outerHeight() + 20;
        }
        if (this.pageYOffset > bottomPos) {
            if (! showFloat) {
                floatBlock.addClass('stream-fixed');
                //floatBlock.offset({left: Math.min($(window).width - 360, $(window).width / 2 + 1280/2 - 360)});
                floatBlock.animate({ opacity: 0 }, 0, function () { floatBlock.animate({ opacity: 1}, 350) });
                showFloat = true;
            }
        } else {
            if (showFloat) {
                floatBlock.removeClass('stream-fixed');
                showFloat = false;
            }
        }
    }
    $(window).bind('scroll resize', reinit);
    reinit();
}

jQuery(document).ready( function ($) {
    showFloatBlock($);
} );
