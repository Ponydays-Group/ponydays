import * as Msg from "./msg"
import Emitter from "./emitter"
import $ from "jquery"
import * as Ajax from "./ajax"

export let sText = "";

export function showForm(sText) {
    $("#usernote-button-add").hide();
    $("#usernote-note").hide();
    $("#usernote-form").show();
    if(this.sText) {
        $("#usernote-form-text").html(this.sText);
    } else {
        $("#usernote-form-text").val("");
    }
    $("#usernote-form-text").focus();
    return false;
}

export function hideForm() {
    $("#usernote-form").hide();
    if(this.sText) {
        this.showNote();
    } else {
        $("#usernote-button-add").show();
    }
    return false;
}

export function save(iUserId) {
    const url = aRouter["profile"] + "ajax-note-save/";
    const params = {iUserId: iUserId, text: $("#usernote-form-text").val()};
    Emitter.emit("saveBefore");
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            this.sText = result.sText;
            this.showNote();
            Emitter.emit("ls_usernote_save_after", [params, result]);
        }
    }.bind(this));
    return false;
}

export function showNote() {
    $("#usernote-form").hide();
    $("#usernote-note").show();
    $("#usernote-note-text").html(this.sText);
}

export function remove(iUserId) {
    const url = aRouter["profile"] + "ajax-note-remove/";
    const params = {iUserId: iUserId};
    Emitter.emit("removeBefore");
    Ajax.ajax(url, params, function(result) {
        if(result.bStateError) {
            Msg.error(null, result.sMsg);
        } else {
            $("#usernote-note").hide();
            $("#usernote-button-add").show();
            this.sText = "";
            Emitter.emit("ls_usernote_remove_after", [params, result]);
        }
    }.bind(this));
    return false;
}
