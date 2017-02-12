import $ from 'jquery'
import * as Ajax from './ajax'

export let isBusy = false;
export let dateLast = null;

export function subscribe(iTargetUserId) {
    var url = aRouter['stream'] + 'subscribe/';
    var params = {'id': iTargetUserId};

    ls.hook.marker('subscribeBefore');
    Ajax.ajax(url, params, function (data) {
        if (data.bStateError) {
            ls.msg.error(data.sMsgTitle, data.sMsg);
        } else {
            ls.msg.notice(data.sMsgTitle, data.sMsg);
            ls.hook.run('ls_stream_subscribe_after', [params, data]);
        }
    });
};

export function unsubscribe(iId) {
    var url = aRouter['stream'] + 'unsubscribe/';
    var params = {'id': iId};

    ls.hook.marker('unsubscribeBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            ls.msg.notice(data.sMsgTitle, data.sMsg);
            ls.hook.run('ls_stream_unsubscribe_after', [params, data]);
        }
    });
};

export function switchEventType(iType) {
    var url = aRouter['stream'] + 'switchEventType/';
    var params = {'type': iType};

    ls.hook.marker('switchEventTypeBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            ls.msg.notice(data.sMsgTitle, data.sMsg);
            ls.hook.run('ls_stream_switch_event_type_after', [params, data]);
        }
    });
};

export function appendUser() {
    var sLogin = $('#stream_users_complete').val();
    if (!sLogin) return;

    var url = aRouter['stream'] + 'subscribeByLogin/';
    var params = {'login': sLogin};

    ls.hook.marker('appendUserBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            $('#stream_no_subscribed_users').remove();
            var checkbox = $('#strm_u_' + data.uid);
            if (checkbox.length) {
                if (checkbox.attr('checked')) {
                    ls.msg.error(ls.lang.get('error'), ls.lang.get('stream_subscribes_already_subscribed'));
                } else {
                    checkbox.attr('checked', 'on');
                    ls.msg.notice(data.sMsgTitle, data.sMsg);
                }
            } else {
                var liElement = $('<li><input type="checkbox" class="streamUserCheckbox input-checkbox" id="strm_u_' + data.uid + '" checked="checked" onClick="if ($(this).get(\'checked\')) {ls.stream.subscribe(' + data.uid + ')} else {ls.stream.unsubscribe(' + data.uid + ')}" /> <a href="' + data.user_web_path + '">' + data.user_login + '</a></li>');
                $('#stream_block_users_list').append(liElement);
                ls.msg.notice(data.sMsgTitle, data.sMsg);
            }
            ls.hook.run('ls_stream_append_user_after', [checkbox.length, data]);
        } else {
            ls.msg.error(data.sMsgTitle, data.sMsg);
        }
    });
};

export function getMore() {
    if (this.isBusy) {
        return;
    }
    var lastId = $('#stream_last_id').val();
    if (!lastId) return;
    $('#stream_get_more').addClass('stream_loading');
    this.isBusy = true;

    var url = aRouter['stream'] + 'get_more/';
    var params = {'last_id': lastId, 'date_last': this.dateLast};

    ls.hook.marker('getMoreBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError && data.events_count) {
            $('#stream-list').append(data.result);
            $('#stream_last_id').attr('value', data.iStreamLastId);
        }
        if (!data.events_count) {
            $('#stream_get_more').hide();
        }
        $('#stream_get_more').removeClass('stream_loading');
        ls.hook.run('ls_stream_get_more_after', [lastId, data]);
        this.isBusy = false;
    }.bind(this));
};

export function getMoreAll() {
    if (this.isBusy) {
        return;
    }
    var lastId = $('#stream_last_id').val();
    if (!lastId) return;
    $('#stream_get_more').addClass('stream_loading');
    this.isBusy = true;

    var url = aRouter['stream'] + 'get_more_all/';
    var params = {'last_id': lastId, 'date_last': this.dateLast};

    ls.hook.marker('getMoreAllBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError && data.events_count) {
            $('#stream-list').append(data.result);
            $('#stream_last_id').attr('value', data.iStreamLastId);
        }
        if (!data.events_count) {
            $('#stream_get_more').hide();
        }
        $('#stream_get_more').removeClass('stream_loading');
        ls.hook.run('ls_stream_get_more_all_after', [lastId, data]);
        this.isBusy = false;
    }.bind(this));
};

export function getMoreByUser(iUserId) {
    if (this.isBusy) {
        return;
    }
    var lastId = $('#stream_last_id').val();
    if (!lastId) return;
    $('#stream_get_more').addClass('stream_loading');
    this.isBusy = true;

    var url = aRouter['stream'] + 'get_more_user/';
    var params = {'last_id': lastId, user_id: iUserId, 'date_last': this.dateLast};

    ls.hook.marker('getMoreByUserBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError && data.events_count) {
            $('#stream-list').append(data.result);
            $('#stream_last_id').attr('value', data.iStreamLastId);
        }
        if (!data.events_count) {
            $('#stream_get_more').hide();
        }
        $('#stream_get_more').removeClass('stream_loading');
        ls.hook.run('ls_stream_get_more_by_user_after', [lastId, iUserId, data]);
        this.isBusy = false;
    }.bind(this));
};
