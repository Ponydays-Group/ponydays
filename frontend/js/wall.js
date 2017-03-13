import * as Msg from './msg'
import Emitter from './emitter'
import $ from 'jquery'
import * as Ajax from './ajax'

/**
 * Стена пользователя
 */

export let options = {
    login: ''
};

export let iIdForReply = null;
/**
 * Добавление записи
 */
export function add(sText, iPid) {
    $('.js-button-wall-submit').attr('disabled', true);
    var url = aRouter['profile'] + this.options.login + '/wall/add/';
    var params = {sText: sText, iPid: iPid};

    Emitter.emit('addBefore');
    $('#wall-text').addClass('loader');
    Ajax.ajax(url, params, function (result) {
        $('.js-button-wall-submit').attr('disabled', false);
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('.js-wall-reply-parent-text').val('');
            $('#wall-note-list-empty').hide();
            this.loadNew();
            Emitter.emit('ls_wall_add_after', [sText, iPid, result]);
        }
        $('#wall-text').removeClass('loader');
    }.bind(this));
    return false;
};

export function addReply(sText, iPid) {
    $('.js-button-wall-submit').attr('disabled', true);
    var url = aRouter['profile'] + this.options.login + '/wall/add/';
    var params = {sText: sText, iPid: iPid};

    Emitter.emit('addReplyBefore');
    $('#wall-reply-text-' + iPid).addClass('loader');
    Ajax.ajax(url, params, function (result) {
        $('.js-button-wall-submit').attr('disabled', false);
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('.js-wall-reply-text').val('');
            this.loadReplyNew(iPid);
            Emitter.emit('ls_wall_addreply_after', [sText, iPid, result]);
        }
        $('#wall-reply-text-' + iPid).removeClass('loader');
    }.bind(this));
    return false;
};

export function load(iIdLess, iIdMore, callback) {
    var url = aRouter['profile'] + this.options.login + '/wall/load/';
    var params = {iIdLess: iIdLess ? iIdLess : '', iIdMore: iIdMore ? iIdMore : ''};
    Emitter.emit('loadBefore');
    Ajax.ajax(url, params, callback);
    return false;
};

export function loadReply(iIdLess, iIdMore, iPid, callback) {
    var url = aRouter['profile'] + this.options.login + '/wall/load-reply/';
    var params = {iIdLess: iIdLess ? iIdLess : '', iIdMore: iIdMore ? iIdMore : '', iPid: iPid};
    Emitter.emit('loadReplyBefore');
    Ajax.ajax(url, params, callback);
    return false;
};

export function loadNext() {
    var divLast = $('#wall-container').find('.js-wall-item:last');
    if (divLast.length) {
        var idLess = divLast.attr('id').replace('wall-item-', '');
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
            var iCount = result.iCountWall - result.iCountWallReturn;
            if (iCount) {
                $('#wall-count-next').text(iCount);
            } else {
                $('#wall-button-next').detach();
            }
            Emitter.emit('ls_wall_loadnext_after', [idLess, result]);
        }
        $('#wall-button-next').removeClass('loader');
    }.bind(this));
    return false;
};

export function loadNew() {
    var divFirst = $('#wall-container').find('.js-wall-item:first');
    if (divFirst.length) {
        var idMore = divFirst.attr('id').replace('wall-item-', '');
    } else {
        var idMore = -1;
    }
    this.load('', idMore, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            if (result.iCountWall) {
                $('#wall-container').prepend(result.sText);
            }
            Emitter.emit('ls_wall_loadnew_after', [idMore, result]);
        }
    }.bind(this));
    return false;
};

export function loadReplyNew(iPid) {
    var divFirst = $('#wall-reply-container-' + iPid).find('.js-wall-reply-item::last');
    if (divFirst.length) {
        var idMore = divFirst.attr('id').replace('wall-reply-item-', '');
    } else {
        var idMore = -1;
    }
    this.loadReply('', idMore, iPid, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            if (result.iCountWall) {
                $('#wall-reply-container-' + iPid).append(result.sText);
            }
            Emitter.emit('ls_wall_loadreplynew_after', [iPid, idMore, result]);
        }
    }.bind(this));
    return false;
};

export function loadReplyNext(iPid) {
    var divLast = $('#wall-reply-container-' + iPid).find('.js-wall-reply-item:first');
    if (divLast.length) {
        var idLess = divLast.attr('id').replace('wall-reply-item-', '');
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
            var iCount = result.iCountWall - result.iCountWallReturn;
            if (iCount) {
                $('#wall-reply-count-next-' + iPid).text(iCount);
            } else {
                $('#wall-reply-button-next-' + iPid).detach();
            }
            Emitter.emit('ls_wall_loadreplynext_after', [iPid, idLess, result]);
        }
        $('#wall-reply-button-next-' + iPid).removeClass('loader');
    }.bind(this));
    return false;
};

export function toggleReply(iId) {
    $('#wall-item-' + iId + ' .wall-submit-reply').addClass('active').toggle().children('textarea').focus();
    return false;
};

export function expandReply(iId) {
    $('#wall-item-' + iId + ' .wall-submit-reply').addClass('active');
    return false;
};

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
};

export function remove(iId) {
    var url = aRouter['profile'] + this.options.login + '/wall/remove/';
    var params = {iId: iId};
    Emitter.emit('removeBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#wall-item-' + iId).fadeOut('slow', function () {
                Emitter.emit('ls_wall_remove_item_fade', [iId, result], this);
            });
            $('#wall-reply-item-' + iId).fadeOut('slow', function () {
                Emitter.emit('ls_wall_remove_reply_item_fade', [iId, result], this);
            });
            Emitter.emit('ls_wall_remove_after', [iId, result]);
        }
    });
    return false;
};
