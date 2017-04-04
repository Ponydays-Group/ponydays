import $ from "jquery";

import * as Registry from './registry'
import * as Blocks from './blocks'
import Emitter from './emitter'
import * as Msg from './msg'
import * as Ajax from "./ajax"
import ReactDOM from 'react-dom'
import React from 'react'

import Tree from './Tree.component'

var dateFormat = require('dateformat');

/**
 * Опции
 */
export let options = {
    type: {
        topic: {
            url_add: aRouter.blog + 'ajaxaddcomment/',
            url_response: aRouter.blog + 'ajaxresponsecomment/'
        },
        talk: {
            url_add: aRouter.talk + 'ajaxaddcomment/',
            url_response: aRouter.talk + 'ajaxresponsecomment/'
        }
    },
    classes: {
        form_loader: 'loader',
        comment_new: 'comment-new',
        comment_current: 'comment-current',
        comment_deleted: 'comment-deleted',
        comment_self: 'comment-self',
        comment: 'comment',
        comment_goto_parent: 'goto-comment-parent',
        comment_goto_child: 'goto-comment-child'
    },
    wysiwyg: null,
    folding: true,
    pageTitle: false // for comment count: original title here
};

export let sBStyle
export let cbsclick
export let iCurrentShowFormComment = 0;
export let iCurrentViewComment = null;
export let aCommentNew = [];
export let aCommentOld = [];
export let lastNewComment = 0;

export async function loadComments() {
    let url = ""
    if (location.pathname.startsWith(aRouter["talk"])) {
        url = location.href.replace("read", "readcomments")
    } else {
        url = window.location.pathname + "/comments"
    }
    return await Ajax.asyncAjax(url, {})
}

export async function renderComments() {
    let result = await loadComments()
    let comments = result.aComments
    let ids = []
    for (let id in comments) {
        ids.push(id)
    }
    $("#comment_last_id").val(result.iMaxIdComment)
    let CommentsTree = new Tree()
    CommentsTree.mount($("#comments-tree")[0], comments, ids)
    //ReactDOM.render(<CommentsTree ids={ids} comments={comments}/>, $("#comments-tree")[0])
    calcNewComments()
    resize_sidebar()
    if (location.hash.startsWith('#comment')) {
        setTimeout(scrollToComment(location.hash.replace('#comment', '')), 2000)
    }
}

// Добавляет комментарий
export function add(formObj, targetId, targetType) {
    if (options.wysiwyg) {
        $('#' + formObj + ' textarea').val(tinyMCE.activeEditor.getContent());
    }
    formObj = $('#' + formObj);

    $('#form_comment_text').addClass(options.classes.form_loader).attr('readonly', true);
    $('#comment-button-submit').attr('disabled', 'disabled');

    Ajax.ajax(options.type[targetType].url_add, formObj.serializeJSON(), function(result) {
        $('#comment-button-submit').removeAttr('disabled');
        if (!result) {
            enableFormComment();
            Msg.error('Error', 'Please try again later');
            return;
        }
        if (result.bStateError) {
            enableFormComment();
            Msg.error(null, result.sMsg);
        } else {
            enableFormComment();
            $('#form_comment_text').val('');

            // Load new comments
            load(targetId, targetType, result.sCommentId, true);
            Emitter.emit('ls_comments_add_after', [formObj, targetId, targetType, result]);
        }
    }.bind(this));
}

// Активирует форму
export function enableFormComment() {
    $('#form_comment_text').removeClass(options.classes.form_loader).attr('readonly', false);
}

// Показывает/скрывает форму комментирования
export function _toggleCommentForm(idComment, bNoFocus) {
    var reply = $('#reply');
    if (!reply.length) {
        return;
    }
    $('#comment_preview_' + iCurrentShowFormComment).remove();

    if (iCurrentShowFormComment == idComment && reply.is(':visible')) {
        reply.hide();
        return;
    }
    if (options.wysiwyg) {
        tinyMCE.execCommand('mceRemoveControl', true, 'form_comment_text');
    }
    let comment = $('#comment_id_' + idComment)
    reply.insertAfter(comment).show();
    reply.css("marginLeft", (comment.data("level") + 1) * 20)
    if (!comment) {
        reply.css("marginLeft", 0)
    }
    $('#form_comment_text').val('');
    $('#form_comment_reply').val(idComment);

    iCurrentShowFormComment = idComment;
    if (options.wysiwyg) {
        tinyMCE.execCommand('mceAddControl', true, 'form_comment_text');
    }
    if (!bNoFocus)
        $('#form_comment_text').focus();
    }

// Подгружает новые комментарии
export function load(idTarget, typeTarget, selfIdComment, bNotFlushNew) {
    //if (options.loadMutex) return;
    //options.loadMutex = true;
    console.log("Start load", dateFormat(new Date(), "HH:MM:ss:l"))

    var idCommentLast = $("#comment_last_id").val();
    if (aCommentNew != []) {
        aCommentOld = aCommentNew;
    }

    // Удаляем подсветку у комментариев
    if (!bNotFlushNew) {
        $('.comment').each(function(index, item) {
            $(item).removeClass(options.classes.comment_new + ' ' + options.classes.comment_current);
        }.bind(this));
    }

    let objImg = $('#update-comments');
    objImg.addClass('fa-pulse');

    var params = {
        idCommentLast: idCommentLast,
        idTarget: idTarget,
        typeTarget: typeTarget
    };
    if (selfIdComment) {
        params.selfIdComment = selfIdComment;
    }

    console.log("Before ajax", dateFormat(new Date(), "HH:MM:ss:l"))
    Ajax.ajax(options.type[typeTarget].url_response, params, function(result) {
        console.log("Ajax catched", dateFormat(new Date(), "HH:MM:ss:l"))
        objImg.removeClass('fa-pulse');

        if (!result) {
            Msg.error('Error', 'Please try again later');
        }
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            var aCmt = result.aComments;
            console.log("Ajax OK", dateFormat(new Date(), "HH:MM:ss:l"))
            if (Object.keys(aCmt).length > 0 && result.iMaxIdComment) {
                Emitter.emit("comments-new-loaded", result.aComments)
                $("#comment_last_id").val(result.iMaxIdComment);
                $('#count-comments').text(parseInt($('#count-comments').text()) + Object.keys(aCmt).length);
            }
            if (Object.keys(result.aEditedComments).length > 0) {
                Emitter.emit("comments-edited-loaded", result.aEditedComments)
            }
            var iCountOld = 0;
            if (bNotFlushNew) {
                iCountOld = aCommentNew.length;
            } else {
                aCommentNew = [];
            }
            if (selfIdComment) {
                toggleCommentForm(iCurrentShowFormComment, true);
                setCountNewComment(aCmt.length - 1 + iCountOld);
            } else {
                setCountNewComment(aCmt.length + iCountOld);
            }

            if (selfIdComment && $('#comment_id_' + selfIdComment).length) {
                scrollToComment(selfIdComment);
            }
            // checkFolding();
            aCommentNew = [];
            calcNewComments();
            //if (aCmt.length>0) {
            // Emitter.emit('ls_comments_load_after', [idTarget, typeTarget, selfIdComment, bNotFlushNew, result]);
            //}

            var new_messages = document.getElementById("new_messages");
            var pm_title = "";
            if (result.iUserCurrentCountTalkNew > 0) {
                $(".toolbar-talk").css("display", "block")
                $(".toolbar-talk a")[0].title = `+${result.iUserCurrentCountTalkNew}`
                new_messages.classList.add("new-messages");
                pm_title = " (" + result.iUserCurrentCountTalkNew.toString() + ")";
            } else {
                $(".toolbar-talk").css("display", "none")
                new_messages.classList.remove("new-messages");
            }
            new_messages.childNodes[0].textContent = pm_title;
            new_messages.parentNode.title = pm_title;
            console.log("Ajax done", dateFormat(new Date(), "HH:MM:ss:l"))
        }
        var curItemBlock = Blocks.getCurrentItem('stream');
        if (aCmt.length > 0) {
            Blocks.load(curItemBlock, 'stream');
            console.log("Load done", dateFormat(new Date(), "HH:MM:ss:l"))
        }
    }.bind(this));
}

// Вставка комментария
export function inject(idCommentParent, idComment, sHtml) {
    var newComment = $('<div>', {
        'class': 'comment-wrapper',
        id: 'comment_wrapper_id_' + idComment
    }).html(sHtml);
    if (idCommentParent) {
        // Уровень вложенности родителя
        var iCurrentTree = $('#comment_wrapper_id_' + idCommentParent).parentsUntil('#comments').length;
        if (iCurrentTree == Registry.get('comment_max_tree')) {
            // Определяем id предыдушего родителя
            var prevCommentParent = $('#comment_wrapper_id_' + idCommentParent).parent();
            idCommentParent = parseInt(prevCommentParent.attr('id').replace('comment_wrapper_id_', ''));
        }
        $('#comment_wrapper_id_' + idCommentParent).append(newComment);
    } else {
        $('#comments').append(newComment);
    }
    Emitter.emit('ls_comment_inject_after', arguments, newComment);
}

// Удалить/восстановить комментарий
export function toggle(obj, commentId) {
    var url = aRouter['ajax'] + 'comment/delete/';
    var params = {
        idComment: commentId
    };

    Emitter.emit('toggleBefore');
    Ajax.ajax(url, params, function(result) {
        if (!result) {
            Msg.error('Error', 'Please try again later');
        }
        if (result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            Msg.notice(null, result.sMsg);

            $('#comment_id_' + commentId).removeClass(options.classes.comment_self + ' ' + options.classes.comment_new + ' ' + options.classes.comment_deleted + ' ' + options.classes.comment_current);
            if (result.bState) {
                $('#comment_id_' + commentId).addClass(options.classes.comment_deleted);
            }
            $(obj).text(result.sTextToggle);
            Emitter.emit('ls_comments_toggle_after', [obj, commentId, result]);
        }
    }.bind(this));
};

// Предпросмотр комментария
export function preview(divPreview) {
    if (options.wysiwyg) {
        $("#form_comment_text").val(tinyMCE.activeEditor.getContent());
    }
    if ($("#form_comment_text").val() == '')
        return;
    $("#comment_preview_" + iCurrentShowFormComment).remove();
    $('#reply').before('<div id="comment_preview_' + iCurrentShowFormComment + '" class="comment-preview text"></div>');
    ls.tools.textPreview('form_comment_text', false, 'comment_preview_' + iCurrentShowFormComment);
}

export function isCollapsed(el) {
    if (el.closest(".collapsed")) {
        return true;
    }
    return false;
}

// Устанавливает число новых комментариев
export function setCountNewComment(count) {
    // TODO that will work good only if there are no any other title modificators!
    if (!options.pageTitle)
        options.pageTitle = document.title;
    if (count > 0) {
        $('#new_comments_counter').show().text(count);
        if (document.getElementById('autoload').checked) {
            document.title = '(' + count + ') ' + options.pageTitle;
        }
    } else {
        $('#new_comments_counter').text(0).hide();
        document.title = options.pageTitle;
    }
}

// Вычисляет кол-во новых комментариев
export function calcNewComments() {
    var aCommentsNew = $('.' + options.classes.comment + '.' + options.classes.comment_new);
    $.each(aCommentsNew, function(k, v) {
        //console.log(isCollapsed(v));
    }.bind(this));

    let count = aCommentsNew.length;
    $.each(aCommentsNew, function(k, v) {
        if (!isCollapsed(v)) {
            aCommentNew.push(parseInt($(v).attr('id').replace('comment_id_', '')));
        } else {
            count--;
        }
    }.bind(this));
    setCountNewComment(count);
}

// Переход к следующему комментарию
export function goToNextComment() {
    if (lastNewComment>0) {
        aCommentOld.push(lastNewComment)
    }
    if (aCommentNew[0]) {
        if ($('#comment_id_' + aCommentNew[0]).length) {
            scrollToComment(aCommentNew[0]);
            $('#comment_id_' + aCommentNew[0]).removeClass(options.classes.comment_new);
        }
        lastNewComment = aCommentNew.shift();
    }
    setCountNewComment(aCommentNew.length);
}

export function goToPrevComment() {
    if (!aCommentOld.length) {
        return
    }
    scrollToComment(aCommentOld.pop())
}

// Прокрутка к комментарию
export function scrollToComment(idComment) {
    $('html, body').animate({
        scrollTop: $('#comment_id_' + idComment).offset().top - 250
    }, 150);

    if (iCurrentViewComment) {
        $('#comment_id_' + iCurrentViewComment).removeClass(options.classes.comment_current);
        $('#comment_id_' + iCurrentViewComment).removeClass(options.classes.comment_new);
    }
    $('#comment_id_' + idComment).addClass(options.classes.comment_current);
    iCurrentViewComment = idComment;
}

// Прокрутка к родительскому комментарию
export function goToParentComment(id, pid) {
    let thisObj = this;
    $('.' + options.classes.comment_goto_child).hide().find('a').unbind();

    $("#comment_id_" + pid).find('.' + options.classes.comment_goto_child).show().find("a").bind("click", function() {
        $(this).parent('.' + thisObj.options.classes.comment_goto_child).hide();
        thisObj.scrollToComment(id);
        return false;
    });
    scrollToComment(pid);
    return false;
}

// Сворачивание комментариев
export function checkFolding() {
    //if(!options.folding){
    //	return false;
    //}
    $(".folding").each(function(index, element) {
        if ($(element).parent(".comment").next(".comment-wrapper").length == 0) {
            $(element).hide();
        } else {
            $(element).show();
        }
    }).off("click").click(function(x) {
        if (x.target.className == "folding fa fa-minus-square") {
            collapseComment(x.target)
        } else {
            expandComment(x.target)
        }
    });
    return false;
}

export function expandComment(folding) {
    $(folding).removeClass("fa-plus-square").addClass("fa-minus-square").parent().nextAll(".comment-wrapper").show().removeClass("collapsed");
}

export function collapseComment(folding) {
    $(folding).removeClass("fa-minus-square").addClass("fa-plus-square").parent().nextAll(".comment-wrapper").hide().addClass("collapsed");
}

export function expandCommentAll() {
    $.each($(".folding"), function(k, v) {
        expandComment(v);
    }.bind(this));
}

export function collapseCommentAll() {
    $.each($(".folding"), function(k, v) {
        collapseComment(v);
    }.bind(this));
}

export function init() {
    initEvent();
    calcNewComments();
    checkFolding();
    toggleCommentForm(iCurrentShowFormComment);

    if (typeof(options.wysiwyg) != 'number') {
        options.wysiwyg = Boolean(BLOG_USE_TINYMCE && tinyMCE);
    }
    //Emitter.emit('ls_comments_init_after',[],this);
}

export function initEvent() {
    $('#form_comment_text').bind('keyup', function(e) {
        var key = e.keyCode || e.which;
        if (e.ctrlKey && (key == 13)) {
            $('#comment-button-submit').click();
            return false;
        }
    });

    if (options.folding) {
        $(".folding").click(function(e) {
            if ($(e.target).hasClass("folded")) {
                expandComment(e.target);
            } else {
                collapseComment(e.target);
            }
        }.bind(this));
    }
}

export function toggleCommentForm(idComment, bNoFocus) {
    if (typeof(sBStyle) != 'undefined')
        $('#comment-button-submit').css('display', sBStyle);
    if (typeof(cbsclick) != 'undefined') {
        $('#comment-button-submit').unbind('click');
        $('#comment-button-submit').attr('onclick', cbsclick);
    }

    var b = $('#comment-button-submit-edit');
    if (b.length)
        b.remove();

    b = $('#comment-button-history');
    if (b.length)
        b.remove();

    b = $('#comment-button-cancel');
    if (b.length)
        b.remove();

    _toggleCommentForm(idComment, bNoFocus);
}

export function cancelEditComment(idComment) {
    var reply = $('#reply');
    if (!reply.length) {
        return;
    }

    reply.hide();
    setFormText('');
}

export function editComment(idComment) {
    var reply = $('#reply');
    if (!reply.length) {
        return;
    }

    if (!(iCurrentShowFormComment == idComment && reply.is(':visible'))) {
        var thisObj = this;
        $('#comment_content_id_' + idComment).addClass(thisObj.options.classes.form_loader);
        Ajax.ajax(aRouter.ajax + 'editcomment-getsource/', {
            'idComment': idComment
        }, function(result) {
            $('#comment_content_id_' + idComment).removeClass(thisObj.options.classes.form_loader);
            if (!result) {
                Msg.error('Error', 'Please try again later');
                return;
            }
            if (result.bStateError) {
                Msg.error(null, result.sMsg);
            } else {
                toggleCommentForm(idComment);
                sBStyle = $('#comment-button-submit').css('display');
                var cbs = $('#comment-button-submit');
                cbs.css('display', 'none');
                cbsclick = $('#comment-button-submit').attr('onclick');

                $('#comment-button-submit').attr('onclick', "");
                $('#comment-button-submit').bind('click', function() {
                    $('#comment-button-submit-edit').click();
                    return false;
                });

                cbs.after($(thisObj.options.cancel_button_code));

                cbs.after($(thisObj.options.edit_button_code));
                ls.comments.setFormText(result.sCommentSource);

                thisObj.enableFormComment();
            }
        });
    } else {
        reply.hide();
        return;
    }
}

export function setFormText(sText) {
    if (options.wysiwyg) {
        tinyMCE.execCommand('mceRemoveControl', false, 'form_comment_text');
        $('#form_comment_text').val(sText);
        tinyMCE.execCommand('mceAddControl', true, 'form_comment_text');
    } else if (typeof($('#form_comment_text').getObject) == 'function') {
        $('#form_comment_text').destroyEditor();
        $('#form_comment_text').val(sText);
        $('#form_comment_text').redactor();
    } else
        $('#form_comment_text').val(sText);
    }

export function edit(formObject, targetId, targetType) {
    if (options.wysiwyg) {
        $('#' + formObj + ' textarea').val(tinyMCE.activeEditor.getContent());
    } else if (typeof($('#form_comment_text').getObject) == 'function') {
        $('#' + formObj + ' textarea').val($('#form_comment_text').getCode());
    }
    var formObj = $('#' + formObject);

    $('#form_comment_text').addClass(options.classes.form_loader).attr('readonly', true);
    $('#comment-button-submit').attr('disabled', 'disabled');

    var lData = formObj.serializeJSON();
    var idComment = lData.reply;

    Ajax.ajax(aRouter.ajax + 'editcomment-edit/', lData, function(result) {
        $('#comment-button-submit').removeAttr('disabled');
        if (!result) {
            enableFormComment();
            Msg.error('Error', 'Please try again later');
            return;
        }
        if (result.bStateError) {
            enableFormComment();
            Msg.error(null, result.sMsg);
        } else {
            if (result.sMsg)
                Msg.notice(null, result.sMsg);

            enableFormComment();
            setFormText('');

            // Load new comments
            if (result.bEdited) {
                $('#comment_content_id_' + idComment).html(result.sCommentText);
            }
            if (!result.bCanEditMore)
                $('#comment_id_' + idComment).find('.editcomment_editlink').remove();
            load(targetId, targetType, idComment, true);
            if (Blocks) {
                var curItemBlock = Blocks.getCurrentItem('stream');
                if (curItemBlock.data('type') == 'comment') {
                    Blocks.load(curItemBlock, 'stream');
                }
            }

            Emitter.emit('ls_comments_edit_after', [formObj, targetId, targetType, result]);
        }
    }.bind(this));
}

export function showHistory(cId) {
    var formObj = $('#form_comment');

    $('#form_comment_text').addClass(options.classes.form_loader).attr('readonly', true);
    $('#comment-button-submit-edit').attr('disabled', 'disabled');

    var lData = formObj.serializeJSON();
    lData.form_comment_text = '';
    lData.reply = cId || lData.reply;
    var idComment = cId || lData.reply;
    Ajax.ajax(aRouter.ajax + 'editcomment-gethistory/', lData, function(result) {
        $('#comment-button-submit-edit').removeAttr('disabled');
        if (!result) {
            enableFormComment();
            Msg.error('Error', 'Please try again later');
            return;
        }
        if (result.bStateError) {
            enableFormComment();
            Msg.error(null, result.sMsg);
        } else {
            if (result.sMsg)
                Msg.notice(null, result.sMsg);

            enableFormComment();
            $('#editcomment-history-content').html(result.sContent);
            $('#modal-editcomment-history').jqmShow();
        }
    }.bind(this));
}

// export function init_editcomment = function () {
//        toggleCommentForm = that.superior("toggleCommentForm");
//        ls.comments.toggleCommentForm = mytoggleCommentForm;
//    }
