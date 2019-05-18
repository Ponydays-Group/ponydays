import $ from "jquery"

/**
 * Функционал кнопки "UP"
 */

export function goUp() {
    $("html, body").animate({
        scrollTop: 0,
    }, 350);
    return false;
}

export function goDown() {
    $("html, body").animate({
        scrollTop: $("#footer").offset().top,
    }, 350);
    return false;
}
