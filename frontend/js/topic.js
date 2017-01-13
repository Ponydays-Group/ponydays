import $ from 'jquery'

/**
 * Опросы
 */

export function preview(form, preview) {
    form = $('#' + form);
    preview = $('#' + preview);
    var url = aRouter['ajax'] + 'preview/topic/';
    ls.hook.marker('previewBefore');
    ls.ajaxSubmit(url, form, function (result) {
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            preview.show().html(result.sText);
            ls.hook.run('ls_topic_preview_after', [form, preview, result]);
        }
    });
};

export function insertImageToEditor(sUrl, sAlign, sTitle) {
    sAlign = sAlign == 'center' ? 'class="image-center"' : 'align="' + sAlign + '"';
    $.markItUp({replaceWith: '<img src="' + sUrl + '" title="' + sTitle + '" ' + sAlign + ' />'});
    $('#window_upload_img').find('input[type="text"]').val('');
    $('#window_upload_img').jqmHide();
    return false;
};

export function onControlLocked(result) {
    if (result.bStateError) {
        this.checked = this.dataset.checkedOld == "1";
        ls.msg.error(null, result.sMsg);
    } else {
        this.checked = result.bState;
        ls.msg.notice(null, result.sMsg);
    }
    delete this.dataset.checkedOld;
};
export function lockControl(idTopic, obj) {
    var state = obj.checked;
    obj.dataset.checkedOld = state ? "0" : "1";
    var params = {};
    params['idTopic'] = idTopic;
    params['bState'] = state ? "1" : "0";

    var url = aRouter['ajax'] + 'topic-lock-control';
    ls.hook.marker('topicLockControlBefore');
    ls.ajax(url, params, this.onControlLocked.bind(obj));
    return true;
};