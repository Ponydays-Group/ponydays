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

    ls.hook.marker('toggleJoinBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            obj = $(obj);
            ls.msg.notice(null, result.sMsg);

            let text = result.bState
                    ? ls.lang.get('blog_leave')
                    : ls.lang.get('blog_join')
                ;

            obj.empty().text(text);
            obj.toggleClass('active');

            $('#blog_user_count_' + idBlog).text(result.iCountUser);
            ls.hook.run('ls_blog_toggle_join_after', [idBlog, result], obj);
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

    ls.hook.marker('addInviteBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            $.each(result.aUsers, function (index, item) {
                if (item.bStateError) {
                    ls.msg.error(null, item.sMsg);
                } else {
                    if ($('#invited_list').length == 0) {
                        $('#invited_list_block').append($('<ul class="list" id="invited_list"></ul>'));
                    }
                    let listItem = $('<li><a href="' + item.sUserWebPath + '" class="user">' + item.sUserLogin + '</a></li>');
                    $('#invited_list').append(listItem);
                    $('#blog-invite-empty').hide();
                    ls.hook.run('ls_blog_add_invite_user_after', [idBlog, item], listItem);
                }
            });
            ls.hook.run('ls_blog_add_invite_after', [idBlog, sUsers, result]);
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

    ls.hook.marker('repeatInviteBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            ls.msg.notice(null, result.sMsg);
            ls.hook.run('ls_blog_repeat_invite_after', [idUser, idBlog, result]);
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

    ls.hook.marker('removeInviteBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            $('#blog-invite-remove-item-' + idBlog + '-' + idUser).remove();
            ls.msg.notice(null, result.sMsg);
            if ($('#invited_list li').length == 0) $('#blog-invite-empty').show();
            ls.hook.run('ls_blog_remove_invite_after', [idUser, idBlog, result]);
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

    ls.hook.marker('loadInfoBefore');
    Ajax.ajax(url, params, function (result) {
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            let block = $('#block_blog_info');
            block.html(result.sText);
            ls.hook.run('ls_blog_load_info_after', [idBlog, result], block);
        }
    });
}


/**
 * Отображение информации о типе блога
 */
export function loadInfoType(type) {
    $('#blog_type_note').text(ls.lang.get('blog_create_type_' + type + '_notice'));
}


/**
 * Поиск блогов
 */
export function searchBlogs(form) {
    let url = aRouter['blogs'] + 'ajax-search/';
    let inputSearch = $('#' + form).find('input');
    inputSearch.addClass('loader');

    ls.hook.marker('searchBlogsBefore');
    Ajax.ajaxSubmit(url, form, function (result) {
        inputSearch.removeClass('loader');
        if (result.bStateError) {
            $('#blogs-list-search').hide();
            $('#blogs-list-original').show();
        } else {
            $('#blogs-list-original').hide();
            $('#blogs-list-search').html(result.sText).show();
            ls.hook.run('ls_blog_search_blogs_after', [form, result]);
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
        more.html(ls.lang.get('blog_fold_info'));
    } else {
        more.html(ls.lang.get('blog_expand_info'));
    }

    return false;
}
