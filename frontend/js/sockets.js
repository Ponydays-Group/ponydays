function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

const io = require("socket.io-client")

window.sock = io("http://sock.dev", {
    query: {
        token: localStorage.getItem("sKey")
    }
})

sock.on("broadcast", function(data){
    console.log("DATA:",data)
})

sock.on("reply-info", function(data){
    console.log("DATA:",data)
    let perm = localStorage.getItem("notice_reply")
    if (perm==null||parseInt(perm)) {
        ls.msg.notice(data.senderLogin+" ответил вам в посте "+data.targetTitle, data.commentData.text)
    }
})

sock.on("edit-comment-info", function(data){
    console.log("DATA:",data)
    let perm = localStorage.getItem("notice_comment_edit")
    if (perm==null||parseInt(perm)) {
        ls.msg.notice(data.senderLogin + " отредактировал ваш комментарий", data.commentData.text)
    }
})

sock.on('edit-comment', (data)=>ls.emitter.emit("socket-edit-comment", data))
sock.on('delete-comment', (data)=>ls.emitter.emit("socket-delete-comment", data))
sock.on('new-comment', (data)=>{console.log("DATA:",data.commentData);ls.emitter.emit("socket-new-comment", data);})

sock.on("delete-comment-info", function(data){
    console.log("DATA:", data)
    data.delete =parseInt(data.delete)
    let perm = localStorage.getItem("notice_reply")
    if (perm==null||parseInt(perm)) {
        if (data.delete) {
            ls.msg.notice(data.senderLogin + " удалил ваш комментарий", data.deleteReason)
        } else {
            ls.msg.notice(data.senderLogin + " восстановил ваш комментарий")
        }
    }
})

sock.on("talk-answer", function(data){
    console.log("DATA:",data)
    let perm = localStorage.getItem("notice_talk_reply")
    if (perm==null||parseInt(perm)) {
        ls.msg.notice("Новый комментарий в " + data.targetTitle + ", от "+data.senderLogin, data.commentText)
    }
})

sock.on('connect')