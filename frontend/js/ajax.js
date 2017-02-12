import * as Msg from './msg'
import $ from 'jquery'
import * as Tools from './tools'
import * as Hook from './hook'

export function ajax(url, params, callback, more) {
    more = more || {};
    params = params || {};
    params.security_ls_key = LIVESTREET_SECURITY_KEY;

    $.each(params, function (k, v) {
        if (typeof(v) == "boolean") {
            params[k] = v ? 1 : 0;
        }
    });

    if (url.indexOf('http://') != 0 && url.indexOf('https://') != 0 && url.indexOf('/') != 0) {
        url = aRouter['ajax'] + url + '/';
    }

    let ajaxOptions = {
        type: more.type || "POST",
        url: url,
        data: params,
        dataType: more.dataType || 'json',
        success: callback || function () {
            Tools.debug("ajax success: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
        error: more.error || function (msg) {
            Tools.debug("ajax error: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
        complete: more.complete || function (msg) {
            Tools.debug("ajax complete: ");
            Tools.debug.apply(this, arguments);
        }.bind(this)
    };

    Hook.run('ls_ajax_before', [ajaxOptions], this);

    return $.ajax(ajaxOptions);
}

export function ajaxSubmit(url, form, callback, more) {
    more = more || {};
    if (typeof(form) == 'string') {
        form = $('#' + form);
    }
    if (url.indexOf('http://') != 0 && url.indexOf('https://') != 0 && url.indexOf('/') != 0) {
        url = aRouter['ajax'] + url + '/';
    }

    let options = {
        type: 'POST',
        url: url,
        dataType: more.dataType || 'json',
        data: {security_ls_key: LIVESTREET_SECURITY_KEY},
        success: callback || function () {
            Tools.debug("ajax success: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
        error: more.error || function () {
            Tools.debug("ajax error: ");
            Tools.debug.apply(this, arguments);
        }.bind(this)

    };

    Hook.run('ls_ajaxsubmit_before', [options], this);

    form.ajaxSubmit(options);
}

/**
 * Загрузка изображения
 */
export function ajaxUploadImg(form, sToLoad) {
    Hook.marker('ajaxUploadImgBefore');
    debugSubmit('upload/image/', form, function (data) {
        if (data.bStateError) {
            Msg.error(data.sMsgTitle, data.sMsg);
        } else {
            $.markItUp({replaceWith: data.sText});
            $('#window_upload_img').find('input[type="text"], input[type="file"]').val('');
            $('#window_upload_img').jqmHide();
            Hook.marker('ajaxUploadImgAfter');
        }
    });
}
