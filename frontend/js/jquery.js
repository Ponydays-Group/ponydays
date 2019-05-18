import $ from "jquery"

window.$ = $;
window.jQuery = $;

window.jQuery.browser = {};
(function() {
    window.jQuery.browser.msie = false;
    window.jQuery.browser.version = 0;
    if(navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        window.jQuery.browser.msie = true;
        window.jQuery.browser.version = RegExp.$1;
    }
})();
