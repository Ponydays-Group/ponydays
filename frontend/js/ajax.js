import * as Msg from "./msg"
import $ from "jquery"
import "jquery-form/jquery.form"
import * as Tools from "./tools"
import Emitter from "./emitter"

export function ajax(url, params, callback, more) {
    more = more || {};
    params = params || {};
    params.security_ls_key = LIVESTREET_SECURITY_KEY;

    $.each(params, function(k, v) {
        if(typeof (v) == "boolean") {
            params[k] = v ? 1 : 0;
        }
    });

    if(url.indexOf("http://") != 0 && url.indexOf("https://") != 0 && url.indexOf("/") != 0) {
        url = aRouter["ajax"] + url + "/";
    }

    let ajaxOptions = {
        type: more.type || "POST",
        url: url,
        data: params,
        dataType: more.dataType || "json",
        success: callback ? function() {
            callback.apply(this, arguments)
        } : function() {
            Tools.debug("ajax success: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
        error: more.error || function(msg) {
            Tools.debug("ajax error: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
        complete: more.complete || function(msg) {
            Tools.debug("ajax complete: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
    };

    Emitter.emit("ajax_before", [ajaxOptions], this);

    return $.ajax(ajaxOptions);
}

export async function asyncAjax(url, params, callback, more) {
    more = more || {};
    params = params || {};
    params.security_ls_key = LIVESTREET_SECURITY_KEY;

    $.each(params, function(k, v) {
        if(typeof (v) == "boolean") {
            params[k] = v ? 1 : 0;
        }
    });

    if(url.indexOf("http://") != 0 && url.indexOf("https://") != 0 && url.indexOf("/") != 0) {
        url = aRouter["ajax"] + url + "/";
    }

    let ajaxOptions = {
        type: more.type || "POST",
        url: url,
        data: params,
        dataType: more.dataType || "json",
        success: callback || function() {
            Tools.debug("ajax success: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
        error: more.error || function(msg) {
            Tools.debug("ajax error: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
        complete: more.complete || function(msg) {
            Tools.debug("ajax complete: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
    };

    Emitter.emit("ajax_before", [ajaxOptions], this);

    return $.ajax(ajaxOptions);
}

export function ajaxSubmit(url, form, callback, more) {
    more = more || {};
    if(typeof (form) == "string") {
        form = $("#" + form);
    }
    if(url.indexOf("http://") != 0 && url.indexOf("https://") != 0 && url.indexOf("/") != 0) {
        url = aRouter["ajax"] + url + "/";
    }

    let options = {
        type: "POST",
        url: url,
        data: {security_ls_key: LIVESTREET_SECURITY_KEY},
        success: function(data) {
            if(typeof (data) === "string") {
                data = JSON.parse(data);
            }
            callback(data);
        } || function() {
            Tools.debug("ajax success: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
        error: more.error || function() {
            Tools.debug("ajax error: ");
            Tools.debug.apply(this, arguments);
        }.bind(this),
    };

    Emitter.emit("ajax_submit_before", [options], this);

    form.ajaxSubmit(options);
}

/**
 * Загрузка изображения
 */
export async function ajaxUploadImg(form, sToLoad, spoilSelector) {
    const submit_sel = $("#" + form + "_submit");
    submit_sel.attr("disabled", true);
    submit_sel.toggleClass("hovered");
    let result = await ajaxSubmit("upload/image/", form, function(data) {
        if(data.bStateError) {
            Msg.error(data.sMsgTitle, data.sMsg);
            submit_sel.toggleClass("hovered");
            submit_sel.attr("disabled", false);
        } else {
            if(!data.sText.length) {
                Msg.error("Ошибка", "Сервер не отдал картинку");
            }
            if($(spoilSelector).prop("checked")) {
                let s = prompt("Спойлер", "Спойлер");
                if(s)
                    $.markItUp({replaceWith: "<span class=\"spoiler\"><span class=\"spoiler-title spoiler-close\">" + s + "</span><span class=\"spoiler-body\">" + data.sText + "</span></span>"});
                else
                    $.markItUp({replaceWith: data.sText});
            } else {
                $.markItUp({replaceWith: data.sText});
            }
            const upload_img_sel = $("#window_upload_img");
            upload_img_sel.find("input[type=\"text\"], input[type=\"file\"]").val("");
            upload_img_sel.jqmHide();
            Emitter.emit("ajax_uploadimg_after");
            submit_sel.toggleClass("hovered");
            submit_sel.attr("disabled", false);
        }
    }, {
        error: function() {
            Msg.error("Ошибка", "Изображение слишком тяжелое");
            submit_sel.toggleClass("hovered");
            submit_sel.attr("disabled", false);
        },
    });
}

export function LoadMoreActions(LastActionId) {
    const params = {};
    params["LastActionId"] = LastActionId;

    $("#LoadMoreButton").toggleClass("loading");

    return this.ajax(aRouter["feedbacks"] + "LoadMoreActions", params, function(data) {
        if(data.aResult.Errors.length > 0) {
            const $aErrors = data.aResult.Errors;
            for(let i = 0; i < $aErrors.length; i++) {
                const $sError = $aErrors[i];
                Msg.error("", $sError);
            }
        } else {
            $("#stream-list").append(data.aResult.Text);
            $("#LoadMoreButton").remove();
        }
    });
}

export function LoadMoreNotifications(Page) {
    const params = {};
    params["Page"] = Page;

    $("#LoadMoreButton").toggleClass("loading");

    return this.ajax(aRouter["notifications"] + "LoadMoreActions", params, function(data) {
        if(data.aResult.Errors.length > 0) {
            const $aErrors = data.aResult.Errors;
            for(let i = 0; i < $aErrors.length; i++) {
                const $sError = $aErrors[i];
                Msg.error("", $sError);
            }
        } else {
            $("#stream-list").append(data.aResult.Text);
            $("#LoadMoreButton").remove();
        }
    });
}

export function saveConfig() {
    const changed = {};

    $(".config-param").each(
        function(k, v) {
            const input = $(v).find("input")[0];
            if(v.dataset.val != (input.type == "checkbox" ? input.checked : input.value)) {
                changed[v.dataset.name] = (input.type == "checkbox" ? (input.checked ? 1 : 0) : input.value);
                if(v.dataset.separator) changed[v.dataset.name] = input.value.split(", ");
            }
        },
    );
    asyncAjax("/admin/save", {values: changed});
}

export function saveUserAdmin() {
    ajaxSubmit("/admin/user", "user_admin");
}
