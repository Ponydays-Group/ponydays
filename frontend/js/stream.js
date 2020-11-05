import * as Lang from "./lang"
import * as Msg from "./msg"
import Emitter from "./emitter"
import $ from "jquery"
import * as Ajax from "./ajax"

export let isBusy = false;
export let dateLast = null;

export function subscribe(iTargetUserId) {
    const url = "/stream/subscribe/";
    const params = {"id": iTargetUserId};

    Emitter.emit("stream_subscribe_before");
    Ajax.ajax(url, params, function(data) {
        if(data.bStateError) {
            Msg.error(data.sMsgTitle, data.sMsg);
        } else {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("stream_subscribe_after", [params, data]);
        }
    });
}

export function unsubscribe(iId) {
    const url = "/stream/unsubscribe/";
    const params = {"id": iId};

    Emitter.emit("stream_unsubscribe_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("stream_unsubscribe_after", [params, data]);
        }
    });
}

export function switchEventType(iType) {
    const url = "/stream/switchEventType/";
    const params = {"type": iType};

    Emitter.emit("stream_switcheventtype_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            Msg.notice(data.sMsgTitle, data.sMsg);
            Emitter.emit("stream_switcheventtype_after", [params, data]);
        }
    });
}

export function appendUser() {
    const sLogin = $("#stream_users_complete").val();
    if(!sLogin) return;

    const url = "/stream/subscribeByLogin/";
    const params = {"login": sLogin};

    Emitter.emit("stream_appenduser_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError) {
            $("#stream_no_subscribed_users").remove();
            const checkbox = $("#strm_u_" + data.uid);
            if(checkbox.length) {
                if(checkbox.attr("checked")) {
                    Msg.error(Lang.get("error"), Lang.get("stream_subscribes_already_subscribed"));
                } else {
                    checkbox.attr("checked", "on");
                    Msg.notice(data.sMsgTitle, data.sMsg);
                }
            } else {
                const liElement = $("<li><input type=\"checkbox\" class=\"streamUserCheckbox input-checkbox\" id=\"strm_u_" + data.uid + "\" checked=\"checked\" onClick=\"if ($(this).get('checked')) {ls.streamsubscribe(" + data.uid + ")} else {ls.streamunsubscribe(" + data.uid + ")}\" /> <a href=\"" + data.user_web_path + "\">" + data.user_login + "</a></li>");
                $("#stream_block_users_list").append(liElement);
                Msg.notice(data.sMsgTitle, data.sMsg);
            }
            Emitter.emit("stream_appenduser_after", [checkbox.length, data]);
        } else {
            Msg.error(data.sMsgTitle, data.sMsg);
        }
    });
}

export function getMore() {
    if(isBusy) {
        return;
    }
    const lastId = $("#stream_last_id").val();
    if(!lastId) return;
    $("#stream_get_more").addClass("stream_loading");
    isBusy = true;

    const url = "/stream/get_more/";
    const params = {"last_id": lastId, "date_last": this.dateLast};

    Emitter.emit("stream_getmore_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError && data.events_count) {
            $("#stream-list").append(data.result);
            $("#stream_last_id").attr("value", data.iStreamLastId);
        }
        if(!data.events_count) {
            $("#stream_get_more").hide();
        }
        $("#stream_get_more").removeClass("stream_loading");
        Emitter.emit("stream_getmore_after", [lastId, data]);
        this.isBusy = false;
    }.bind(this));
}

export function getMoreAll() {
    if(isBusy) {
        return;
    }
    const lastId = $("#stream_last_id").val();
    if(!lastId) return;
    $("#stream_get_more").addClass("stream_loading");
    isBusy = true;

    const url = "/stream/get_more_all/";
    const params = {"last_id": lastId, "date_last": this.dateLast};

    Emitter.emit("stream_getmoreall_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError && data.events_count) {
            $("#stream-list").append(data.result);
            $("#stream_last_id").attr("value", data.iStreamLastId);
        }
        if(!data.events_count) {
            $("#stream_get_more").hide();
        }
        $("#stream_get_more").removeClass("stream_loading");
        Emitter.emit("stream_getmoreall_after", [lastId, data]);
        isBusy = false;
    }.bind(this));
}

export function getMoreByUser(iUserId) {
    if(isBusy) {
        return;
    }
    const lastId = $("#stream_last_id").val();
    if(!lastId) return;
    $("#stream_get_more").addClass("stream_loading");
    isBusy = true;

    const url = "/stream/get_more_user/";
    const params = {"last_id": lastId, user_id: iUserId, "date_last": this.dateLast};

    Emitter.emit("stream_getmorebyuser_before");
    Ajax.ajax(url, params, function(data) {
        if(!data.bStateError && data.events_count) {
            $("#stream-list").append(data.result);
            $("#stream_last_id").attr("value", data.iStreamLastId);
        }
        if(!data.events_count) {
            $("#stream_get_more").hide();
        }
        $("#stream_get_more").removeClass("stream_loading");
        Emitter.emit("stream_getmorebyuser_after", [lastId, iUserId, data]);
        isBusy = false;
    }.bind(this));
}
