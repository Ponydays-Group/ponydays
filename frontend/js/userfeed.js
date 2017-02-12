import * as Msg from './msg'
import * as Hook from './hook'
import $ from 'jquery'
import * as Ajax from './ajax'

export let isBusy = false;

export function subscribe(sType, iId) {
    var url = aRouter['feed'] + 'subscribe/';
    var params = {'type': sType, 'id': iId};

    Hook.marker('subscribeBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Hook.run('ls_userfeed_subscribe_after', [sType, iId, data]);
        }
    });
}

export function subscribeAll() {
    var url = aRouter['feed'] + 'subscribe_all/';
    var params = {};

    Hook.marker('subscribeBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Hook.run('ls_userfeed_subscribe_all_after', [data]);
            location.reload();
        }
    });
}

export function unsubscribe(sType, iId) {
    var url = aRouter['feed'] + 'unsubscribe/';
    var params = {'type': sType, 'id': iId};

    Hook.marker('unsubscribeAllBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Hook.run('ls_userfeed_unsubscribe_after', [sType, iId, data]);
        }
    });
}

export function unsubscribeAll() {
    var url = aRouter['feed'] + 'unsubscribe_all/';
    var params = {};

    Hook.marker('unsubscribeAllBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Hook.run('ls_userfeed_unsubscribe_all_after', [data]);
            location.reload();
        }
    });
}

export function appendUser() {
    var sLogin = $('#userfeed_users_complete').val();
    if (!sLogin) return;

    var url = aRouter['feed'] + 'subscribeByLogin/';
    var params = {'login': sLogin};

    Hook.marker('appendUserBefore');
    Ajax.ajax(url, params, function (data) {
        if (data.bStateError) {
            Msg.error(data.sMsgTitle, data.sMsg);
        } else {
            $('#userfeed_no_subscribed_users').remove();
            var checkbox = $('#usf_u_' + data.uid);
            if (checkbox.length) {
                if (checkbox.attr('checked')) {
                    Msg.error(data.lang_error_title, data.lang_error_msg);
                    return;
                } else {
                    checkbox.attr('checked', 'on');
                    Msg.notice(data.sMsgTitle, data.sMsg);
                }
            } else {
                var liElement = $('<li><input type="checkbox" class="userfeedUserCheckbox input-checkbox" id="usf_u_' + data.uid + '" checked="checked" onClick="if ($(this).get(\'checked\')) {ls.userfeedsubscribe(\'users\',' + data.uid + ')} else {ls.userfeedunsubscribe(\'users\',' + data.uid + ')}" /><a href="' + data.user_web_path + '">' + data.user_login + '</a></li>');
                $('#userfeed_block_users_list').append(liElement);
                Msg.notice(data.sMsgTitle, data.sMsg);
            }
        }
    });
}

export function getMore() {
    if (this.isBusy) {
        return;
    }
    var lastId = $('#userfeed_last_id').val();
    if (!lastId) return;
    $('#userfeed_get_more').addClass('userfeed_loading');
    this.isBusy = true;

    var url = aRouter['feed'] + 'get_more/';
    var params = {'last_id': lastId};

    Hook.marker('getMoreBefore');
    Ajax.ajax(url, params, function (data) {
        if (!data.bStateError && data.topics_count) {
            $('#userfeed_loaded_topics').append(data.result);
            $('#userfeed_last_id').attr('value', data.iUserfeedLastId);
        }
        if (!data.topics_count) {
            $('#userfeed_get_more').hide();
        }
        $('#userfeed_get_more').removeClass('userfeed_loading');
        Hook.run('ls_userfeed_get_more_after', [lastId, data]);
        this.isBusy = false;
    }.bind(this));
}
