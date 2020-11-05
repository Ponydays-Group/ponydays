import * as Msg from "./msg"
import Emitter from "./emitter"
import $ from "jquery"
import * as Ajax from "./ajax"

/**
 * Опросы
 */

export function preview(form, preview) {
    form = $("#" + form);
    preview = $("#" + preview);
    const url = "/ajax/preview/topic/";
    Emitter.emit("topic_preview_before");
    Ajax.ajaxSubmit(url, form, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            preview.show().html(result.sText);
            Emitter.emit("topic_preview_after", [form, preview, result]);
        }
    });
}

export function insertImageToEditor(sUrl, bSpoil, sAlign, sTitle) {
    sAlign = sAlign == "center" ? "class=\"image-center\"" : "align=\"" + sAlign + "\"";
    let s = false;
    if(bSpoil)
        s = prompt("Спойлер", "Спойлер");
    $.markItUp({
        replaceWith:
            (bSpoil && s ? "<span class=\"spoiler\"><span class=\"spoiler-title spoiler-close\">" + s + "</span><span class=\"spoiler-body\">" : "") +
            "<img src=\"" + sUrl + "\" title=\"" + sTitle + "\" " + sAlign + " />"
            + (bSpoil && s ? "</span></span>" : ""),
    });
    const upload_img_sel = $("#window_upload_img");
    upload_img_sel.find("input[type=\"text\"]").val("");
    upload_img_sel.jqmHide();
    return false;
}

export function onControlLocked(result) {
    if(result.bStateError) {
        this.checked = this.dataset.checkedOld == "1";
        Msg.error(null, result.sMsg);
    } else {
        this.checked = result.bState;
        Msg.notice(null, result.sMsg);
    }
    delete this.dataset.checkedOld;
}

export function lockControl(idTopic, obj) {
    const state = obj.checked;
    obj.dataset.checkedOld = state ? "0" : "1";
    const params = {};
    params["idTopic"] = idTopic;
    params["bState"] = state ? "1" : "0";

    const url = "/ajax/topic-lock-control";
    Emitter.emit("topic_lockcontrol_before");
    Ajax.ajax(url, params, this.onControlLocked.bind(obj));
    return true;
}
