import * as Lang from './lang'
import Emitter from './emitter'
import * as Msg from './msg'
import $ from 'jquery'
import * as Ajax from './ajax'

/**
 * Функционал личных сообщений
 */
export function markAsRead(id) {
    Ajax.ajax(DIR_WEB_ROOT + 'talk/ajaxmarkasread',
        {"target": id},
        function (result) {
            if (result.bStateError) {
                Msg.error(null, result.sMsg);
            } else {
                Msg.notice(null, result.sMsg);
            }
        }
    );
}
/**
 * Добавляет пользователя к переписке
 */
export function addToTalk(idTalk) {
    var sUsers = $('#talk_speaker_add').val();
    if (!sUsers) return false;
    $('#talk_speaker_add').val('');

    var url = aRouter['talk'] + 'ajaxaddtalkuser/';
    var params = {users: sUsers, idTalk: idTalk};

    Emitter.emit('addToTalkBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $.each(result.aUsers, function (index, item) {
                if (item.bStateError) {
                    Msg.notice(null, item.sMsg);
                } else {
                    var list = $('#speaker_list');
                    if (list.length == 0) {
                        list = $('<ul class="list" id="speaker_list"></ul>');
                        $('#speaker_list_block').append(list);
                    }
                    var listItem = $('<li id="speaker_item_' + item.sUserId + '_area"><a href="' + item.sUserLink + '" class="user">' + item.sUserLogin + '</a> - <a href="#" id="speaker_item_' + item.sUserId + '" class="delete">' + Lang.get('delete') + '</a></li>')
                    list.append(listItem);
                    Emitter.emit('ls_talk_add_to_talk_item_after', [idTalk, item], listItem);
                }
            });

            Emitter.emit('ls_talk_add_to_talk_after', [idTalk, result]);
        }
    });
    return false;
};

/**
 * Удаляет или приглашает обратно пользователя из переписки
 */
export function removeFromTalk(link, idTalk) {
    link = $(link);
    if (link.attr('id').includes('restore')) {

        /**
         * Приглашает пользователя обратно в переписку
         */

        var idTarget = link.attr('id').replace('speaker_restore_item_', '');

        var url = aRouter['talk'] + 'ajaxinvitetalkuserback/';
        var params = {idTarget: idTarget, idTalk: idTalk};

        $('#speaker_item_' + idTarget + '_area > #speaker_restore_item_' + idTarget).html('Приглашен');
        $('#speaker_item_' + idTarget + '_area > .user').addClass('inactive');

        Ajax.ajax(url, params, function (result) {
            if (!result) {
                Msg.error('Error', 'Please try again later');
                link.parent('li').show();
            }
            if (result.bStateError) {
                Msg.error(null, result.sMsg);
                link.parent('li').show();
            } else {
                Msg.notice(null, result.sMsg);
            }
        });
    } else {
        /**
         * Удаляет пользователя из переписки
         */
        var idTarget = link.attr('id').replace('speaker_item_', '');

        var url = aRouter['talk'] + 'ajaxdeletetalkuser/';
        var params = {idTarget: idTarget, idTalk: idTalk};

        $('#speaker_item_' + idTarget + '_area > #speaker_item_' + idTarget).attr("id","speaker_restore_item_" + idTarget).html('Восстановить');
        $('#speaker_item_' + idTarget + '_area > .user').addClass('inactive');

        Emitter.emit('removeFromTalkBefore');
        Ajax.ajax(url, params, function (result) {
            if (!result) {
                Msg.error('Error', 'Please try again later');
                link.parent('li').show();
            }
            if (result.bStateError) {
                Msg.error(null, result.sMsg);
                link.parent('li').show();
            } else {
                Msg.notice(null, result.sMsg);
            }
            Emitter.emit('ls_talk_remove_from_talk_after', [idTalk, idTarget], link);
        });
    }

    return false;
};

/**
 * Принимает приглашение обратно в переписку
 */
export function acceptInviteBackToTalk(link) {
    link = $(link);

    var idTalk = link.attr('idTalk');

    var idTarget = link.attr('id').replace('speaker_accept_restore_item_', '');

    var url = aRouter['talk'] + 'ajaxacceptinvitetalkuserback/';
    var params = {idTarget: idTarget, idTalk: idTalk};

    Ajax.ajax(url, params, function (result) {
        if (!result) {
            Msg.error('Error', 'Please try again later');
            link.parent('li').show();
        }
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
            link.parent('li').show();
        } else {
            Msg.notice(null, result.sMsg);
        }
    });

    return false;
};

/**
 * Добавляет пользователя в черный список
 */
export function addToBlackList() {
    var sUsers = $('#talk_blacklist_add').val();
    if (!sUsers) return false;
    $('#talk_blacklist_add').val('');

    var url = aRouter['talk'] + 'ajaxaddtoblacklist/';
    var params = {users: sUsers};

    Emitter.emit('addToBlackListBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $.each(result.aUsers, function (index, item) {
                if (item.bStateError) {
                    Msg.notice(null, item.sMsg);
                } else {
                    var list = $('#black_list');
                    if (list.length == 0) {
                        list = $('<ul class="list" id="black_list"></ul>');
                        $('#black_list_block').append(list);
                    }
                    var listItem = $('<li id="blacklist_item_' + item.sUserId + '_area"><a href="#" class="user">' + item.sUserLogin + '</a> - <a href="#" id="blacklist_item_' + item.sUserId + '" class="delete">' + Lang.get('delete') + '</a></li>');
                    $('#black_list').append(listItem);
                    Emitter.emit('ls_talk_add_to_black_list_item_after', [item], listItem);
                }
            });
            Emitter.emit('ls_talk_add_to_black_list_after', [result]);
        }
    });
    return false;
};

/**
 * Удаляет пользователя из черного списка
 */
export function removeFromBlackList(link) {
    link = $(link);

    $('#' + link.attr('id') + '_area').fadeOut(500, function () {
        $(this).remove();
    });
    var idTarget = link.attr('id').replace('blacklist_item_', '');

    var url = aRouter['talk'] + 'ajaxdeletefromblacklist/';
    var params = {idTarget: idTarget};

    Emitter.emit('removeFromBlackListBefore');
    Ajax.ajax(url, params, function (result) {
        if (!result) {
            Msg.error('Error', 'Please try again later');
            link.parent('li').show();
        }
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
            link.parent('li').show();
        }
        Emitter.emit('ls_talk_remove_from_black_list_after', [idTarget], link);
    });
    return false;
};

function removeA(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax= arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}
/**
 * Добавляет или удаляет друга из списка получателей
 */
export function toggleRecipient(login, add) {
    var to = $.map($('#talk_users').val().split(','), function (item, index) {
        item = $.trim(item);
        return item != '' ? item : null;
    });
    console.log(login,to)
    if (add) {
        to.push(login);
        console.log(login,to)
        to = $.uniqueSort(to);
    } else {
        console.log(login,to,"Remove")
        to = removeA(to, login);
    }
    $('#talk_users').val(to.join(', '));
};

/**
 * Очищает поля фильтра
 */
export function clearFilter() {
    $('#block_talk_search_content').find('input[type="text"]').val('');
    $('#block_talk_search_content').find('input[type="checkbox"]').removeAttr("checked");
    return false;
};

/**
 * Удаление списка писем
 */
export function removeTalks() {
    if ($('.form_talks_checkbox:checked').length == 0) {
        return false;
    }
    $('#form_talks_list_submit_del').val(1);
    $('#form_talks_list_submit_read').val(0);
    $('#form_talks_list').submit();
    return false;
};

/**
 * Пометка о прочтении писем
 */
export function makeReadTalks() {
    if ($('.form_talks_checkbox:checked').length == 0) {
        return false;
    }
    $('#form_talks_list_submit_read').val(1);
    $('#form_talks_list_submit_del').val(0);
    $('#form_talks_list').submit();
    return false;
};
