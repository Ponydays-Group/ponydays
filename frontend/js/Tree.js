import Comment from "./Comment"
import $ from "jquery"
import * as Comments from "./comments"
import * as Vote from "./vote"
import Emitter from "./emitter"
import {updateImgs} from "./template.js"

const dateFormat = require("dateformat");
const DEBUG = 0;

class commentSortTree {
    constructor() {
        this.root = null;
    }

    addId(id, pid) {
        if(!this.root) {
            this.root = new commentSortTreeNode(id);
        } else if(!this.root.appendId(id, pid)) {
            this.root.appendAsBro(id);
        }
    }

    getSorted() {
        if(!this.root) {
            return [];
        }

        return this.root.getIds();
    }
}

class commentSortTreeNode {
    constructor(id) {
        this.id = id;
        this.child = null;
        this.bro = null;
    }

    appendId(id, pid) {
        // На случай повторений.
        if(this.id === id)
            return true;

        if(pid === null)
            return this.appendAsBro(id);

        if(this.id > pid)
            return false;

        if(this.id === pid) {
            if(!this.child) {
                this.child = new commentSortTreeNode(id);
                return true;
            } else {
                return this.child.appendAsBro(id);
            }
        } else {
            if(this.child && this.child.appendId(id, pid))
                return true;

            let target = this;

            while(target.bro) {
                target = target.bro;

                if(target.id === pid)
                    return target.appendId(id, pid);

                if(target.child && target.child.appendId(id, pid))
                    return true;
            }
        }

        return false;
    }

    appendAsBro(id) {
        if(this.id === id)
            return true;

        let target = this;
        while(target.bro !== null) {
            target = target.bro;

            // На случай повторений. Раскомментировать, если буду использовать одно дерево
            if(target.id === id)
                return true;

        }
        target.bro = new commentSortTreeNode(id);
        return true;
    }

    getIds() {
        let aSortedIds = [];

        aSortedIds.push(this.id);

        if(this.child)
            aSortedIds = aSortedIds.concat(this.child.getIds());

        let target = this;
        while(target.bro) {
            target = target.bro;

            aSortedIds.push(target.id);

            if(target.child)
                aSortedIds = aSortedIds.concat(target.child.getIds());
        }

        return aSortedIds;
    }
}

export default class Tree {
    state = {
        mySortTree: null,
        aSortedIds: [],
        aSortedIdsOld: [],
        aComments: [],
        iMaxNesting: 0,
        aCommentsNew: [],
        aCommentsOld: [],
        lastNewComment: 0,
        lastReadComment: 0,
        hidden: [],
    };

    constructor() {
        this.obj = $("#comments-tree");

        Emitter.on("comments-new-loaded", this.handleNewComments.bind(this));
        Emitter.on("comments-edited-loaded", this.checkEdited.bind(this));
        Emitter.on("go-to-next-comment", this.goToNextComment.bind(this));
        Emitter.on("go-to-prev-comment", this.goToPrevComment.bind(this));
        Emitter.on("go-to-comment", this.goToComment.bind(this));
        Emitter.on("comments-calc-nesting", this.updateNesting.bind(this));
    }

    updateCommentDeleted(id, deleted, reason) {
        const cmt = $(`[data-id="${id}"]`);
        if(!cmt.length) {
            return;
        }
        if(deleted) {
            cmt.addClass("comment-deleted");
            cmt.find(".comment-content").html(`
        <div class="delete-reason">
            ${reason || "Нет причины удаления"}
        </div>

            ${LOGGED_IN && (IS_ADMIN || cmt.hasClass("comment-self")) ? `<a href="#" onclick="ls.comments.showHiddenComment(${this.id}); return false;">Раскрыть комментарий</a>` : ""}`);
        } else {
            cmt.removeClass("comment-deleted");
            Comments.showHiddenComment(id);
        }
    }

    updateCommentEdited(id, sText) {
        const cmt = $(`[data-id="${id}"]`);
        if(!cmt.length) {
            return;
        }
        cmt.find(".comment-edited").css("display", "inline-block");
        cmt.find(".comment-content").html(sText);
    }

    calcNesting() {
        let minWidth = parseInt(localStorage.getItem("min_comment_width"));

        if(!minWidth) {
            localStorage.setItem("min_comment_width", 250);
            minWidth = 250;
        }

        window.iMaxNesting = parseInt(($("#comments").width() - minWidth) / 20);
    }

    renderNewComments(aNewComments) {
        let iPrevId = 0;	// последний добавленный айдишник
        let iOld = 0;		// индекс последнего совпавшего значения в старом массиве
        let bUseNew = false;// true, если новые комментарии идут подряд. Означает - использовать последний добавленный ID вместо значений из массива старых

        // цикл пробегает по всему массиву комментариев и сравнивает со старым, все изменения - отрисовывает
        this.state.aSortedIds.forEach(function(id, i) {
            // совпадение со старым массивом - пропускаем шаг
            if(id === this.state.aSortedIdsOld[iOld]) {
                if(bUseNew)
                    bUseNew = false;

                iOld++;
                return
            }

            /*if($(`[data-id=${id}]`).length) {
                console.log("Comment already exists!")
                return;
            }*/

            if(!bUseNew)
                iPrevId = this.state.aSortedIdsOld[iOld - 1];''

            // let sCmtHtml = render_comment(aNewComments[id], iMaxNesting)
            const cmt = this.state.aComments[id];
            let foldable = false''
            if(i < this.state.aSortedIds.length - 1) {
                foldable = this.state.aComments[this.state.aSortedIds[i + 1]].level > cmt.level''
            }
            const sCmtHtml = cmt.render(false, foldable);
            const oPrevCmt = $(`[data-id=${iPrevId}]`);

            // если не существует предыдущего коммента, считаем комментарий началом новой ветки
            if(oPrevCmt.length !== 1) {
                if(DEBUG)
                    console.info("No needed comment in DOM!");

                $(this.obj).append($(sCmtHtml));
            }

            $(sCmtHtml).insertAfter(oPrevCmt.next(".reply").length ? oPrevCmt.next(".reply") : oPrevCmt);
            const prev_cmt = this.state.aComments[iPrevId];

            if(prev_cmt.level < cmt.level) {
                prev_cmt.update_foldable(true);
            }

            // формируем массив новых ID
            if(!(aNewComments[id].author.login === USERNAME || aNewComments[id].isBad))
                this.state.aCommentsNew.push(id);

            iPrevId = id;
            bUseNew = true;

            updateImgs($(`[data-id=${id}]`));

            if(DEBUG && $(`[data-id=${id}]`).length !== 1) {
                console.error("No inserted comment in DOM!");
            }
        }.bind(this));
        this.update_hidden();
    }

    checkEdited(edited_comments) {
        for(let id in edited_comments) {
            if(edited_comments.hasOwnProperty(id)) {
                const oComment = $(`[data-id=${id}]`);

                if(this.state.aComments[id].text !== edited_comments[id].text && !oComment.hasClass("comment-self") && !oComment.hasClass("comment-current") && !$(`#comment_content_id_${id}`).hasClass("hidden")) {
                    $(`#comment_content_id_${id}`)[0].innerHTML = edited_comments[id].text;
                    this.state.aComments[id].text = edited_comments[id].text;
                    oComment.addClass("comment-new");
                    this.state.aCommentsNew.push(id);
                    this.state.aCommentsNew.sort(this.sortByTree.bind(this));
                    this.updateCommentsNewCount();
                }
                if(this.state.aComments[id].text !== edited_comments[id].text) {
                    this.state.aComments[id].update_edited(true);
                }
                updateImgs(oComment);
            }
        }
    }

    sortByTree(a, b) {
        const a_index = this.state.aSortedIds.indexOf(a);
        const b_index = this.state.aSortedIds.indexOf(b);

        if(a_index < b_index) {
            return -1;
        } else {
            return 1;
        }
    }


    handleNewComments(aNewComments, selfIdComment, soft) {
        // массивы, с которых пополняется делево
        const aUnsortedIds = [];
        const aUnsortedPids = [];

        for(let id in aNewComments) {
            if(aNewComments.hasOwnProperty(id) && !this.state.aComments.hasOwnProperty(id)) {
                const oComment = aNewComments[id];

                // вычисление уловня вложенности
                oComment.level = oComment.parentId ? 1 + this.state.aComments[oComment.parentId].level : 0;
                this.state.aComments[id] = new Comment(oComment);

                aUnsortedIds.push(id);
                aUnsortedPids.push(oComment.parentId + "");
            }
        }

        // сохраняем старый массив для вычисления разности
        this.state.aSortedIdsOld = this.state.aSortedIds;
        this.state.aSortedIds = this.updateSortTree(aUnsortedIds, aUnsortedPids);

        if(DEBUG) {
            console.log("Unsorted new IDs", aUnsortedIds);
            console.log("Old sorted IDs", this.state.aSortedIdsOld);
            console.log("New sorted IDs", this.state.aSortedIds);
        }

        // "мягкое" обновление не убирает старых непрочитанных комментов
        if(!soft)
            this.state.aCommentsNew = [];

        // добавляет новые комментарии на страницу
        this.renderNewComments(aNewComments);

        if(soft)
            this.state.aCommentsNew.sort(this.sortByTree.bind(this));

        this.updateCommentsNewCount();

        // скроллим к новому комментарию
        if(selfIdComment && $("#comment_id_" + selfIdComment).length) {
            Comments.scrollToComment(selfIdComment);
        }
    }

    updateCommentsNewCount() {
        const oCounter = document.getElementById("new_comments_counter");
        if(!oCounter) return;

        const count = $(".comment-new:visible").length;
        if(count) {
            oCounter.style.display = "";
            oCounter.innerHTML = count;

            document.title = `(${count}) ` + TITLE;
            $("#next_new").removeClass("disabled");
        } else {
            oCounter.style.display = "none";
            $("#next_new").addClass("disabled");
            document.title = TITLE;
        }
    }

    goToNextComment() {
        // console.log(this.state.aCommentsNew)
        if(!$(".comment-new:visible").length) {
            return false;
        }

        if(this.state.lastNewComment > 0) {
            this.state.aCommentsOld.push(this.state.lastNewComment);
        }

        const id = $(".comment-new:visible")[0].dataset["id"];
        this.state.aComments[id].isNew = false;
        Comments.scrollToComment(id);

        this.state.lastNewComment = id;
        $("#prev_new").removeClass("disabled");
        this.updateCommentsNewCount();
    }

    goToPrevComment() {
        if(!this.state.aCommentsOld.length) {
            return;
        }

        Comments.scrollToComment(this.state.aCommentsOld.pop());
    }

    goToComment(id) {
        if(this.state.aComments[id].isBad) {
            return;
        }

        const oComment = $(`[data-id=${id}]`);
        const oCommentCurrent = $(".comment-current");

        if(!oComment.length) {
            return false;
        }

        const body = $("html, body");
        body.stop();
        body.animate({
            scrollTop: oComment.offset().top - 250,
        }, 150);

        if(oCommentCurrent.length) {
            oCommentCurrent.removeClass("comment-current");
        }

        oComment.addClass("comment-current");
        oComment.removeClass("comment-new");

        if(this.state.aCommentsNew.indexOf("" + id) > (-1)) {
            this.state.aCommentsNew.splice(this.state.aCommentsNew.indexOf("" + id), 1);
        }

        this.updateCommentsNewCount();
        Comments.iCurrentViewComment = id;
    }

    mount(obj, comments, lastReadComment = 0) {
        $("#autoload").change(function() {
            if(this.checked) {
                ls.comments.load(targetId, targetType, false, true);
            }
        });
        lastReadComment = parseInt(lastReadComment);

        if(targetType == "topic") {
            sock.emit("listenTopic", {id: targetId});
            sock.on("reconnect", () => sock.emit("listenTopic", {id: targetId}));
        }

        Emitter.on("socket-edit-comment", (data) => this.updateCommentEdited(data.commentData.id, data.commentData.text));
        Emitter.on("socket-delete-comment", (data) => this.updateCommentDeleted(data.commentData.id, parseInt(data.delete), data.deleteReason));
        Emitter.on("socket-new-comment", (data) => {
            if(!document.getElementById("autoload").checked)
                return;
            // let a = {}
            // a[data.commentData.id]=data.commentData
            // this.handleNewComments(a, false,true)
            ls.comments.load(targetId, targetType, false, true);
        });

        function applyNesting() {
            this.calcNesting();
            this.render();
        }

        this.obj = obj;
        //$(window).on('resize', updateNesting.bind(this))

        const ids = [];
        const pids = [];

        for(let id in comments) {
            if(comments.hasOwnProperty(id)) {
                ids.push(id);
                pids.push(comments[id].parentId);
                this.state.aComments[id] = new Comment(comments[id]);
                if(lastReadComment && !LOGGED_IN) {
                    this.state.aComments[id].isNew = parseInt(id) > lastReadComment;
                }
            }
        }

        this.state.aSortedIds = this.updateSortTree(ids, pids)

        // this.state.aComments = comments

        if(DEBUG) {
            console.log("comment mount", comments);
            console.log("ids mount", ids);
            console.log("ids sorted", this.state.aSortedIds);
        }

        this.state.lastReadComment = lastReadComment;

        applyNesting.bind(this)();

        // Заполнение массива ID новых сообщений
        $(".comment-new").each(function(k, v) {
            this.state.aCommentsNew.push("" + $(v).data("id"));
        }.bind(this));

        this.updateCommentsNewCount();
        this.initShortcuts();
    }

    initShortcuts() {
        function goToPrevComment() {
            Comments.scrollToComment(this.state.aSortedIds[this.state.aSortedIds.indexOf("" + $(".comment-current").data("id")) - 1]);
        }

        function goToNextComment() {
            Comments.scrollToComment(this.state.aSortedIds[this.state.aSortedIds.indexOf("" + $(".comment-current").data("id")) + 1]);
        }

        function goToLastComment() {
            Comments.scrollToComment(this.state.aSortedIds[this.state.aSortedIds.length - 1]);
        }

        function goToFirstComment() {
            Comments.scrollToComment(this.state.aSortedIds[0]);
        }

        function goToNextBranch() {
            const cur_id = $(".comment-current").data("id");
            const data = this.state.aSortedIds.slice(this.state.aSortedIds.indexOf("" + cur_id) + 1);
            let prev_branch = this.state.aSortedIds[0];
            for(let key in data) {
                if(data.hasOwnProperty(key)) {
                    const id = data[key];
                    const cmt = this.state.aComments[id];
                    if(parseInt(cmt.level) === 0) {
                        prev_branch = cmt.id;
                        break
                    }
                }
            }
            Comments.scrollToComment(prev_branch);
        }

        function goToPrevBranch() {
            const cur_id = $(".comment-current").data("id");
            const data = this.state.aSortedIds.slice(0, this.state.aSortedIds.indexOf("" + cur_id)).reverse();
            let prev_branch = this.state.aSortedIds[0];
            for(let key in data) {
                if(data.hasOwnProperty(key)) {
                    const id = data[key];
                    const cmt = this.state.aComments[id];
                    if(parseInt(cmt.level) === 0) {
                        prev_branch = cmt.id;
                        break;
                    }
                }
            }
            Comments.scrollToComment(prev_branch);
        }

        function toggleReplyOnCurrent() {
            Comments.toggleCommentForm($(".comment-current").data("id"));
        }

        function updateComments() {
            Comments.load(window.targetId, window.targetType);
        }

        function updateCommentsSoft() {
            Comments.load(window.targetId, window.targetType, null, true);
        }

        function toggleReplyOnRoot() {
            Comments.toggleCommentForm(0);
        }

        function editComment() {
            Comments.editComment($(".comment-current").data("id"));
        }

        function goToParent() {
            let oCommentCurrent = $(".comment-current")
            Comments.goToParentComment(oCommentCurrent.data("id"), oCommentCurrent.data("pid"));
        }

        function goToChild() {
            const oCommentCurrent = $(".comment-current");

            oCommentCurrent.find("." + Comments.options.classes.comment_goto_child).hide();
            Comments.scrollToComment(oCommentCurrent.data("cid"));
        }

        const despoilComment = function() {
            const current = $(".comment-current");
            const despoiled = current.hasClass("comment-despoiled");
            current.find(".spoiler-body").each(function(k, v) {
                if(v.style.display !== "block") {
                    if(!despoiled) window.openSpoiler(v);
                } else {
                    if(despoiled) window.closeSpoiler(v);
                }
            });
            current.toggleClass("comment-despoiled");
        }.bind(this);

        function markAllChildAsRead() {
            const oCommentCurrent = $(".comment-current");

            const ids = this.state.aSortedIds.slice(this.state.aSortedIds.indexOf("" + oCommentCurrent.data("id")) + 1);
            const level = oCommentCurrent.data("level");
            for(let i in ids) {
                if(ids.hasOwnProperty(i)) {
                    const id = ids[i];
                    const cmt = this.state.aComments[id];
                    if(cmt.level <= level) {
                        break
                    }
                    $(`[data-id=${id}]`).removeClass("comment-new");
                    this.state.aCommentsNew.splice(this.state.aCommentsNew.indexOf("" + id), 1);
                }
            }
            this.updateCommentsNewCount();
        }

        function voteUp() {
            Vote.vote($(".comment-current").data("id"), this, 1, "comment");
        }

        function voteDown() {
            Vote.vote($(".comment-current").data("id"), this, -1, "comment");
        }

        const shortcuts = {
            "ctrl+space": Comments.goToNextComment,
            "ctrl+shift+space": Comments.goToPrevComment,
            "ctrl+up": goToPrevComment,
            "ctrl+down": goToNextComment,
            "ctrl+end": goToLastComment,
            "ctrl+home": goToFirstComment,
            "alt+pagedown": goToNextBranch,
            "alt+pageup": goToPrevBranch,
            "alt+r": toggleReplyOnCurrent,
            "alt+u": updateComments,
            "alt+shift+u": updateCommentsSoft,
            "alt+shift+d": window.despoil,
            "alt+shift+s": despoilComment,
            "alt+n": toggleReplyOnRoot,
            "alt+shift+e": editComment,
            "alt+shift+p": goToParent,
            "alt+shift+c": goToChild,
            "alt+shift+m": markAllChildAsRead,
            "alt+shift+w": window.widemode,
            "alt+up": voteUp,
            "alt+down": voteDown,
        };

        const oFormText = $("#form_comment_text");

        for(let i in shortcuts) {
            $(document).on("keydown", null, i, function(e) {
                shortcuts[i].apply(this);
                e.preventDefault();
            }.bind(this));
            oFormText.on("keydown", null, i, function(e) {
                shortcuts[i].apply(this);
                e.preventDefault();
            }.bind(this));
        }

        oFormText.off("keydown", shortcuts["ctrl+end"]);
        oFormText.off("keydown", shortcuts["ctrl+home"]);

        window.foldBranch = function(id) {
            let foldings = localStorage.getItem("foldings_" + this.state.aComments[this.state.aSortedIds[0]].targetType + "_" + targetId); //||"".split(',') || []
            if(!foldings) {
                foldings = [];
            } else {
                foldings = foldings.split(",");
            }
            foldings.push(id);

            localStorage.setItem("foldings_" + targetType + "_" + targetId, foldings.join(","));
            if($("#folded_branch_" + id).length > 0) {
                $("#folded_branch_" + id).addClass("folded");
                $("#comment_id_" + id).addClass("comment-folding-start");
                this.updateCommentsNewCount();
                return;
            }
            const comment = this.state.aComments[id];
            const to_fold = [];
            let found = false;
            for(let i = this.state.aSortedIds.indexOf(id) + 1; !found; i++) {
                if(this.state.aComments[this.state.aSortedIds[i]].level > comment.level) {
                    to_fold.push(this.state.aSortedIds[i]);
                } else {
                    found = true;
                }
            }
            $("#comment_id_" + to_fold.join(", #comment_id_")).wrapAll(`<div class='folding-comments'></div>`);
            $(`#comment_id_${to_fold[0]}`).parent().wrap(`<div class='folding folded' id="folded_branch_${id}" data-commentid='${id}'></div>`);
            $("#comment_id_" + id).addClass("comment-folding-start");
            this.updateCommentsNewCount();
        }.bind(this);

        window.unfoldBranch = function(id) {
            const foldings = localStorage.getItem("foldings_" + this.state.aComments[this.state.aSortedIds[0]].targetType + "_" + targetId).split(",") || [];
            foldings.pop(id);
            localStorage.setItem("foldings_" + targetType + "_" + targetId, foldings.join(","));
            $("#folded_branch_" + id).removeClass("folded");
            $("#comment_id_" + id).removeClass("comment-folding-start");
            this.updateCommentsNewCount();
        }.bind(this);
    }

    updateSortTree(ids, pids) {
        if(!this.state.mySortTree)
            this.state.mySortTree = new commentSortTree();

        for(let i = 0; i < ids.length; i++)
            this.state.mySortTree.addId(ids[i], pids[i]);

        return this.state.mySortTree.getSorted();
    }

    update_hidden() {
        this.state.hidden = localStorage.getItem("comments_hide");
        if(this.state.hidden == null) {
            this.state.hidden = [];
        } else {
            this.state.hidden = this.state.hidden.split(",");
        }
        for(let i = 0; i < this.state.hidden.length; i++) {
            Comments.hideComment(this.state.hidden[i]);
        }
    }

    render() {
        let foldings = localStorage.getItem("foldings_" + this.state.aComments[this.state.aSortedIds[0]].targetType + "_" + targetId); //||"".split(',') || []
        if(!foldings) {
            foldings = [];
        } else {
            foldings = foldings.split(",");
        }

        const foldings_tree = [];
        this.obj[0].innerHTML = `<div>${this.state.aSortedIds.map(function(id, i) {
            const folding_starting = foldings.indexOf(id) >= 0;
            const comment = this.state.aComments[id];
            let foldable = false;
            if(i != this.state.aSortedIds.length - 1) {
                foldable = this.state.aComments[this.state.aSortedIds[i + 1]].level > comment.level;
            }
            let s = comment.render(folding_starting, foldable);
            if(foldings_tree[foldings_tree.length - 1] >= parseInt(comment.level)) {
                s = "</div></div>" + s;
                foldings_tree.pop();
            }
            if(folding_starting) {
                s += `<div class='folding folded' id="folded_branch_${id}" data-commentid='${id}'><div class='folding-comments'>`;
                foldings_tree.push(parseInt(comment.level));
            }
            return s;
            //return render_comment(this.state.aComments[id], iMaxNesting)
        }.bind(this)).join("")}</div>`;

        updateImgs();
        this.update_hidden();
    }

    updateNesting() {
        let minWidth = parseInt(localStorage.getItem("min_comment_width"));

        if(!minWidth) {
            localStorage.setItem("min_comment_width", 250);
            minWidth = 250;
        }

        window.iMaxNesting = parseInt(($("#comments").width() - minWidth) / 20);

        const aComments = $(".comment");

        aComments.each(function(i, comment) {
            const level = +$(comment).attr("data-level") > iMaxNesting ? iMaxNesting : +$(comment).attr("data-level");

            $(comment).css("margin-left", level * 20 + "px");
        }.bind(this));
    }
}
