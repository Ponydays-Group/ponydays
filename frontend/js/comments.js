import $ from "jquery";
/**
 * Обработка комментариев
 */

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

export let iCurrentShowFormComment = 0;
export let iCurrentViewComment = null;
export let aCommentNew = [];
export let aCommentOld = [];

// Добавляет комментарий
export function add(formObj, targetId, targetType) {
    if (this.options.wysiwyg) {
        $('#' + formObj + ' textarea').val(tinyMCE.activeEditor.getContent());
    }
    formObj = $('#' + formObj);

    $('#form_comment_text').addClass(this.options.classes.form_loader).attr('readonly', true);
    $('#comment-button-submit').attr('disabled', 'disabled');

    ls.ajax(this.options.type[targetType].url_add, formObj.serializeJSON(), function (result) {
        $('#comment-button-submit').removeAttr('disabled');
        if (!result) {
            this.enableFormComment();
            ls.msg.error('Error', 'Please try again later');
            return;
        }
        if (result.bStateError) {
            this.enableFormComment();
            ls.msg.error(null, result.sMsg);
        } else {
            this.enableFormComment();
            $('#form_comment_text').val('');

            // Load new comments
            this.load(targetId, targetType, result.sCommentId, true);
            ls.hook.run('ls_comments_add_after', [formObj, targetId, targetType, result]);
        }
    }.bind(this));
}


// Активирует форму
export function enableFormComment() {
    $('#form_comment_text').removeClass(this.options.classes.form_loader).attr('readonly', false);
}


// Показывает/скрывает форму комментирования
export function toggleCommentForm(idComment, bNoFocus) {
    var reply = $('#reply');
    if (!reply.length) {
        return;
    }
    $('#comment_preview_' + this.iCurrentShowFormComment).remove();

    if (this.iCurrentShowFormComment == idComment && reply.is(':visible')) {
        reply.hide();
        return;
    }
    if (this.options.wysiwyg) {
        tinyMCE.execCommand('mceRemoveControl', true, 'form_comment_text');
    }
    reply.insertAfter('#comment_id_' + idComment).show();
    $('#form_comment_text').val('');
    $('#form_comment_reply').val(idComment);

    this.iCurrentShowFormComment = idComment;
    if (this.options.wysiwyg) {
        tinyMCE.execCommand('mceAddControl', true, 'form_comment_text');
    }
    if (!bNoFocus) $('#form_comment_text').focus();

    if ($('html').hasClass('ie7')) {
        var inputs = $('input.input-text, textarea');
        ls.ie.bordersizing(inputs);
    }
}


// Подгружает новые комментарии
export function load(idTarget, typeTarget, selfIdComment, bNotFlushNew) {
    //if (this.options.loadMutex) return;
    //this.options.loadMutex = true;

    var idCommentLast = $("#comment_last_id").val();
    if (this.aCommentNew != []) {
        this.aCommentOld = this.aCommentNew;
    }

    // Удаляем подсветку у комментариев
    if (!bNotFlushNew) {
        $('.comment').each(function (index, item) {
            $(item).removeClass(this.options.classes.comment_new + ' ' + this.options.classes.comment_current);
        }.bind(this));
    }

    objImg = $('#update-comments');
    objImg.addClass('fa-pulse');

    var params = {idCommentLast: idCommentLast, idTarget: idTarget, typeTarget: typeTarget};
    if (selfIdComment) {
        params.selfIdComment = selfIdComment;
    }
    if ($('#comment_use_paging').val()) {
        params.bUsePaging = 1;
    }

    ls.ajax(this.options.type[typeTarget].url_response, params, function (result) {
        objImg.removeClass('fa-pulse');

        if (!result) {
            ls.msg.error('Error', 'Please try again later');
        }
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            var aCmt = result.aComments;
            if (aCmt.length > 0 && result.iMaxIdComment) {
                $("#comment_last_id").val(result.iMaxIdComment);
                $('#count-comments').text(parseInt($('#count-comments').text()) + aCmt.length);
                if (ls.blocks) {
                    var curItemBlock = ls.blocks.getCurrentItem('stream');
                    if (curItemBlock.data('type') == 'comment') {
                        ls.blocks.load(curItemBlock, 'stream');
                    }
                }
            }
            var iCountOld = 0;
            if (bNotFlushNew) {
                iCountOld = this.aCommentNew.length;
            } else {
                this.aCommentNew = [];
            }
            if (selfIdComment) {
                this.toggleCommentForm(this.iCurrentShowFormComment, true);
                this.setCountNewComment(aCmt.length - 1 + iCountOld);
            } else {
                this.setCountNewComment(aCmt.length + iCountOld);
            }

            $.each(aCmt, function (index, item) {
                if (!document.getElementById('comment_id_' + item.id)) {
                    if (!(selfIdComment && selfIdComment == item.id)) {
                        this.aCommentNew.push(item.id);
                    }
                    this.inject(item.idParent, item.id, item.html);
                }
            }.bind(this));

            if (selfIdComment && $('#comment_id_' + selfIdComment).length) {
                this.scrollToComment(selfIdComment);
            }
            this.checkFolding();
            this.aCommentNew = [];
            this.calcNewComments();
            ls.hook.run('ls_comments_load_after', [idTarget, typeTarget, selfIdComment, bNotFlushNew, result]);

            try {
                var new_messages = document.getElementById("new_messages");
                var pm_title = new_messages.dataset.title;
                if (result.iUserCurrentCountTalkNew > 0) {
                    new_messages.classList.add("new-messages");
                    pm_title += " (" + result.iUserCurrentCountTalkNew.toString() + ")";
                } else {
                    new_messages.classList.remove("new-messages");
                }
                new_messages.childNodes[0].textContent = pm_title;
                new_messages.parentNode.title = pm_title;
            } catch (err) {
                throw err;
            }
        }
    }.bind(this));
}

export function turnBack() {
    this.aCommentNew += this.aCommentOld;
    this.aCommentNew.forEach(function (item, i) {
        item.addClass(this.options.comment_new)
    })
    this.setCountNewComment(this.aCommentNew.length);

}


// Вставка комментария
export function inject(idCommentParent, idComment, sHtml) {
    var newComment = $('<div>', {'class': 'comment-wrapper', id: 'comment_wrapper_id_' + idComment}).html(sHtml);
    if (idCommentParent) {
        // Уровень вложенности родителя
        var iCurrentTree = $('#comment_wrapper_id_' + idCommentParent).parentsUntil('#comments').length;
        if (iCurrentTree == ls.registry.get('comment_max_tree')) {
            // Определяем id предыдушего родителя
            var prevCommentParent = $('#comment_wrapper_id_' + idCommentParent).parent();
            idCommentParent = parseInt(prevCommentParent.attr('id').replace('comment_wrapper_id_', ''));
        }
        $('#comment_wrapper_id_' + idCommentParent).append(newComment);
    } else {
        $('#comments').append(newComment);
    }
    ls.hook.run('ls_comment_inject_after', arguments, newComment);
}


// Удалить/восстановить комментарий
export function toggle(obj, commentId) {
    var url = aRouter['ajax'] + 'comment/delete/';
    var params = {idComment: commentId};

    ls.hook.marker('toggleBefore');
    ls.ajax(url, params, function (result) {
        if (!result) {
            ls.msg.error('Error', 'Please try again later');
        }
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            ls.msg.notice(null, result.sMsg);

            $('#comment_id_' + commentId).removeClass(this.options.classes.comment_self + ' ' + this.options.classes.comment_new + ' ' + this.options.classes.comment_deleted + ' ' + this.options.classes.comment_current);
            if (result.bState) {
                $('#comment_id_' + commentId).addClass(this.options.classes.comment_deleted);
            }
            $(obj).text(result.sTextToggle);
            ls.hook.run('ls_comments_toggle_after', [obj, commentId, result]);
        }
    }.bind(this));
}
;


// Предпросмотр комментария
export function preview(divPreview) {
    if (this.options.wysiwyg) {
        $("#form_comment_text").val(tinyMCE.activeEditor.getContent());
    }
    if ($("#form_comment_text").val() == '') return;
    $("#comment_preview_" + this.iCurrentShowFormComment).remove();
    $('#reply').before('<div id="comment_preview_' + this.iCurrentShowFormComment + '" class="comment-preview text"></div>');
    ls.tools.textPreview('form_comment_text', false, 'comment_preview_' + this.iCurrentShowFormComment);
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
    if (!this.options.pageTitle) this.options.pageTitle = document.title;
    if (count > 0) {
        $('#new_comments_counter').show().text(count);
        if (document.getElementById('autoload').checked) {
            document.title = '(' + count + ') ' + this.options.pageTitle;
        }
    } else {
        $('#new_comments_counter').text(0).hide();
        document.title = this.options.pageTitle;
    }
}



// Вычисляет кол-во новых комментариев
export function calcNewComments() {
    var aCommentsNew = $('.' + this.options.classes.comment + '.' + this.options.classes.comment_new);
    $.each(aCommentsNew, function (k, v) {
        console.log(this.isCollapsed(v));
    }.bind(this));

    let count = aCommentsNew.length;
    $.each(aCommentsNew, function (k, v) {
        if (!this.isCollapsed(v)) {
            this.aCommentNew.push(parseInt($(v).attr('id').replace('comment_id_', '')));
        } else {
            count--;
        }
    }.bind(this));
    this.setCountNewComment(count);
}



// Переход к следующему комментарию
export function goToNextComment() {
    if (this.aCommentNew[0]) {
        if ($('#comment_id_' + this.aCommentNew[0]).length) {
            this.scrollToComment(this.aCommentNew[0]);
            $('#comment_id_' + this.aCommentNew[0]).removeClass(this.options.classes.comment_new);
        }
        this.aCommentNew.shift();
    }
    this.setCountNewComment(this.aCommentNew.length);
}



// Прокрутка к комментарию
export function scrollToComment(idComment) {
    $.scrollTo('#comment_id_' + idComment, 350, {offset: -250});

    if (this.iCurrentViewComment) {
        $('#comment_id_' + this.iCurrentViewComment).removeClass(this.options.classes.comment_current);
        $('#comment_id_' + this.iCurrentViewComment).removeClass(this.options.classes.comment_new);
    }
    $('#comment_id_' + idComment).addClass(this.options.classes.comment_current);
    this.iCurrentViewComment = idComment;
}



// Прокрутка к родительскому комментарию
export function goToParentComment(id, pid) {
    thisObj = this;
    $('.' + this.options.classes.comment_goto_child).hide().find('a').unbind();

    $("#comment_id_" + pid).find('.' + this.options.classes.comment_goto_child).show().find("a").bind("click", function () {
        $(this).parent('.' + thisObj.options.classes.comment_goto_child).hide();
        thisObj.scrollToComment(id);
        return false;
    });
    this.scrollToComment(pid);
    return false;
}



// Сворачивание комментариев
export function checkFolding() {
    //if(!this.options.folding){
    //	return false;
    //}
    $(".folding").each(function (index, element) {
        if ($(element).parent(".comment").next(".comment-wrapper").length == 0) {
            $(element).hide();
        } else {
            $(element).show();
        }
    }).off("click").click(function (x) {
        if (x.target.className == "folding fa fa-minus-square") {
            ls.comments.collapseComment(x.target)
        } else {
            ls.comments.expandComment(x.target)
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
    $.each($(".folding"), function (k, v) {
        this.expandComment(v);
    }.bind(this));
}


export function collapseCommentAll() {
    $.each($(".folding"), function (k, v) {
        this.collapseComment(v);
    }.bind(this));
}



export function init() {
    this.initEvent();
    this.calcNewComments();
    this.checkFolding();
    this.toggleCommentForm(this.iCurrentShowFormComment);

    if (typeof(this.options.wysiwyg) != 'number') {
        this.options.wysiwyg = Boolean(BLOG_USE_TINYMCE && tinyMCE);
    }
    //ls.hook.run('ls_comments_init_after',[],this);
}


export function initEvent() {
    $('#form_comment_text').bind('keyup', function (e) {
        key = e.keyCode || e.which;
        if (e.ctrlKey && (key == 13)) {
            $('#comment-button-submit').click();
            return false;
        }
    });

    if (this.options.folding) {
        $(".folding").click(function (e) {
            if ($(e.target).hasClass("folded")) {
                this.expandComment(e.target);
            } else {
                this.collapseComment(e.target);
            }
        }.bind(this));
    }
}

