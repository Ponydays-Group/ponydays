import * as Msg from "./msg"
import Emitter from "./emitter"
import $ from "jquery"
import * as Ajax from "./ajax"

export let isBusy = false;

export function subscribe(sType, iId) {
    const url = "/feed/subscribe/";
    const params = {"type": sType, "id": iId};

    Emitter.emit("userfeed_subscribe_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("userfeed_subscribe_after", [sType, iId, data]);
        }
    });
}

export function subscribeAll() {
    const url = "/feed/subscribe_all/";
    const params = {};

    Emitter.emit("userfeed_subscribeall_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("userfeed_subscribeall_after", [data]);
            location.reload();
        }
    });
}

export function unsubscribe(sType, iId) {
    const url = "/feed/unsubscribe/";
    const params = {"type": sType, "id": iId};

    Emitter.emit("userfeed_unsubscribe_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("userfeed_unsubscribe_after", [sType, iId, data]);
        }
    });
}

export function unsubscribeAll() {
    const url = "/feed/unsubscribe_all/";
    const params = {};

    Emitter.emit("userfeed_unsubscribeall_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("userfeed_unsubscribeall_after", [data]);
            location.reload();
        }
    });
}

export function appendUser() {
    const sLogin = $("#userfeed_users_complete").val();
    if(!sLogin) return;

    const url = "/feed/subscribeByLogin/";
    const params = {"login": sLogin};

    Emitter.emit("userfeed_appenduser_before");
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
    if(isBusy) {
        return;
    }
    const lastId = $("#userfeed_last_id").val();
    if(!lastId) return;
    $("#userfeed_get_more").addClass("userfeed_loading");
    isBusy = true;

    const url = "/feed/get_more/";
    const params = {"last_id": lastId};

    Emitter.emit("userfeed_getmore_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError && data.topics_count) {
            $("#userfeed_loaded_topics").append(data.result);
            $("#userfeed_last_id").attr("value", data.iUserfeedLastId);
        }
        if(!data.topics_count) {
            $("#userfeed_get_more").hide();
        }
        $("#userfeed_get_more").removeClass("userfeed_loading");
        Emitter.emit("userfeed_getmore_after", [lastId, data]);

        isBusy = false;
    }.bind(this));
}
