import * as Msg from "./msg"
import Emitter from "./emitter"
import $ from "jquery"
import * as Ajax from "./ajax"

export let isBusy = false;

export function subscribe(sType, iId) {
    const url = aRouter["feed"] + "subscribe/";
    const params = {"type": sType, "id": iId};

    Emitter.emit("subscribeBefore");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("ls_userfeed_subscribe_after", [sType, iId, data]);
        }
    });
}

export function subscribeAll() {
    const url = aRouter["feed"] + "subscribe_all/";
    const params = {};

    Emitter.emit("subscribeBefore");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("ls_userfeed_subscribe_all_after", [data]);
            location.reload();
        }
    });
}

export function unsubscribe(sType, iId) {
    const url = aRouter["feed"] + "unsubscribe/";
    const params = {"type": sType, "id": iId};

    Emitter.emit("unsubscribeAllBefore");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("ls_userfeed_unsubscribe_after", [sType, iId, data]);
        }
    });
}

export function unsubscribeAll() {
    const url = aRouter["feed"] + "unsubscribe_all/";
    const params = {};

    Emitter.emit("unsubscribeAllBefore");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("ls_userfeed_unsubscribe_all_after", [data]);
            location.reload();
        }
    });
}

export function appendUser() {
    const sLogin = $("#userfeed_users_complete").val();
    if(!sLogin) return;

    const url = aRouter["feed"] + "subscribeByLogin/";
    const params = {"login": sLogin};

    Emitter.emit("appendUserBefore");
    Ajax.ajax(url, params, function(data) {
        if(data.bStateError) {
            Msg.error(data.sMsgTitle, data.sMsg);
        } else {
            $("#userfeed_no_subscribed_users").remove();
            var checkbox = $("#usf_u_" + data.uid);
            if(checkbox.length) {
                if(checkbox.attr("checked")) {
                    Msg.error(data.lang_error_title, data.lang_error_msg);
                } else {
                    checkbox.attr("checked", "on");
                    Msg.notice(data.sMsgTitle, data.sMsg);
                }
            } else {
                const liElement = $("<li><input type=\"checkbox\" class=\"userfeedUserCheckbox input-checkbox\" id=\"usf_u_" + data.uid + "\" checked=\"checked\" onClick=\"if ($(this).get('checked')) {ls.userfeedsubscribe('users'," + data.uid + ")} else {ls.userfeedunsubscribe('users'," + data.uid + ")}\" /><a href=\"" + data.user_web_path + "\">" + data.user_login + "</a></li>");
                $("#userfeed_block_users_list").append(liElement);
                Msg.notice(data.sMsgTitle, data.sMsg);
            }
        }
    });
}

export function getMore() {
    if(this.isBusy) {
        return;
    }
    const lastId = $("#userfeed_last_id").val();
    if(!lastId) return;
    $("#userfeed_get_more").addClass("userfeed_loading");
    this.isBusy = true;

    const url = aRouter["feed"] + "get_more/";
    const params = {"last_id": lastId};

    Emitter.emit("getMoreBefore");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError && data.topics_count) {
            $("#userfeed_loaded_topics").append(data.result);
            $("#userfeed_last_id").attr("value", data.iUserfeedLastId);
        }
        if(!data.topics_count) {
            $("#userfeed_get_more").hide();
        }
        $("#userfeed_get_more").removeClass("userfeed_loading");
        Emitter.emit("ls_userfeed_get_more_after", [lastId, data]);
        this.isBusy = false;
    }.bind(this));
}
