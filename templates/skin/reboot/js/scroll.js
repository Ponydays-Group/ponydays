/*function scrollDown(){
    $('html, body').animate({scrollTop:$(document).height()}, 'slow');
}*/

function showFloatBlock() {
    $.browser.isMobileDevice = /android|webos|iphone|ipad|ipod|blackberry/i.test(navigator.userAgent.toLowerCase());
    if ($.browser.isMobileDevice || $(window).width() < 1024 || $(window).height() < 500 || screen.width < 1024) {
        return
    }
    var showFloat = false;
    var reinit = function () {
        if ($(window).width() < 1024 || $(window).height() < 500 || screen.width < 1024) {
            return
        }
        var sidebar = $('#sidebar');
        var floatBlock = $('.block-type-stream');
        var bottomPos = sidebar.offset().top + sidebar.outerHeight();
        if (showFloat) {
            bottomPos += floatBlock.outerHeight() + 20;
        }
        if (this.pageYOffset > bottomPos) {
            if (! showFloat) {
                floatBlock.addClass('stream-fixed');
                floatBlock.animate({ opacity: 0 }, 0, function () { floatBlock.animate({ opacity: 1}, 500) });
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

$(document).ready( function () {
    showFloatBlock();
} );
