import '@babel/polyfill';
import './jquery';
import './jquery/jquery.markitup'
import './jquery/jquery.file'
import hljs from "highlightjs";

window.getCookie = function (name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}


window.switchTheme = function () {
    let date = new Date;
    date.setDate(date.getDate() + 100);
    if (getCookie("SiteStyle") == "Dark") {
        document.cookie = "SiteStyle=Light; path=/; expires=" + date.toUTCString();
    } else {
        document.cookie = "SiteStyle=Dark; path=/; expires=" + date.toUTCString();
    }
    location.reload();
}



window.checkPerm = function (key) {
    let perm = localStorage.getItem(key)
    return (perm == null || parseInt(perm))
}


String.prototype.tr = function (a, p) {
    var k;
    var p = typeof(p) == 'string' ? p : '';
    var s = this;
    jQuery.each(a, function (k) {
        var tk = p ? p.split('/') : [];
        tk[tk.length] = k;
        var tp = tk.join('/');
        if (typeof(a[k]) == 'object') {
            s = s.tr(a[k], tp);
        } else {
            s = s.replace((new RegExp('%%' + tp + '%%', 'g')), a[k]);
        }
        ;
    });
    return s;
};

let extend = function (self, obj) {
    for (var i in obj) {
        if (obj.hasOwnProperty(i)) {
            self[i] = obj[i];
        }
    }
};

import init from './template'

init()

import * as Ajax from './ajax'
import * as Blog from './blog'
import * as Subscribe from './subscribe'
import * as Settings from './settings'
import * as Comments from './comments'
import * as Lang from './lang'
import * as Userfeed from './userfeed'
import * as Stream from './stream'
import * as Toolbar from './toolbar'
import * as Poll from './poll'
import * as Timer from './timer'
import * as Topic from './topic'
import * as Userfield from './userfield'
import * as Tools from './tools'
import * as Vote from './vote'
import * as User from './user'
import * as Wall from './wall'
import * as Usernote from './usernote'
import * as Talk from './talk'
import * as Favourite from './favourite'
import * as Geo from './geo'
import * as Registry from './registry'
import * as Blocks from './blocks'
import * as Autocomplete from './autocomplete'
import Emitter from './emitter'
import * as Msg from './msg'
import * as Quotes from './quotes'


let ls = {
    ajax: Ajax,
    blog: Blog,
    subscribe: Subscribe,
    settings: Settings,
    comments: Comments,
    lang: Lang,
    userfeed: Userfeed,
    stream: Stream,
    toolbar: Toolbar,
    poll: Poll,
    timer: Timer,
    topic: Topic,
    userfield: Userfield,
    tools: Tools,
    vote: Vote,
    user: User,
    wall: Wall,
    usernote: Usernote,
    talk: Talk,
    favourite: Favourite,
    geo: Geo,
    registry: Registry,
    blocks: Blocks,
    autocomplete: Autocomplete,
    emitter: Emitter,
    msg: Msg,
    quotes: Quotes,
}

console.log(ls)
window.ls = ls;

console.log("Hello, world!")

window.lastWindow = 0
window.info = {action: "", target: null, visible: false, currentWindow: 0}

async function handler(e) {
    let profile_exp = /\/profile\/[a-zA-Zа-яА-Я_0-9]+$/
    let profile_exp_s = /\/profile\/[a-zA-Zа-яА-Я_0-9]+\/$/
    // let blog_exp = /\/blog\/[a-zA-Z]+$/
    // let blog_exp_s = /\/blog\/[a-zA-Z]+\/$/
    let topic_exp = /\/blog\/[a-zA-Z]+\/\d+$/
    // let talk_exp = /\/talk\/read\/\d+$/
    // let talk_exp_s = /\/talk\/read\/\d+\/$/
    let comment_exp = /#comment\d+$/

    function cb(target, el, windowid) {
        if (info.target != target && !$(info.target).parents(`[data-floatwindowid="${windowid}"]`).length) {
            // console.log("REMOVE", windowid, info.currentWindow)
            info.visible = false
            el.animate({opacity: 0}, 100)
            setTimeout(() => el.remove(), 100)
            return
        }
        // console.log("TIMEOUT")
        setTimeout(cb.bind({}, target, el, windowid), 1000)
    }

    info.action = ""

    if (e.target.tagName == "A" || $(e.target).parents("a").length) {
        if (e.target == info.target) {
            return
        }
        let s = e.target.getAttribute("href") ||$(e.target).parents("a")[0].getAttribute("href")
        if(!s) return
        info.target = e.target
        if (s.match(profile_exp))
            info.action = "profile"
        if (s.match(profile_exp_s))
            info.action = "profile"
        // if (s.match(blog_exp))
        //     info.action = "blog"
        // if (s.match(blog_exp_s))
        //     info.action = "blog"
        if (s.match(topic_exp))
            info.action = "topic"
        // if (s.match(talk_exp))
        //     info.action = "talk"
        // if (s.match(talk_exp_s))
        //     info.action = "talk"
        if (s.match(comment_exp))
            info.action = "comment"

        // console.log(window.info.currentWindow, info.currentWindow)
        let data = {}
        switch (info.action) {
            case "topic":
                data = {iTopicId: s.match(/\d+/)[0]}
                break
            case "comment":
                data = {iCommentId: s.match(/\d+$/)[0]}
                break
            case "profile":
                data = {sLogin: (s.match(/\/profile\/[a-zA-Zа-яА-Я_0-9]+$/) || s.match(/\/profile\/[a-zA-Zа-яА-Я_0-9]+\/$/))[0].replace("/profile/", "").replace("/", "")}
                break
            default:
                return
        }

        await new Promise(function (resolve) {
            setTimeout(() => resolve(), parseInt(localStorage.getItem("float_window_wait"))||1000)
        })

        if (info.target != e.target) {
            return
        }

        if ((info.currentWindow == $(e.target).data('floatwindow')) && info.visible) {
            let el = $(`[data-floatwindowid="${$(e.target).data('floatwindow')}"]`)
            el.css({left: (e.pageX>($(window).width()/2)? e.pageX-el.width()+10 : e.pageX-10) + "px"})
            return
        }

        let resp = await Ajax.asyncAjax("/info/" + info.action, data)
        console.log("AJAX Loaded")

        lastWindow += 1
        info.currentWindow = lastWindow

        $(e.target).data('floatwindow', info.currentWindow)

        let el = $(`
                <div class="float-info" data-floatwindowid="${info.currentWindow}">
                    ${resp.sText}
                </div>
            `)
        el.appendTo(document.body)
        el.css({
            opacity: 0
        })
        if (e.clientY>($(window).height()/2) && (e.pageY-el.height())>0) {
            // console.log("BOTTOM")
            el.css({
                bottom: $(window).height() - e.pageY + "px",
                left: (e.pageX > ($(window).width() / 2) ? e.pageX - el.width() : e.pageX) + "px",
            })
        } else {
            // console.log("TOP")
            el.css({
                top: e.pageY + 5 + "px",
                left: (e.pageX > ($(window).width() / 2) ? e.pageX - el.width() : e.pageX) + "px",
            })
        }
        el.animate({opacity: 1}, 300)
        el.find(`pre code`).each((k,el)=>hljs.highlightBlock(el))

        setTimeout(cb.bind({}, e.target, el, info.currentWindow), 1000)

        info.visible = true
    }

    info.target = e.target

}

if(!(/Mobi/i.test(navigator.userAgent) || /Android/i.test(navigator.userAgent)))
    $(document).ready(() => document.body.onmouseover = document.body.onmouseout = handler)

$(document).ready(function() {
    $(`pre code`).each((k,el)=>hljs.highlightBlock(el))
})