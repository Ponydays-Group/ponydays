import $ from "jquery"
import * as Ajax from './ajax'

/**
 * Опции
 */
export let options = {
    active: 'active',
    loader: DIR_STATIC_SKIN + '/images/loader.gif',
    type: {
        stream_comment: {
            url: aRouter['ajax'] + 'stream/comment/'
        },
        stream_topic: {
            url: aRouter['ajax'] + 'stream/topic/'
        },
        blogs_top: {
            url: aRouter['ajax'] + 'blogs/top/'
        },
        blogs_join: {
            url: aRouter['ajax'] + 'blogs/join/'
        },
        blogs_self: {
            url: aRouter['ajax'] + 'blogs/self/'
        }
    }
}

/**
 * Метод загрузки содержимого блока
 */
export function load(obj, block, params) {
    let type = $(obj).data('type');
    ls.hook.marker('loadBefore');

    if (!type) return;
    type = block + '_' + type;

    params = $.extend(true, {}, options.type[type].params || {}, params || {});

    let content = $('.js-block-' + block + '-content');
    showProgress(content);

    $('.js-block-' + block + '-item').removeClass(options.active);
    $(obj).addClass(options.active);

    Ajax.ajax(options.type[type].url, params, function (result) {
        let args = [content, result];
        ls.hook.marker('onLoadBefore');
        onLoad.apply(this, args);
    }.bind(this));
}

/**
 * Переключает вкладки в блоке, без использования Ajax
 * @param obj
 * @param block
 */
export function switchTab(obj, block) {
    /**
     * Если вкладку передаем как строчку - значение data-type
     */
    if (typeof(obj) == 'string') {
        $('.js-block-' + block + '-item').each(function (k, v) {
            if ($(v).data('type') == obj) {
                obj = v;
                return;
            }
        });
    }
    /**
     * Если не нашли такой вкладки
     */
    if (typeof(obj) == 'string') {
        return false;
    }

    $('.js-block-' + block + '-item').removeClass(options.active);
    $(obj).addClass(options.active);

    $('.js-block-' + block + '-content').hide();
    $('.js-block-' + block + '-content').each(function (k, v) {
        if ($(v).data('type') == $(obj).data('type')) {
            $(v).show();
        }
    });
    ls.hook.run('ls_blocks_switch_tab_after', [obj, block], this);
    return true;
}

/**
 * Отображение процесса загрузки
 */
export function showProgress(content) {
    content.height(content.height());
    content.empty().css({'background': 'url(' + options.loader + ') no-repeat center top', 'min-height': 70});
}

/**
 * Обработка результатов загрузки
 */
export function onLoad(content, result) {
    $(this).trigger('loadSuccessful', arguments);
    content.empty().css({'background': 'none', 'height': 'auto', 'min-height': 0});
    if (result.bStateError) {
        ls.msg.error(null, result.sMsg);
    } else {
        content.html(result.sText);
        ls.hook.run('ls_block_onload_html_after', arguments, this);
    }
}


export function getCurrentItem(block) {
    if ($('.js-block-' + block + '-nav').is(':visible')) {
        return $('.js-block-' + block + '-nav').find('.js-block-' + block + '-item.' + options.active);
    } else {
        return $('.js-block-' + block + '-dropdown-items').find('.js-block-' + block + '-item.' + options.active);
    }
}

export function initSwitch(block) {
    $('.js-block-' + block + '-item').click(function () {
        ls.blocks.switchTab(this, block);
        return false;
    });
}

export function init(block, params) {
    params = params || {};
    $('.js-block-' + block + '-item').click(function () {
        ls.blocks.load(this, block);
        return false;
    });
    if (params.group_items) {
        initNavigation(block, params.group_min);
    }

    let $this = this;
    $('.js-block-' + block + '-update').click(function () {
        $(this).addClass('fa-spin');
        ls.blocks.load(getCurrentItem(block), block);
        setTimeout(function () {
            $(this).removeClass('fa-spin');
        }.bind(this), 600);
    });
}

export function initNavigation(block, count) {
    count = count || 3;
    if ($('.js-block-' + block + '-nav').find('li').length >= count) {
        $('.js-block-' + block + '-nav').hide();
        $('.js-block-' + block + '-dropdown').show();
        // Dropdown
        let trigger = $('.js-block-' + block + '-dropdown-trigger');
        let menu = $('.js-block-' + block + '-dropdown-items');

        menu.appendTo('body').css({'display': 'none'});
        trigger.click(function () {
            let pos = $(this).offset();
            menu.css({'left': pos.left, 'top': pos.top + 30, 'z-index': 2100});
            menu.slideToggle();
            $(this).toggleClass('opened');
            return false;
        });
        menu.find('a').click(function () {
            trigger.removeClass('opened').find('a').text($(this).text());
            menu.slideToggle();
        });
        // Hide menu
        $(document).click(function () {
            trigger.removeClass('opened');
            menu.slideUp();
        });
        $('body').on('click', '.js-block-' + block + '-dropdown-trigger, .js-block-' + block + '-dropdown-items', function (e) {
            e.stopPropagation();
        });

        $(window).resize(function () {
            menu.css({'left': $('.js-block-' + block + '-dropdown-trigger').offset().left});
        });
    } else {
        // Transform nav to dropdown
        $('.js-block-' + block + '-nav').show();
        $('.js-block-' + block + '-dropdown').hide();
    }
    //ls.hook.run('ls_blocks_init_navigation_after',[block,count],this);
}
