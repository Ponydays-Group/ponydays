import "@babel/polyfill/noConflict";
import {options} from "./vote"

function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

if (!(getCookie("key") || getCookie("wskey")) && LOGGED_IN) {
    ls.msg.error("Необходимо войти в аккаунт еще раз.", "Были внесены изменения в механизм входа")
}

const io = require("socket.io-client");

window.sock = io(SOCKET_URL, {
    query: {
        token: getCookie("key")
    }
});

sock.on('reconnect_attempt', () => {
    sock.io.opts.query = {
        token: getCookie("key")||getCookie("wskey")
    }
});

window.nAudio = new Audio();
nAudio.src = localStorage.getItem("notice_sound_url") || "http://freesound.org/data/previews/245/245645_1038806-lq.mp3";

sock.on('notification_group', function (data) {
    console.log("DATA_group:", data);
    switch(data.notification_type * 1){
        case 1: //talk_new_topic
            break;
        case 2: //talk_new_comment
            break;
        case 3: //comment_response
            break;
        case 4: //comment_mention
            break;
        case 5: //topic_new_comment
            ls.emitter.emit("socket-new-comment", data);
            break;
        case 6: //comment_edit
            ls.emitter.emit("socket-edit-comment", data);
            break;
        case 7: //comment_delete
            ls.emitter.emit("socket-delete-comment", data);
            break;
        case 8: //comment_restore
            ls.emitter.emit("socket-restore-comment", data);
            break;
        case 9: //comment_restore_deleted_by_you
            break;
        case 10: //comment_rank
            onVote(data);
            break;
        case 11: //topic_rank
            onVote(data);
            break;
        case 12: //topic_invite_ask
            break;
        case 13: //topic_invite_offer
            break;
        case 14: //talk_invite_offer
            break;
        case 15: //ban_in_blog
            break;
        case 16: //ban_global
            break;
        case 17: //topic_mention
            break;
        default:
    }
});

function onVote(data) {
    let area = data.target_type == "comment" ? $("#vote_area_" + data.target_type + "_" + data.target_id) : $("#vote_total_" + data.target_type + "_" + data.target_id);
    let rating = data.comment_extra.rating * 1;
    if (rating == 0 && data.comment_extra.countVote > 0) {
        area.addClass("vote-count-mixed");
        area.removeClass("vote-count-positive");
        area.removeClass("vote-count-negative");
        area.removeClass("action-hidden");
        $("#vote_total_" + data.target_type + "_" + data.target_id).html("0")
    } else if (rating == 0) {
        area.addClass("action-hidden");
        area.removeClass("vote-count-positive");
        area.removeClass("vote-count-negative");
        area.removeClass("vote-count-mixed");
        $("#vote_total_" + data.target_type + "_" + data.target_id).html("0")
    } else if (rating > 0) {
        area.addClass("vote-count-positive");
        area.removeClass("vote-count-negative");
        area.removeClass("vote-count-mixed");
        area.removeClass("action-hidden");
        $("#vote_total_" + data.target_type + "_" + data.target_id).html("+" + rating)
    } else {
        area.addClass("vote-count-negative");
        area.removeClass("vote-count-positive");
        area.removeClass("vote-count-mixed");
        area.removeClass("action-hidden");
        $("#vote_total_" + data.target_type + "_" + data.target_id).html(rating)
    }
    if (isFinite(data.comment_extra.voteCount)) {
        area.data("count", data.comment_extra.voteCount);
    }
}

sock.on('notification', function (data) {
    console.log("DATA_user:", data);
    let isEnabled = false;

    if (data.user_id == data.sender_user_id) {
        return;
    }
    switch (data.notification_type * 1) {
        case 1: //talk_new_topic
            isEnabled = checkPerm("talk_new_topic");
            break;
        case 2: //talk_new_comment
            isEnabled = checkPerm("talk_new_comment");
            break;
        case 3: //comment_response
            isEnabled = checkPerm("comment_response");
            break;
        case 4: //comment_mention
            isEnabled = checkPerm("comment_mention");
            break;
        case 5: //topic_new_comment
            isEnabled = checkPerm("topic_new_comment");
            break;
        case 6: //comment_edit
            isEnabled = checkPerm("comment_edit");
            break;
        case 7: //comment_delete
            isEnabled = checkPerm("comment_delete_restore");
            break;
        case 8: //comment_restore
            isEnabled = checkPerm("comment_delete_restore");
            break;
        case 9: //comment_restore_deleted_by_you
            isEnabled = true;
            break;
        case 10: //comment_rank
            isEnabled = checkPerm("comment_rank");
            break;
        case 11: //topic_rank
            isEnabled = checkPerm("topic_rank");
            break;
        case 12: //topic_invite_ask
            isEnabled = checkPerm("topic_invite_ask");
            break;
        case 13: //topic_invite_offer
            isEnabled = checkPerm("topic_invite_offer");
            break;
        case 14: //talk_invite_offer
            isEnabled = checkPerm("talk_invite_offer");
            break;
        case 15: //ban_in_blog
            isEnabled = true;
            break;
        case 16: //ban_global
            isEnabled = true;
            break;
        case 17: //topic_mention
            isEnabled = checkPerm("topic_mention");
            break;
        default:
    }
    if (isEnabled) {
        let title = data.title;
        if (data.rating * 1 > 0) {
            title += "<span><span class=\"" + options.classes.vote + " " + options.classes.positive + "\"><span class=\"" + options.classes.vote_count + "\">+" + data.rating + "</span></span></span>";
        } else if (data.rating * 1 < 0) {
            title += "<span><span class=\"" + options.classes.vote + " " + options.classes.negative + "\"><span class=\"" + options.classes.vote_count + "\">" + data.rating + "</span></span></span>";
        }
        let text = "";
        if (data.comment_extra != null && data.group_target_type != 'talk') {
            text = data.comment_extra.text;
        }
        ls.msg.notice(title, text, data.link, false);

        if (checkPerm("sound_notice")) {
            nAudio.play();
        }
    }
});

sock.on("reply-info", function (data) {
    console.log("DATA:", data);
    if (checkPerm("notice_reply")) {
        let url = "/blog/undefined/" + data.targetId + "#comment" + data.commentData.id;
        let blank = true;
        if (location.pathname.startsWith("/blog/") && location.pathname.endsWith(data.targetId)) {
            url = "#comment" + data.commentData.id;
            blank = false
        }
        ls.msg.notice(data.senderLogin + " ответил вам в посте " + data.targetTitle, data.commentData.text, url, blank);
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
});

sock.on("edit-comment-info", function (data) {
    console.log("DATA:", data);
    if (checkPerm("notice_comment_delete")) {
        let url = "/blog/undefined/" + data.targetId + "#comment" + data.commentData.id;
        let blank = true;
        if (location.pathname.startsWith("/blog/") && location.pathname.endsWith(data.targetId)) {
            url = "#comment" + data.commentData.id;
            blank = false
        }
        ls.msg.notice(data.senderLogin + " отредактировал ваш комментарий", data.commentData.text, url, blank);
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
});

sock.on("delete-comment-info", function (data) {
    console.log("DATA:", data);
    data.delete = parseInt(data.delete);
    if (checkPerm("notice_comment_delete")) {
        let url = "/blog/undefined/" + data.targetId + "#comment" + data.commentId;
        let blank = true;
        if (location.pathname.startsWith("/blog/") && location.pathname.endsWith(data.targetId)) {
            url = "#comment" + data.commentId;
            blank = false
        }
        if (data.delete) {
            ls.msg.notice(data.senderLogin + " удалил ваш комментарий", data.deleteReason, url, blank)
        } else {
            ls.msg.notice(data.senderLogin + " восстановил ваш комментарий", data.commentText, url, blank)
        }
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
});

sock.on("talk-answer", function (data) {
    console.log("DATA:", data);
    if (checkPerm("notice_talk_reply")) {
        let url = "/talk/read/" + data.targetId + "#comment" + data.commentData.id;
        let blank = true;
        if (location.pathname.startsWith("/talk/") && (location.pathname.endsWith(data.targetId) || location.pathname.endsWith(data.targetId + "/"))) {
            url = "#comment" + data.commentData.id;
            blank = false
        }
        ls.msg.notice("Новый комментарий в " + data.targetTitle + ", от " + data.senderLogin, data.commentText, url, blank);
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
});

sock.on("vote-info", function (data) {
    console.log("DATA:", data);
    if (checkPerm("notice_vote")) {
        let url = "/blog/undefined/" + data.targetId;
        let blank = true;
        if (data.targetParentId) {
            url = "/blog/undefined/" + data.targetParentId + "#comment" + data.targetId
        }
        if (location.pathname.startsWith("/blog/") && (location.pathname.endsWith(data.targetParentId) || location.pathname.endsWith(data.targetParentId + "/"))) {
            url = "#comment" + data.targetId;
            blank = false
        }
        if (location.pathname.startsWith("/blog/") && (location.pathname.endsWith(data.targetId) || location.pathname.endsWith(data.targetId + "/")) && data.targetType == "topic") {
            url = "#";
            blank = false
        }
        switch (data.voteType*1) {
            case 0:
                ls.msg.notice("Пользователь " + data.senderName + " проголосовал за ваш " +
                    (data.targetType == "comment" ? "комментарий" : "пост") + ", поставив " +
                    (data.voteValue > 0 ?
                        "<span><span class=\""+ options.classes.vote + " " + options.classes.positive + "\"><span class=\"" + options.classes.vote_count +"\">+"+ data.voteValue +"</span></span></span>" :
                        "<span><span class=\""+ options.classes.vote + " " + options.classes.negative + "\"><span class=\"" + options.classes.vote_count +"\">"+ data.voteValue +"</span></span></span>"),
                    data.commentText || data.topicTitle,
                    url,
                    blank);
                break;
            case 1:
                ls.msg.notice("Пользователь " + data.senderName + " изменил голос за ваш " +
                    (data.targetType == "comment" ? "комментарий" : "пост") + " на " +
                    (data.voteValue > 0 ?
                        "<span><span class=\""+ options.classes.vote + " " + options.classes.positive + "\"><span class=\"" + options.classes.vote_count +"\">+"+ data.voteValue +"</span></span></span>" :
                        "<span><span class=\""+ options.classes.vote + " " + options.classes.negative + "\"><span class=\"" + options.classes.vote_count +"\">"+ data.voteValue +"</span></span></span>"),
                    data.commentText || data.topicTitle,
                    url,
                    blank);
                break;
            case 2:
                ls.msg.notice("Пользователь " + data.senderName + " отменил свой голос за ваш " +
                    (data.targetType == "comment" ? "комментарий" : "пост"),
                    data.commentText || data.topicTitle,
                    url,
                    blank);
                break;
        }
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
});

sock.on('edit-comment', (data) => ls.emitter.emit("socket-edit-comment", data));
sock.on('delete-comment', (data) => ls.emitter.emit("socket-delete-comment", data));
sock.on('new-comment', (data) => {
    console.log("DATA:", data.commentData);
    ls.emitter.emit("socket-new-comment", data);
});

sock.on('new-vote', function (data) {
    console.log("VOTE!", data);
    let area = data.targetType == "comment" ? $("#vote_area_" + data.targetType + "_" + data.targetId) : $("#vote_total_" + data.targetType + "_" + data.targetId);
    if (data.rating == 0 && data.voteCount > 0) {
        area.addClass("vote-count-mixed");
        area.removeClass("vote-count-positive");
        area.removeClass("vote-count-negative");
        area.removeClass("action-hidden");
        $("#vote_total_" + data.targetType + "_" + data.targetId).html(data.rating)
    } else if (data.rating == 0) {
        area.addClass("action-hidden");
        area.removeClass("vote-count-positive");
        area.removeClass("vote-count-negative");
        area.removeClass("vote-count-mixed");
        $("#vote_total_" + data.targetType + "_" + data.targetId).html(data.rating)
    } else if (data.rating > 0) {
        area.addClass("vote-count-positive");
        area.removeClass("vote-count-negative");
        area.removeClass("vote-count-mixed");
        area.removeClass("action-hidden");
        $("#vote_total_" + data.targetType + "_" + data.targetId).html("+" + data.rating)
    } else {
        area.addClass("vote-count-negative");
        area.removeClass("vote-count-positive");
        area.removeClass("vote-count-mixed");
        area.removeClass("action-hidden");
        $("#vote_total_" + data.targetType + "_" + data.targetId).html(data.rating)
    }
    if (isFinite(data.voteCount)) {
        area.data("count", data.voteCount);
    }
});

sock.on('site-update', function () {
    ls.msg.notice('Сайт был обновлен', 'Рекомендуется перезагрузить страницу');
    if (checkPerm("sound_notice"))
        nAudio.play()
});
