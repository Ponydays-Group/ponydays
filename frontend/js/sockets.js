function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

if (!(getCookie("key")||getCookie("wskey"))&&LOGGED_IN) {
    ls.msg.error("Необходимо войти в аккаунт еще раз.", "Были внесены изменения в механизм входа")
}

const SimpleWebRTC = require("simplewebrtc")
const hark = require("hark")
const io = require("socket.io-client")

window.sock = io(SOCKET_URL, {
    query: {
        token: getCookie("key")
    }
})

sock.on('reconnect_attempt', () => {
    sock.io.opts.query = {
        token: getCookie("key")||getCookie("wskey")
    }
});

window.nAudio = new Audio()
nAudio.src = localStorage.getItem("notice_sound_url") || "http://freesound.org/data/previews/245/245645_1038806-lq.mp3"

sock.on("reply-info", function (data) {
    console.log("DATA:", data)
    if (checkPerm("notice_reply")) {
        let url = "/blog/undefined/" + data.targetId + "#comment" + data.commentData.id
        let blank = true
        if (location.pathname.startsWith("/blog/") && location.pathname.endsWith(data.targetId)) {
            url = "#comment" + data.commentData.id
            blank = false
        }
        ls.msg.notice(data.senderLogin + " ответил вам в посте " + data.targetTitle, data.commentData.text, url, blank)
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
})

sock.on("edit-comment-info", function (data) {
    console.log("DATA:", data)
    if (checkPerm("notice_comment_delete")) {
        let url = "/blog/undefined/" + data.targetId + "#comment" + data.commentData.id
        let blank = true
        if (location.pathname.startsWith("/blog/") && location.pathname.endsWith(data.targetId)) {
            url = "#comment" + data.commentData.id
            blank = false
        }
        ls.msg.notice(data.senderLogin + " отредактировал ваш комментарий", data.commentData.text, url, blank)
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
})

sock.on("delete-comment-info", function (data) {
    console.log("DATA:", data)
    data.delete = parseInt(data.delete)
    if (checkPerm("notice_comment_delete")) {
        let url = "/blog/undefined/" + data.targetId + "#comment" + data.commentId
        let blank = true
        if (location.pathname.startsWith("/blog/") && location.pathname.endsWith(data.targetId)) {
            url = "#comment" + data.commentId
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
})

sock.on("talk-answer", function (data) {
    console.log("DATA:", data)
    if (checkPerm("notice_talk_reply")) {
        let url = "/talk/read/" + data.targetId + "#comment" + data.commentData.id
        let blank = true
        if (location.pathname.startsWith("/talk/") && (location.pathname.endsWith(data.targetId) || location.pathname.endsWith(data.targetId + "/"))) {
            url = "#comment" + data.commentData.id
            blank = false
        }
        ls.msg.notice("Новый комментарий в " + data.targetTitle + ", от " + data.senderLogin, data.commentText, url, blank)
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
})

sock.on("vote-info", function (data) {
    console.log("DATA:", data)
    if (checkPerm("notice_vote")) {
        let url = "/blog/undefined/" + data.targetId
        let blank = true
        if (data.targetParentId) {
            url = "/blog/undefined/" + data.targetParentId + "#comment" + data.targetId
        }
        if (location.pathname.startsWith("/blog/") && (location.pathname.endsWith(data.targetParentId) || location.pathname.endsWith(data.targetParentId + "/"))) {
            url = "#comment" + data.targetId
            blank = false
        }
        if (location.pathname.startsWith("/blog/") && (location.pathname.endsWith(data.targetId) || location.pathname.endsWith(data.targetId + "/")) && data.targetType == "topic") {
            url = "#"
            blank = false
        }
        ls.msg.notice("За ваш " + (data.targetType == "comment" ? "комментарий" : "пост") + " проголосовали", data.commentText || data.topicTitle, url, blank)
        if (checkPerm("sound_notice"))
            nAudio.play()
    }
})

sock.on('edit-comment', (data) => ls.emitter.emit("socket-edit-comment", data))
sock.on('delete-comment', (data) => ls.emitter.emit("socket-delete-comment", data))
sock.on('new-comment', (data) => {
    console.log("DATA:", data.commentData);
    ls.emitter.emit("socket-new-comment", data);
})

sock.on('new-vote', function (data) {
    console.log("VOTE!", data)
    let area = data.targetType == "comment" ? $("#vote_area_" + data.targetType + "_" + data.targetId) : $("#vote_total_" + data.targetType + "_" + data.targetId)
    if (data.rating == 0) {
        area.removeClass("vote-count-positive")
        area.removeClass("vote-count-negative")
        $("#vote_total_" + data.targetType + "_" + data.targetId).html(data.rating)
    } else if (data.rating > 0) {
        area.addClass("vote-count-positive")
        area.removeClass("vote-count-negative")
        area.removeClass("action-hidden")
        $("#vote_total_" + data.targetType + "_" + data.targetId).html("+" + data.rating)
    } else {
        area.addClass("vote-count-negative")
        area.removeClass("vote-count-positive")
        area.removeClass("action-hidden")
        $("#vote_total_" + data.targetType + "_" + data.targetId).html(data.rating)
    }
})

sock.on('site-update', function () {
    ls.msg.notice('Сайт был обновлен', 'Рекомендуется перезагрузить страницу')
    if (checkPerm("sound_notice"))
        nAudio.play()
})

function changeRTCstatus(status) {
    $("#webrtc-status")[0].className = status
}



if (LOGGED_IN && location.pathname.match(/\/voice\/voice/)) {
    function updateRTCusers(users) {
        rtcUsers = users
        $("#voice-users").html(Object.values(rtcUsers).map((data) => `<span class="user" id="voice-user-${data.id}"><img src="${data.avatar}" class="avatar" width=40 height=40 />${data.login} <i ${data.audio? 'style="display: none"':""} class="material-icons">mic_off</i>${data.id!=USER_ID?`<input type="range" id="voice_${data.id}" min="0" value="1" max="1" step="0.05"></span>`:""}`))
        Object.values(rtcUsers).map((data) => {if(data.id!=USER_ID){$("#voice_"+data.id).on('change', (e)=>$("video[data-userid='"+data.id+"']").each((k,v)=>v.volume=e.target.value))}})

    }
    window.rtcUsers = []
    async function startRTC() {
        let canVideo = false
        let canAudio = false

        try {
            let stream = await navigator.mediaDevices.getUserMedia({video: true})
            canVideo = {width: 1280, height: 720}//true;
            stream.stop()
        } catch(e) {
            console.info("No video")
        }
        try {
            let stream = await navigator.mediaDevices.getUserMedia({audio: true})
            canAudio = true;
            stream.stop()
        } catch(e) {
            console.info("No audio")
        }

        initWRTC({audio: canAudio, video: canVideo})
    }
    startRTC()

    sock.on('user joined', updateRTCusers)
    sock.on('user leaved', updateRTCusers)
    sock.on('rtc users', updateRTCusers)

    sock.on('speaking', (id)=>$("#voice-user-"+id).addClass("speaking"))
    sock.on('stopped speaking', (id)=>$("#voice-user-"+id).removeClass("speaking"))

    sock.on('mute', function(id){
        rtcUsers[id].audio =false
        $("#voice-user-"+id+" i").css("display", "inline-flex")
    })
    sock.on('unmute', function(id){
        rtcUsers[id].audio =true
        $("#voice-user-"+id+" i").css("display", "none")
    })

    sock.emit('getRTC')

    sock.on('reconnect', function(){
        sock.emit("joinRTC")
        sock.emit('getRTC')
    })
    sock.on('connect', function(){
        sock.emit("joinRTC")
        sock.emit('getRTC')
    })
    sock.on('connection', function(){
        sock.emit("joinRTC")
        sock.emit('getRTC')
    })
}

function initWRTC(media, join) {
    function toggleMic() {
        let mic_disable = $("#webrtc-disable-micro")
        if (mic_disable.css("display")=="none") {
            mic_disable.css("display", "inline-flex")
            $("#webrtc-enable-micro").css("display", "none")
            wrtc.unmute()
            sock.emit('unmute')
        } else {
            mic_disable.css("display", "none")
            $("#webrtc-enable-micro").css("display", "inline-flex")
            wrtc.mute()
            sock.emit('mute')
        }
    }
    function toggleVideo() {
        let video_disable = $("#webrtc-disable-video")
        if (video_disable.css("display")=="none") {
            video_disable.css("display", "inline-flex")
            $("#webrtc-enable-video").css("display", "none")
            $("#localVideo").css("display", "block")
            wrtc.resumeVideo()
            sock.emit('resume video')
        } else {
            video_disable.css("display", "none")
            $("#webrtc-enable-video").css("display", "inline-flex")
            $("#localVideo").css("display", "none")
            wrtc.pauseVideo()
            sock.emit('pause video')
        }
    }
    $("#webrtc-disable-micro, #webrtc-enable-micro").click(toggleMic)
    $("#webrtc-disable-video, #webrtc-enable-video").click(toggleVideo)
    function cb() {
        wrtc.joinRoom('your awesome room name')
        sock.emit("joinRTC")

        wrtc.mute()
        wrtc.pauseVideo();
        $("#localVideo").css("display", "none")

        $("#webrtc-join").css("display", "none")
        $("#webrtc-leave").css("display", "inline-flex")
        changeRTCstatus("active")
    }

    window.wrtc = new SimpleWebRTC({
        // the id/element dom element that will hold "our" video
        localVideoEl: 'localVideo',
        // the id/element dom element that will hold remote videos
        remoteVideosEl: '',//'remotesVideos',
        // immediately ask for camera access
        autoRequestMedia: true,
        media: media,
        url: 'https://sockets.lunavod.ru/',
        nick: USER_ID
    });

    wrtc.on('readyToCall', cb)

    wrtc.on('*', console.log)
    wrtc.on("localStream", function(stream){
        var speechEvents = hark(stream,{})
        speechEvents.on('speaking', function() {
            console.error("speaking")
            sock.emit("speaking")
        });

        speechEvents.on('stopped_speaking', function() {
            console.error("stopped speaking")
            sock.emit("stopped speaking")
        })
    })

    wrtc.on('speaking', ()=>console.error("Started speaking"));
    wrtc.on('stoppedSpeaking', ()=>console.error("Stopped speaking"));

    wrtc.on('mute', function (data) {
        console.error(data.id)
        wrtc.getPeers(data.id).forEach(function (peer) {
            if (data.name == 'video') {
                $('video#' + wrtc.getDomId(peer) + '').hide();
            }
        });
    });
    wrtc.on('unmute', function (data) {
        console.error(data.id)
        wrtc.getPeers(data.id).forEach(function (peer) {
            if (data.name == 'video') {
                $('video#' + wrtc.getDomId(peer) + '').show();
            }
        });
    });

    wrtc.on('videoAdded', function (video, peer) {
        console.error("ADD VIDEO", video, peer)
        var remotes = $("#remotesMain video:visible").length<2?$("#remotesMain")[0]:$("#remotesSecondary")[0]

        if (remotes) {
            // var container = document.createElement('div');
            // container.className = 'videoContainer';
            // container.id = 'container_' + wrtc.getDomId(peer);
            // if (remotes.id=="remotesMain")
            //     video.width = $(remotes).width()/2+"px"
            // else
            //     video.height = $($("#remotesSecondary")[0]).height()+"px"
            // container.appendChild(video);

            // suppress contextmenu
            video.oncontextmenu = function () { return false; };
            video.setAttribute('data-userid', peer.nick)

            remotes.appendChild(video);
        }
    });
    wrtc.on('videoRemoved', function (video, peer) {
        console.error('video removed ', peer);
        var remotes = $("#remotesMain video:visible").length<2?$("#remotesMain")[0]:$("#remotesSecondary")[0]
        console.error("REMOVE VIDEO", remotes)
        if (remotes) {
            $(video).hide()
            remotes.removeChild(video);
        }
    });
}