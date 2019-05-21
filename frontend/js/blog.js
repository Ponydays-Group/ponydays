import * as Lang from "./lang"
import * as Msg from "./msg"
import Emitter from "./emitter"
import $ from "jquery"
import * as Ajax from "./ajax"

/**
 * JS функционал для блогов
 */

/**
 * Вступить или покинуть блог
 */
export function toggleJoin(obj, idBlog) {
    const url = aRouter["blog"] + "ajaxblogjoin/";
    const params = {idBlog: idBlog};

    Emitter.emit("blog_togglejoin_before");
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            obj = $(obj);
            Msg.notice(null, result.sMsg);

            const text = result.bState
                ? Lang.get("blog_leave")
                : Lang.get("blog_join")
            ;

            obj.empty().text(text);
            obj.toggleClass("active");

            $("#blog_user_count_" + idBlog).text(result.iCountUser);
            Emitter.emit("blog_togglejoin_after", [idBlog, result], obj);
        }
    });
}

/**
 * Восстановить блог
 */
export function restoreBlog(obj, idBlog) {
    const url = aRouter["blog"] + "restore/" + idBlog;
    const params = {idBlog: idBlog};

    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.notice(null, result.sMsg);
        } else {
            obj = $(obj);
        }
        location.reload();
    });
}

/**
 * Отправляет приглашение вступить в блог
 */
export function addInvite(idBlog) {
    const user_add_sel = $("#blog_admin_user_add");
    const sUsers = user_add_sel.val();
    if(!sUsers) return false;
    user_add_sel.val("");

    const url = aRouter["blog"] + "ajaxaddbloginvite/";
    const params = {users: sUsers, idBlog: idBlog};

    Emitter.emit("blog_addinvite_before");
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $.each(result.aUsers, function(index, item) {
                if(item.bStateError) {
                    Msg.error(null, item.sMsg);
                } else {
                    const invited_list_sel = $("#invited_list");
                    if(invited_list_sel.length === 0) {
                        $("#invited_list_block").append($("<ul class=\"list\" id=\"invited_list\"></ul>"));
                    }
                    const listItem = $("<li><a href=\"" + item.sUserWebPath + "\" class=\"user\">" + item.sUserLogin + "</a></li>");
                    invited_list_sel.append(listItem);
                    $("#blog-invite-empty").hide();
                    Emitter.emit("blog_addinvite_user_after", [idBlog, item], listItem);
                }
            });
            Emitter.emit("blog_addinvite_after", [idBlog, sUsers, result]);
        }
    });

    return false;
}


/**
 * Повторно отправляет приглашение
 */
export function repeatInvite(idUser, idBlog) {
    const url = aRouter["blog"] + "ajaxrebloginvite/";
    const params = {idUser: idUser, idBlog: idBlog};

    Emitter.emit("blog_repeatinvite_before");
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            Msg.notice(null, result.sMsg);
            Emitter.emit("blog_repeatinvite_after", [idUser, idBlog, result]);
        }
    });

    return false;
}


/**
 * Удаляет приглашение в блог
 */
export function removeInvite(idUser, idBlog) {
    const url = aRouter["blog"] + "ajaxremovebloginvite/";
    const params = {idUser: idUser, idBlog: idBlog};

    Emitter.emit("blog_removeinvite_before");
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $("#blog-invite-remove-item-" + idBlog + "-" + idUser).remove();
            Msg.notice(null, result.sMsg);
            if($("#invited_list li").length === 0) $("#blog-invite-empty").show();
            Emitter.emit("blog_removeinvite_after", [idUser, idBlog, result]);
        }
    });

    return false;
}


/**
 * Отображение информации о блоге
 */
export function loadInfo(idBlog) {
    const url = aRouter["blog"] + "ajaxbloginfo/";
    const params = {idBlog: idBlog};

    Emitter.emit("blog_loadinfo_before");
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            const block = $("#block_blog_info");
            block.html(result.sText);
            Emitter.emit("blog_loadinfo_after", [idBlog, result], block);
        }
    });
}


/**
 * Отображение информации о типе блога
 */
export function loadInfoType(type) {
    $("#blog_type_note").text(Lang.get("blog_create_type_" + type + "_notice"));
}


/**
 * Поиск блогов
 */
export function searchBlogs(form) {
    const url = aRouter["blogs"] + "ajax-search/";
    const inputSearch = $("#" + form).find("input");
    inputSearch.addClass("loader");

    Emitter.emit("blog_searchblogs_before");
    Ajax.ajaxSubmit(url, form, function(result) {
        inputSearch.removeClass("loader");
        if(result.bStateError) {
            $("#blogs-list-search").hide();
            $("#blogs-list-original").show();
        } else {
            $("#blogs-list-original").hide();
            $("#blogs-list-search").html(result.sText).show();
            Emitter.emit("blog_searchblogs_after", [form, result]);
        }
    });
}


/**
 * Показать подробную информацию о блоге
 */
export function toggleInfo() {
    $("#blog-more-content").slideToggle();
    const more = $("#blog-more");
    more.toggleClass("expanded");

    if(more.hasClass("expanded")) {
        more.html(Lang.get("blog_fold_info"));
    } else {
        more.html(Lang.get("blog_expand_info"));
    }

    return false;
}
