import * as Msg from './msg'
import Emitter from './emitter'
import $ from 'jquery'
import * as Ajax from './ajax'

/**
 * Вспомогательные функции
 */


/**
 * Переводит первый символ в верхний регистр
 */
export function ucfirst(str)
{
    let f = str.charAt(0).toUpperCase();
    return f + str.substr(1, str.length - 1);
}

/**
 * Выделяет все chekbox с определенным css классом
 */
export function checkAll(cssclass, checkbox, invert)
{
    $('.' + cssclass).each(function (index, item) {
        if (invert) {
            $(item).attr('checked', !$(item).attr("checked"));
        } else {
            $(item).attr('checked', $(checkbox).attr("checked"));
        }
    });
}


/**
 * Предпросмотр
 */
export function textPreview(textId, save, divPreview)
{
    let text = (BLOG_USE_TINYMCE) ? tinyMCE.activeEditor.getContent() : $('#' + textId).val();
    let ajaxUrl = aRouter['ajax'] + 'preview/text/';
    const form_comment_mark = $("#form_comment_mark")[0];
    let ajaxOptions = {text: text, save: save, form_comment_mark: form_comment_mark ? (form_comment_mark.checked? 'on' : 'off') : 'off'};
    Emitter.emit('textPreviewAjaxBefore');
    Ajax.ajax(ajaxUrl, ajaxOptions, function (result) {
        if (!result) {
            Msg.error('Error', 'Please try again later');
        }
        if (result.bStateError) {
            Msg.error(result.sMsgTitle || 'Error', result.sMsg || 'Please try again later');
        } else {
            if (!divPreview) {
                divPreview = 'text_preview';
            }
            let elementPreview = $('#' + divPreview);
            Emitter.emit('textPreviewDisplayBefore');
            if (elementPreview.length) {
                elementPreview.html(result.sText);
                Emitter.emit('textPreviewDisplayAfter');
            }
        }
    });
}


/**
 * Возвращает выделенный текст на странице
 */
export function getSelectedText() {
    let text = '';
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (window.document.selection) {
        let sel = window.document.selection.createRange();
        text = sel.text || sel;
        if (text.toString) {
            text = text.toString();
        } else {
            text = '';
        }
    }
    return text;
}


export let options = {
    debug: true,
}

/**
 * Дебаг сообщений
 */
export function debug()
{
    if (options.debug) {
        Log.apply(this, arguments);
    }
}


/**
 * Лог сообщений
 */
export function Log()
{
    if (window.console && window.console.log) {
        //Function.prototype.bind.call(console.log, console).apply(console, arguments);
    } else {
        //alert(msg);
    }
}
