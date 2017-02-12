import $ from 'jquery'

/**
 * Функционал тул-бара (плавающая пимпа) списка топиков
 */

export let iCurrentTopic = -1;

export function init() {
    var vars = [], hash;
    var hashes = window.location.hash.replace('#', '').split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }

    if (vars.goTopic !== undefined) {
        if (vars.goTopic == 'last') {
            this.iCurrentTopic = $('.js-topic').length - 2;
        } else {
            this.iCurrentTopic = parseInt(vars.goTopic) - 1;
        }
        this.goNext();
    }
};

export function reset() {
    this.iCurrentTopic = -1;
};

/**
 * Прокрутка следующему топику
 */
export function goNext() {
    this.iCurrentTopic++;
    var topic = $('.js-topic:eq(' + this.iCurrentTopic + ')');
    if (topic.length) {
        $.scrollTo(topic, 500);
    } else {
        this.iCurrentTopic = $('.js-topic').length - 1;
        // переход на следующую страницу
        var page = $('.js-paging-next-page');
        if (page.length && page.attr('href')) {
            window.location = page.attr('href') + '#goTopic=0';
        }
    }

    return false;
};

/**
 * Прокрутка предыдущему топику
 */
export function goPrev() {
    this.iCurrentTopic--;
    if (this.iCurrentTopic < 0) {
        this.iCurrentTopic = 0;
        // на предыдущую страницу
        var page = $('.js-paging-prev-page');
        if (page.length && page.attr('href')) {
            window.location = page.attr('href') + '#goTopic=last';
        }
    } else {
        var topic = $('.js-topic:eq(' + this.iCurrentTopic + ')');
        if (topic.length) {
            $.scrollTo(topic, 500);
        }
    }
    return false;
};

/**
 * Функционал кнопки "UP"
 */

export function goUp() {
    $.scrollTo(0, 400);
    return false;
};

export function goDown() {
    $.scrollTo("#footer", 400);
    return false;
};
