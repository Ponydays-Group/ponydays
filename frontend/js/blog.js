import * as Lang from './lang'
import * as Msg from './msg'
import * as Hook from './hook'
import $ from "jquery"
import * as Ajax from './ajax'

/**
 * JS функционал для блогов
 */

/**
 * Вступить или покинуть блог
 */
export function toggleJoin(obj, idBlog) {
    let url = aRouter['blog'] + 'ajaxblogjoin/';
    let params = {idBlog: idBlog};

    Hook.marker('toggleJoinBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            obj = $(obj);
            Msg.notice(null, result.sMsg);

            let text = result.bState
                    ? Lang.get('blog_leave')
                    : Lang.get('blog_join')
                ;

            obj.empty().text(text);
            obj.toggleClass('active');

            $('#blog_user_count_' + idBlog).text(result.iCountUser);
            Hook.run('ls_blog_toggle_join_after', [idBlog, result], obj);
        }
    });
}


/**
 * Отправляет приглашение вступить в блог
 */
export function addInvite(idBlog) {
    let sUsers = $('#blog_admin_user_add').val();
    if (!sUsers) return false;
    $('#blog_admin_user_add').val('');

    let url = aRouter['blog'] + 'ajaxaddbloginvite/';
    let params = {users: sUsers, idBlog: idBlog};

    Hook.marker('addInviteBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $.each(result.aUsers, function (index, item) {
                if (item.bStateError) {
                    Msg.error(null, item.sMsg);
                } else {
                    if ($('#invited_list').length == 0) {
                        $('#invited_list_block').append($('<ul class="list" id="invited_list"></ul>'));
                    }
                    let listItem = $('<li><a href="' + item.sUserWebPath + '" class="user">' + item.sUserLogin + '</a></li>');
                    $('#invited_list').append(listItem);
                    $('#blog-invite-empty').hide();
                    Hook.run('ls_blog_add_invite_user_after', [idBlog, item], listItem);
                }
            });
            Hook.run('ls_blog_add_invite_after', [idBlog, sUsers, result]);
        }
    });

    return false;
}


/**
 * Повторно отправляет приглашение
 */
export function repeatInvite(idUser, idBlog) {
    let url = aRouter['blog'] + 'ajaxrebloginvite/';
    let params = {idUser: idUser, idBlog: idBlog};

    Hook.marker('repeatInviteBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            Msg.notice(null, result.sMsg);
            Hook.run('ls_blog_repeat_invite_after', [idUser, idBlog, result]);
        }
    });

    return false;
}


/**
 * Удаляет приглашение в блог
 */
export function removeInvite(idUser, idBlog) {
    let url = aRouter['blog'] + 'ajaxremovebloginvite/';
    let params = {idUser: idUser, idBlog: idBlog};

    Hook.marker('removeInviteBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $('#blog-invite-remove-item-' + idBlog + '-' + idUser).remove();
            Msg.notice(null, result.sMsg);
            if ($('#invited_list li').length == 0) $('#blog-invite-empty').show();
            Hook.run('ls_blog_remove_invite_after', [idUser, idBlog, result]);
        }
    });

    return false;
}


/**
 * Отображение информации о блоге
 */
export function loadInfo(idBlog) {
    let url = aRouter['blog'] + 'ajaxbloginfo/';
    let params = {idBlog: idBlog};

    Hook.marker('loadInfoBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            let block = $('#block_blog_info');
            block.html(result.sText);
            Hook.run('ls_blog_load_info_after', [idBlog, result], block);
        }
    });
}


/**
 * Отображение информации о типе блога
 */
export function loadInfoType(type) {
    $('#blog_type_note').text(Lang.get('blog_create_type_' + type + '_notice'));
}


/**
 * Поиск блогов
 */
export function searchBlogs(form) {
    let url = aRouter['blogs'] + 'ajax-search/';
    let inputSearch = $('#' + form).find('input');
    inputSearch.addClass('loader');

    Hook.marker('searchBlogsBefore');
    Ajax.ajaxSubmit(url, form, function (result) {
        inputSearch.removeClass('loader');
        if (result.bStateError) {
            $('#blogs-list-search').hide();
            $('#blogs-list-original').show();
        } else {
            $('#blogs-list-original').hide();
            $('#blogs-list-search').html(result.sText).show();
            Hook.run('ls_blog_search_blogs_after', [form, result]);
        }
    });
}


/**
 * Показать подробную информацию о блоге
 */
export function toggleInfo() {
    $('#blog-more-content').slideToggle();
    let more = $('#blog-more');
    more.toggleClass('expanded');

    if (more.hasClass('expanded')) {
        more.html(Lang.get('blog_fold_info'));
    } else {
        more.html(Lang.get('blog_expand_info'));
    }

    return false;
}
