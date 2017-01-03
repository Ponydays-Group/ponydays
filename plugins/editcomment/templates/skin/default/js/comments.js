var ls = ls ||
{};

ls.comments = ls.comments ||
{};

/**
 * Обработка комментариев
 */
ls.comments = (function ($) {
    var that = ls.comments;

    this.superior = function (name) {
        var that = this;
        method = that[name];
        return function () {
            return method.apply(that, arguments);
        }
    }

    this.mytoggleCommentForm = function (idComment, bNoFocus) {
        if (typeof (this.sBStyle) != 'undefined')
            $('#comment-button-submit').css('display', this.sBStyle);
        if (typeof (this.cbsclick) != 'undefined') {
            $('#comment-button-submit').unbind('click');
            $('#comment-button-submit').attr('onclick', this.cbsclick);
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

        this.super_toggleCommentForm(idComment, bNoFocus);
    }

    this.cancelEditComment = function (idComment) {
        var reply = $('#reply');
        if (!reply.length) {
            return;
        }

        reply.hide();
        this.setFormText('');
    }

    this.editComment = function (idComment) {
        var reply = $('#reply');
        if (!reply.length) {
            return;
        }

        if (!(this.iCurrentShowFormComment == idComment && reply.is(':visible'))) {
            var thisObj = this;
            $('#comment_content_id_' + idComment).addClass(thisObj.options.classes.form_loader);
            ls.ajax(aRouter.ajax + 'editcomment-getsource/',
                {
                    'idComment':idComment
                }, function (result) {
                    $('#comment_content_id_' + idComment).removeClass(thisObj.options.classes.form_loader);
                    if (!result) {
                        ls.msg.error('Error', 'Please try again later');
                        return;
                    }
                    if (result.bStateError) {
                        ls.msg.error(null, result.sMsg);
                    }
                    else {
                        thisObj.toggleCommentForm(idComment);
                        thisObj.sBStyle = $('#comment-button-submit').css('display');
                        var cbs = $('#comment-button-submit');
                        cbs.css('display', 'none');
                        thisObj.cbsclick = $('#comment-button-submit').attr('onclick');

                        $('#comment-button-submit').attr('onclick', "");
                        $('#comment-button-submit').bind('click', function () {
                            $('#comment-button-submit-edit').click();
                            return false;
                        });
                        if (result.bHasHistory)
                            cbs.after($(thisObj.options.history_button_code));

                        cbs.after($(thisObj.options.cancel_button_code));

                        cbs.after($(thisObj.options.edit_button_code));
                        ls.comments.setFormText(result.sCommentSource);

                        thisObj.enableFormComment();
                    }
                });
        }
        else {
            reply.hide();
            return;
        }
    }

    this.setFormText=function (sText)
    {
        if (this.options.wysiwyg) {
            tinyMCE.execCommand('mceRemoveControl', false, 'form_comment_text');
            $('#form_comment_text').val(sText);
            tinyMCE.execCommand('mceAddControl', true, 'form_comment_text');
        }
        else if (typeof($('#form_comment_text').getObject) == 'function') {
            $('#form_comment_text').destroyEditor();
            $('#form_comment_text').val(sText);
            $('#form_comment_text').redactor();
        }
        else
            $('#form_comment_text').val(sText);
    }

    this.edit = function (formObj, targetId, targetType) {
        if (this.options.wysiwyg) {
            $('#' + formObj + ' textarea').val(tinyMCE.activeEditor.getContent());
        }
        else
            if (typeof($('#form_comment_text').getObject) == 'function')
            {
                $('#' + formObj + ' textarea').val($('#form_comment_text').getCode());
            }
        formObj = $('#' + formObj);

        $('#form_comment_text').addClass(this.options.classes.form_loader).attr('readonly', true);
        $('#comment-button-submit').attr('disabled', 'disabled');

        var lData = formObj.serializeJSON();
        var idComment = lData.reply;

        ls.ajax(aRouter.ajax + 'editcomment-edit/', lData, function (result) {
            $('#comment-button-submit').removeAttr('disabled');
            if (!result) {
                this.enableFormComment();
                ls.msg.error('Error', 'Please try again later');
                return;
            }
            if (result.bStateError) {
                this.enableFormComment();
                ls.msg.error(null, result.sMsg);
            }
            else {
                if (result.sMsg)
                    ls.msg.notice(null, result.sMsg);

                ls.comments.enableFormComment();
                ls.comments.setFormText('');

                // Load new comments
                if (result.bEdited) {
                    $('#comment_content_id_' + idComment).html(result.sCommentText);
                }
                if (!result.bCanEditMore)
                    $('#comment_id_' + idComment).find('.editcomment_editlink').remove();
                this.load(targetId, targetType, idComment, true);
                if (ls.blocks) {
                    var curItemBlock = ls.blocks.getCurrentItem('stream');
                    if (curItemBlock.data('type') == 'comment') {
                        ls.blocks.load(curItemBlock, 'stream');
                    }
                }

                ls.hook.run('ls_comments_edit_after', [ formObj, targetId, targetType, result ]);
            }
        }.bind(this));
    }

    this.showHistory = function () {
        formObj = $('#form_comment');

        $('#form_comment_text').addClass(this.options.classes.form_loader).attr('readonly', true);
        $('#comment-button-submit-edit').attr('disabled', 'disabled');

        var lData = formObj.serializeJSON();
        lData.form_comment_text = '';
        var idComment = lData.reply;

        ls.ajax(aRouter.ajax + 'editcomment-gethistory/', lData, function (result) {
            $('#comment-button-submit-edit').removeAttr('disabled');
            if (!result) {
                this.enableFormComment();
                ls.msg.error('Error', 'Please try again later');
                return;
            }
            if (result.bStateError) {
                this.enableFormComment();
                ls.msg.error(null, result.sMsg);
            }
            else {
                if (result.sMsg)
                    ls.msg.notice(null, result.sMsg);

                this.enableFormComment();
                $('#editcomment-history-content').html(result.sContent);
                $('#modal-editcomment-history').jqmShow();
            }
        }.bind(this));
    }

    this.init_editcomment = function () {
        this.super_toggleCommentForm = that.superior("toggleCommentForm");
        ls.comments.toggleCommentForm = this.mytoggleCommentForm;
    }

    return this;
}).call(ls.comments ||
{}, jQuery);

jQuery(document).ready(function () {
    ls.comments.init_editcomment();
});
