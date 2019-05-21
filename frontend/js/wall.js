import * as Msg from './msg'
import Emitter from './emitter'
import $ from 'jquery'
import * as Ajax from './ajax'

/**
 * Стена пользователя
 */

export const options = {
    login: ''
};

export let iIdForReply = null;
/**
 * Добавление записи
 */
export function add(sText, iPid) {
    $('.js-button-wall-submit').attr('disabled', true);
    const url = aRouter['profile'] + this.options.login + '/wall/add/';
    const params = {sText: sText, iPid: iPid};

    Emitter.emit('wall_add_before');
    $('#wall-text').addClass('loader');
    Ajax.ajax(url, params, function (result) {
        $('.js-button-wall-submit').attr('disabled', false);
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('.js-wall-reply-parent-text').val('');
            $('#wall-note-list-empty').hide();
            this.loadNew();
            Emitter.emit('wall_add_after', [sText, iPid, result]);
        }
        $('#wall-text').removeClass('loader');
    }.bind(this));
    return false;
}

export function addReply(sText, iPid) {
    $('.js-button-wall-submit').attr('disabled', true);
    const url = aRouter['profile'] + this.options.login + '/wall/add/';
    const params = {sText: sText, iPid: iPid};

    Emitter.emit('wall_addreply_before');
    $('#wall-reply-text-' + iPid).addClass('loader');
    Ajax.ajax(url, params, function (result) {
        $('.js-button-wall-submit').attr('disabled', false);
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('.js-wall-reply-text').val('');
            this.loadReplyNew(iPid);
            Emitter.emit('wall_addreply_after', [sText, iPid, result]);
        }
        $('#wall-reply-text-' + iPid).removeClass('loader');
    }.bind(this));
    return false;
}

export function load(iIdLess, iIdMore, callback) {
    const url = aRouter['profile'] + this.options.login + '/wall/load/';
    const params = {iIdLess: iIdLess ? iIdLess : '', iIdMore: iIdMore ? iIdMore : ''};
    Emitter.emit('wall_load_before');
    Ajax.ajax(url, params, callback);
    return false;
}

export function loadReply(iIdLess, iIdMore, iPid, callback) {
    const url = aRouter['profile'] + this.options.login + '/wall/load-reply/';
    const params = {iIdLess: iIdLess ? iIdLess : '', iIdMore: iIdMore ? iIdMore : '', iPid: iPid};
    Emitter.emit('wall_loadreply_before');
    Ajax.ajax(url, params, callback);
    return false;
}

export function loadNext() {
    const divLast = $('#wall-container').find('.js-wall-item:last');
    let idLess;
    if (divLast.length) {
        idLess = divLast.attr('id').replace('wall-item-', '');
    } else {
        return false;
    }
    $('#wall-button-next').addClass('loader');
    this.load(idLess, '', function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            if (result.iCountWall) {
                $('#wall-container').append(result.sText);
            }
            const iCount = result.iCountWall - result.iCountWallReturn;
            if (iCount) {
                $('#wall-count-next').text(iCount);
            } else {
                $('#wall-button-next').detach();
            }
            Emitter.emit('wall_loadnext_after', [idLess, result]);
        }
        $('#wall-button-next').removeClass('loader');
    }.bind(this));
    return false;
}

export function loadNew() {
    const divFirst = $('#wall-container').find('.js-wall-item:first');
    let idMore;
    if (divFirst.length) {
        idMore = divFirst.attr('id').replace('wall-item-', '');
    } else {
        idMore = -1;
    }
    this.load('', idMore, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            if (result.iCountWall) {
                $('#wall-container').prepend(result.sText);
            }
            Emitter.emit('wall_loadnew_after', [idMore, result]);
        }
    }.bind(this));
    return false;
}

export function loadReplyNew(iPid) {
    const divFirst = $('#wall-reply-container-' + iPid).find('.js-wall-reply-item:last');
    let idMore;
    if (divFirst.length) {
        idMore = divFirst.attr('id').replace('wall-reply-item-', '');
    } else {
        idMore = -1;
    }
    this.loadReply('', idMore, iPid, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            if (result.iCountWall) {
                $('#wall-reply-container-' + iPid).append(result.sText);
            }
            Emitter.emit('wall_loadreplynew_after', [iPid, idMore, result]);
        }
    }.bind(this));
    return false;
}

export function loadReplyNext(iPid) {
    const divLast = $('#wall-reply-container-' + iPid).find('.js-wall-reply-item:first');
    let idLess;
    if (divLast.length) {
        idLess = divLast.attr('id').replace('wall-reply-item-', '');
    } else {
        return false;
    }
    $('#wall-reply-button-next-' + iPid).addClass('loader');
    this.loadReply(idLess, '', iPid, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            if (result.iCountWall) {
                $('#wall-reply-container-' + iPid).prepend(result.sText);
            }
            const iCount = result.iCountWall - result.iCountWallReturn;
            if (iCount) {
                $('#wall-reply-count-next-' + iPid).text(iCount);
            } else {
                $('#wall-reply-button-next-' + iPid).detach();
            }
            Emitter.emit('wall_loadreplynext_after', [iPid, idLess, result]);
        }
        $('#wall-reply-button-next-' + iPid).removeClass('loader');
    }.bind(this));
    return false;
}

export function toggleReply(iId) {
    $('#wall-item-' + iId + ' .wall-submit-reply').addClass('active').toggle().children('textarea').focus();
    return false;
}

export function expandReply(iId) {
    $('#wall-item-' + iId + ' .wall-submit-reply').addClass('active');
    return false;
}

export function init(opt) {
    if (opt) {
        $.extend(true, this.options, opt);
    }
    jQuery(function ($) {
        $(document).click(function (e) {
            if (e.which == 1) {
                $('.wall-submit-reply.active').each(function (k, v) {
                    if (!$(v).find('.js-wall-reply-text').val()) {
                        $(v).removeClass('active');
                    }
                });
            }
        });

        $('body').on("click", ".wall-submit-reply, .link-dotted", function (e) {
            e.stopPropagation();
        });

        let key;
        $('.js-wall-reply-text').bind('keyup', function (e) {
            key = e.keyCode || e.which;
            if (e.ctrlKey && (key == 13)) {
                var id = $(e.target).attr('id').replace('wall-reply-text-', '');
                this.addReply($(e.target).val(), id);
                return false;
            }
        }.bind(this));
        $('.js-wall-reply-parent-text').bind('keyup', function (e) {
            key = e.keyCode || e.which;
            if (e.ctrlKey && (key == 13)) {
                this.add($(e.target).val(), 0);
                return false;
            }
        }.bind(this));
    }.bind(this));
}

export function remove(iId) {
    const url = aRouter['profile'] + this.options.login + '/wall/remove/';
    const params = {iId: iId};
    Emitter.emit('wall_remove_before');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#wall-item-' + iId).fadeOut('slow', function () {
                Emitter.emit('wall_remove_item_fade', [iId, result], this);
            });
            $('#wall-reply-item-' + iId).fadeOut('slow', function () {
                Emitter.emit('wall_remove_reply_item_fade', [iId, result], this);
            });
            Emitter.emit('wall_remove_after', [iId, result]);
        }
    });
    return false;
}
