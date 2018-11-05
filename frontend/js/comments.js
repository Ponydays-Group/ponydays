import $ from "jquery"

import * as Registry from "./registry"
import * as Blocks from "./blocks"
import Emitter from "./emitter"
import * as Msg from "./msg"
import * as Ajax from "./ajax"
var hljs = require('highlightjs')

import Tree from "./Tree"
import * as Vote from "./vote";

var dateFormat = require("dateformat")

/**
 * Опции
 */
export let options = {
	type: {
		topic: {
			url_add: aRouter.blog + "ajaxaddcomment/",
			url_response: aRouter.blog + "ajaxresponsecomment/",
		},
		talk: {
			url_add: aRouter.talk + "ajaxaddcomment/",
			url_response: aRouter.talk + "ajaxresponsecomment/",
		},
		comment: {
			url: aRouter.ajax + "comment/",
		},
	},
	classes: {
		form_loader: "loader",
		comment_new: "comment-new",
		comment_current: "comment-current",
		comment_deleted: "comment-deleted",
		comment_self: "comment-self",
		comment: "comment",
		comment_goto_parent: "goto-comment-parent",
		comment_goto_child: "goto-comment-child",
	},
	wysiwyg: null,
	folding: true,
	pageTitle: false // for comment count: original title here
}

export let sBStyle
export let cbsclick
export let iCurrentShowFormComment = 0
export let iCurrentViewComment = null
// export let bSuccessLoaded = true
// export let bStopAutoload = false
export let aCommentNew = []
export let aCommentNewOld = []
export let aCommentOld = []
export let lastNewComment = 0

export function calcNesting() {
    let minWidth = parseInt(localStorage.getItem("min_comment_width"))

    if (!minWidth) {
        localStorage.setItem("min_comment_width", 250)
        minWidth = 250
    }

    window.iMaxNesting = parseInt(($("#comments").width() - minWidth) / 20)
}

export function updateNesting() {

    let aComments = $(".comment")

    let foldings = localStorage.getItem('foldings_' + targetType + '_' + targetId) //||"".split(',') || []
    if (!foldings) {
        foldings = []
    } else {
        foldings = foldings.split(',')
    }

	let prev = null

    aComments.each(function (i, comment) {
    	if (prev)
    		if (parseInt(prev.dataset.level)<parseInt(comment.dataset.level))
    			$(prev).addClass('comment-foldable')
        let level = +$(comment).attr("data-level") > iMaxNesting ? iMaxNesting : +$(comment).attr("data-level")

        $(comment).css("margin-left", level * 20 + "px")

        if (foldings.indexOf(comment.dataset.id.toString())>=0)
        	foldBranch(comment.dataset.id)

		prev = comment
    }.bind(this))

}

export async function loadComments() {
	let url = ""

	if ((location.pathname).startsWith(aRouter["talk"])) {
		url = location.href.replace("read", "readcomments")
	} else {
		url = window.location.pathname + "/comments"
	}

	return await Ajax.asyncAjax(url, {})
}

export async function renderComments() {
	// let url = window.location.pathname + "/comments"
	// let data = await Ajax.asyncAjax(url, {})
	// $("#comments-tree").html(data.sText)
    // lastNewComment = parseInt(localStorage.getItem('lastcomment_'+targetType+'_'+targetId)) || 0
	// let CommentsTree = new Tree()
    //
	// let result = await loadComments()
	// let comments = result.aComments
    //
	// CommentsTree.mount($("#comments-tree"), comments, lastNewComment)
	// if (!LOGGED_IN)
    	// lastNewComment = result.iMaxIdComment
	// localStorage.setItem('lastcomment_'+targetType+'_'+targetId, lastNewComment)
    //
	// if (location.hash.startsWith("#comment") && location.hash !== "#comments") {
	// 	setTimeout(scrollToComment(location.hash.replace("#comment", ""), 0, 350), 2000)
	// }
    //
	// console.log("finish:", dateFormat(new Date(), "HH:MM:ss:l"))
}

// Добавляет комментарий
export function add(formObj, targetId, targetType) {
	if (options.wysiwyg) {
		$("#" + formObj + " textarea").val(tinyMCE.activeEditor.getContent())
	}

	formObj = $("#" + formObj)
	console.log(formObj)

	$("#form_comment_text").addClass(options.classes.form_loader).attr("readonly", true)
	$("#comment-button-submit").attr("disabled", "disabled")

	// bStopAutoload = true;

	Ajax.ajax(options.type[targetType].url_add, formObj.serializeJSON(), function (result) {
		$("#comment-button-submit").removeAttr("disabled")
		if (!result) {
			enableFormComment()
			Msg.error("Error", "Please try again later")
			return
		}
		if (result.bStateError) {
			enableFormComment()
			Msg.error(null, result.sMsg)
		} else {
			enableFormComment()
			$("#form_comment_text").val("")

			// Load new comments
			// Если подгрузка не завершена - ждем...
			// let timer = setTimeout(function waitUntilLoaded() {
			// 	if(bSuccessLoaded) {
			// 		load(targetId, targetType, result.sCommentId, true)
			// 		bStopAutoload = false;
			// 	} else {
			// 		timer = setTimeout(waitUntilLoaded, 100);
			// 	}
			// }, 100);

			load(targetId, targetType, result.sCommentId, true)

			Emitter.emit("ls_comments_add_after", [formObj, targetId, targetType, result])
		}
	}.bind(this), {
		error: function() {
			console.log("ERROR")
            $("#comment-button-submit").removeAttr("disabled")
            enableFormComment()
            Msg.error("Ошибка", "Возможно поможет перезагрузка страницы.")
		}
	})
}

// Активирует форму
export function enableFormComment() {
	$("#form_comment_text").removeClass(options.classes.form_loader).attr("readonly", false)
}

// Показывает/скрывает форму комментирования
export function _toggleCommentForm(idComment, bNoFocus) {
	let reply = $("#reply")
	if (!reply.length) {
		return
	}
	$("#comment_preview_" + iCurrentShowFormComment).remove()

	if (iCurrentShowFormComment === idComment && reply.is(":visible")) {
		reply.hide()
		return
	}
	if (options.wysiwyg) {
		tinyMCE.execCommand("mceRemoveControl", true, "form_comment_text")
	}
	let comment = $("#comment_id_" + idComment)

	reply.insertAfter(comment).show()
	reply.css("margin-left", parseInt(comment.css("margin-left").replace("px","")) + 20)
	if (!comment) {
		reply.css("margin-left", 0)
	}

	let formCommentText = $("#form_comment_text")

	formCommentText.val("")
	$("#form_comment_reply").val(idComment)

	iCurrentShowFormComment = idComment
	if (options.wysiwyg) {
		tinyMCE.execCommand("mceAddControl", true, "form_comment_text")
	}
	if (!bNoFocus)
		formCommentText.focus()
}

// Подгружает новые комментарии
export function load(idTarget, typeTarget, selfIdComment, bNotFlushNew) {
	// bSuccessLoaded = false;

	if (parseInt(lastNewComment)==0) {
        lastNewComment = $("#comment_last_id").val()
	}

	if (aCommentNew !== []) {
		aCommentOld = aCommentNew
	}

	// Удаляем подсветку у комментариев
	if (!bNotFlushNew) {
		$(".comment:visible").each(function (index, item) {
			$(item).removeClass(options.classes.comment_new + " " + options.classes.comment_current)
		}.bind(this))
	}

	let objImg = $("#update-comments")
	objImg.addClass("fa-pulse")

	let params = {
		idCommentLast: lastNewComment,
		idTarget: idTarget,
		typeTarget: typeTarget,
	}

	if (selfIdComment) {
		params.selfIdComment = selfIdComment
	}

	console.log("Before ajax", dateFormat(new Date(), "HH:MM:ss:l"))
	// console.log("before", lastNewComment)
	console.log("params", params)
	Ajax.ajax(options.type[typeTarget].url_response, params, function (result) {
		console.log("params", params)
		console.log("Ajax catched", dateFormat(new Date(), "HH:MM:ss:l"))
		objImg.removeClass("fa-pulse")
		// bSuccessLoaded = true;

		if (!result) {
			Msg.error("Error", "Please try again later")
		}

		let aCmt;

		if (result.bStateError) {
			Msg.error(null, result.sMsg)
		} else {
			aCmt = result.aComments
			console.log("Ajax OK", dateFormat(new Date(), "HH:MM:ss:l"))

			if (Object.keys(aCmt).length > 0 && result.iMaxIdComment) {
				console.log("before", lastNewComment)
				lastNewComment = result.iMaxIdComment
                localStorage.setItem('lastcomment_'+targetType+'_'+targetId, lastNewComment)
				console.log("after", lastNewComment)
			}

			Emitter.emit("comments-new-loaded", result.aComments, selfIdComment, bNotFlushNew)

			if (Object.keys(result.aEditedComments).length > 0) {
				Emitter.emit("comments-edited-loaded", result.aEditedComments)
			}

			let iCountOld = 0

			if (bNotFlushNew) {
				iCountOld = aCommentNew.length
			} else {
				aCommentNew = []
			}

			if (selfIdComment) {
				toggleCommentForm(iCurrentShowFormComment, true)
				// setCountNewComment(aCmt.length - 1 + iCountOld)
			} else {
				// setCountNewComment(aCmt.length + iCountOld)
			}

			// checkFolding()
			aCommentNew = []
			// calcNewComments()
			// if (aCmt.length>0) {
			//  Emitter.emit('ls_comments_load_after', [idTarget, typeTarget, selfIdComment, bNotFlushNew, result])
			// }

            function next(el,selector){
                let l = []
                while(el.next(selector).length){el = el.next(selector);if(el.length){l.push(el)}}
                return l
            }

			for (let i=0;i<aCmt.length;i++) {
				let cmt = aCmt[i]
                if ($(`[data-id="${cmt.id}"]`).length) {
                    continue
                }
				let parent = $(`[data-id="${cmt.idParent}"]`)
				if (parent.length) {
					parent.addClass("comment-foldable")
                    let level = (parseInt(parent.data("level")) + 1)
                    let prev = null
					let next = null
					console.warn(parent.next('.comment'))
					window.pel = parent
					if (parent.hasClass("comment-folding-start")) {
						$(cmt.html).appendTo(`#folded_branch_${parent.data("id")} .folding-comments`).css("margin-left", level * 20 + "px").attr("data-level", level)
						continue
					}
                    parent.nextAll('.comment').each(function(k,v){
						v=$(v)
						console.warn(v.data("level"))
						if(next==null && parseInt(v.data("level"))>(level-1)){
							prev = v
						} else if (next==null && parseInt(v.data("level"))<=(level-1)) {
							next = v
						}
					})
					console.warn(prev, parent)
					level = level<window.iMaxNesting?level:window.iMaxNesting
					if (prev) {
                        $(cmt.html).insertAfter(prev).css("margin-left", level * 20 + "px").attr("data-level", level)
					} else {
                        $(cmt.html).insertAfter(parent).css("margin-left", level * 20 + "px").attr("data-level", level)
                    }
                } else {
                    $(cmt.html).appendTo("#comments-tree").attr("data-level", 0)
				}
                $(`.comment[data-id=${cmt.id}] pre code`).each((k,el)=>hljs.highlightBlock(el))
			}

            if (selfIdComment && $("#comment_id_" + selfIdComment).length) {
                scrollToComment(selfIdComment)
            }

			calcNewComments()

			let new_messages = $("#new_messages .new-comments")
			let pm_title = ""

			if (result.iUserCurrentCountTalkNew > 0) {
				// $(".toolbar-talk").css("display", "block")
				// $(".toolbar-talk a")[0].title = `+${result.iUserCurrentCountTalkNew}`
				// new_messages.classList.add("new-messages")
                new_messages.css("display", "")
                new_messages[0].innerText=result.iUserCurrentCountTalkNew
			} else {
                new_messages.css("display", "none")
				// new_messages.classList.remove("new-messages")
			}

			// new_messages.childNodes[0].textContent = pm_title
			// new_messages.parentNode.title = pm_title
			console.log("Ajax done", dateFormat(new Date(), "HH:MM:ss:l"))
            let countComments = $("#count-comments")
            countComments.text($(".comment").length)
		}

		let curItemBlock = Blocks.getCurrentItem("stream")

		if (Object.keys(aCmt).length > 0) {
			Blocks.load(curItemBlock, "stream")
			console.log("Load done", dateFormat(new Date(), "HH:MM:ss:l"))
		}

        Emitter.emit('ls_comments_load_after', [idTarget, typeTarget, selfIdComment, bNotFlushNew, result])

	}.bind(this))
}

// Вставка комментария
export function inject(idCommentParent, idComment, sHtml) {
	let newComment = $("<div>", {
		"class": "comment-wrapper",
		id: "comment_wrapper_id_" + idComment,
	}).html(sHtml)
	if (idCommentParent) {
		// Уровень вложенности родителя
		let commentWrapperId = $("#comment_wrapper_id_" + idCommentParent)
		let iCurrentTree = commentWrapperId.parentsUntil("#comments").length
		if (iCurrentTree === Registry.get("comment_max_tree")) {
			// Определяем id предыдушего родителя
			let prevCommentParent = commentWrapperId.parent()
			idCommentParent = parseInt(prevCommentParent.attr("id").replace("comment_wrapper_id_", ""))
		}
		commentWrapperId.append(newComment)
	} else {
		$("#comments").append(newComment)
	}
	Emitter.emit("ls_comment_inject_after", arguments, newComment)
}

// Удалить/восстановить комментарий
export function toggle(obj, commentId) {
    let oComment = $("#comment_id_" + commentId)
	let deleteReason = false
	if (!oComment.hasClass(options.classes.comment_deleted)) {
        deleteReason = prompt("Delete reason:")
        if (!deleteReason)
            return
	}

	let url = aRouter["ajax"] + "comment/delete/"
	let params = {
		idComment: commentId,
		sDeleteReason: deleteReason
	}

	Emitter.emit("toggleBefore")
	Ajax.ajax(url, params, function (result) {
		if (!result) {
			Msg.error("Error", "Please try again later")
		}
		if (result.bStateError) {
			Msg.error(null, result.sMsg)
		} else {
			Msg.notice(null, result.sMsg)

			oComment.removeClass(options.classes.comment_new + " " + options.classes.comment_deleted + " " + options.classes.comment_current)
			if (result.bState) {
				oComment.addClass(options.classes.comment_deleted)
				oComment.find('.comment-content')[0].innerHTML = `
					Комментарий удален пользователем <a href="/profile/${USERNAME}/" class="ls-user">${USERNAME}</a><br/><b>По причине:</b><br/>
					<div class="delete-reason">${deleteReason}</div><a href="#" onclick="ls.comments.showHiddenComment(${commentId}); return false;">Раскрыть комментарий</a>`
			} else {
				oComment.find('.delete-reason').remove()
				ls.comments.showHiddenComment(commentId)
			}
			Emitter.emit("ls_comments_toggle_after", [obj, commentId, result])
		}
	}.bind(this))
}

// Предпросмотр комментария
export function preview() {
	let oCommentText = $("#form_comment_text")

	if (options.wysiwyg) {
		oCommentText.val(tinyMCE.activeEditor.getContent())
	}

	if (oCommentText.val() === "")
		return

	$("#comment_preview_" + iCurrentShowFormComment).remove()
	$("#reply").before("<div id=\"comment_preview_" + iCurrentShowFormComment + "\" class=\"comment-preview text\"></div>")

	ls.tools.textPreview("form_comment_text", false, "comment_preview_" + iCurrentShowFormComment)
}

export function isCollapsed(el) {
	return el.closest(".collapsed")
}

// Устанавливает число новых комментариев
export function setCountNewComment(count) {
    let oCounter = document.getElementById("new_comments_counter")
    if (!oCounter) return

    count = $('.comment-new:visible').length
    if (count) {
        oCounter.style.display = ""
        oCounter.innerHTML = count

        document.title = `(${count}) ` + TITLE
        $("#next_new").removeClass("disabled")
    } else {
        oCounter.style.display = "none"
        $("#next_new").addClass("disabled")
        document.title = TITLE
    }
}

// Вычисляет кол-во новых комментариев
export function calcNewComments() {
	setCountNewComment()
}

// Переход к следующему комментарию
export function goToNextComment() {
	if ($("#next_new").hasClass("disabled"))
		return false
	Emitter.emit("go-to-next-comment")
	let id = $('.comment-new:visible')[0].dataset.id
	scrollToComment(id)
}

export function goToPrevComment() {
	if ($("#prev_new").hasClass("disabled"))
		return false
	Emitter.emit("go-to-prev-comment")
	scrollToComment(aCommentNewOld.splice(-2,1)[0])
	if (aCommentNewOld.length<2)
    	$("#prev_new").addClass("disabled")
}

// Прокрутка к комментарию
export function scrollToComment(id, offset, speed) {
    let oComment = $(`[data-id=${id}]`)
    let oCommentCurrent = $(".comment-current")

    if (!oComment.length) {
        return false
    }

    let body = $("html, body")
    body.stop()
    body.animate({
        scrollTop: oComment.offset().top - 250,
    }, 150)

    if (oCommentCurrent.length) {
        oCommentCurrent.removeClass("comment-current")
    }

    oComment.addClass("comment-current")
	if (oComment.hasClass("comment-new")) {
        aCommentNewOld.push(oComment.data("id"))
		if (aCommentNewOld.length>1)
			$("#prev_new").removeClass("disabled")
        oComment.removeClass("comment-new")
    }
	Emitter.emit("go-to-comment", id)
	calcNewComments()
}

// Прокрутка к родительскому комментарию
export function goToParentComment(id, pid) {
	let thisObj = this
	$("." + options.classes.comment_goto_child).hide().find("a").unbind()

	let oCommentParent = $("#comment_id_" + pid)
	oCommentParent.find("." + options.classes.comment_goto_child).show().find("a").bind("click", function () {
		$(this).parent("." + thisObj.options.classes.comment_goto_child).hide()
		thisObj.scrollToComment(id)
		return false
	}).attr("href", "#comment"+id)
	oCommentParent.data("cid", id)
	scrollToComment(pid)
	return false
}

// Сворачивание комментариев
export function checkFolding() {
	//if(!options.folding){
	//	return false
	//}
	$(".folding").each(function (index, element) {
		if ($(element).parent(".comment").next(".comment-wrapper").length === 0) {
			$(element).hide()
		} else {
			$(element).show()
		}
	}).off("click").click(function (x) {
		if (x.target.className === "folding fa fa-minus-square") {
			collapseComment(x.target)
		} else {
			expandComment(x.target)
		}
	})
	return false
}

export function expandComment(folding) {
	$(folding).removeClass("fa-plus-square").addClass("fa-minus-square").parent().nextAll(".comment-wrapper").show().removeClass("collapsed")
}

export function collapseComment(folding) {
	$(folding).removeClass("fa-minus-square").addClass("fa-plus-square").parent().nextAll(".comment-wrapper").hide().addClass("collapsed")
}

export function expandCommentAll() {
	$.each($(".folding"), function (k, v) {
		expandComment(v)
	}.bind(this))
}

export function collapseCommentAll() {
	$.each($(".folding"), function (k, v) {
		collapseComment(v)
	}.bind(this))
}

export function initShortcuts() {
    function goToPrevCommentS() {
        scrollToComment($(".comment-current").prevAll('.comment')[0].dataset.id)
    }

    function goToNextCommentS() {
        scrollToComment($(".comment-current").nextAll('.comment')[0].dataset.id)
    }

    function goToLastComment() {
        scrollToComment($('.comment')[$('.comment').length-1].dataset.id)
    }

    function goToFirstComment() {
        scrollToComment($('.comment')[0].dataset.id)
    }

    function goToNextBranch() {
    	let done = false
        $(".comment-current").nextAll('.comment').each(function(k,v){
        	console.log(v,k)
        	if (!done && parseInt(v.dataset.level)==0) {
        		done = true
                scrollToComment(v.dataset.id)
			}
		})
    }

    function goToPrevBranch() {
        let done = false
        $(".comment-current").prevAll('.comment').each(function(k,v){
            console.log(v,k)
            if (!done && parseInt(v.dataset.level)==0) {
                done = true
                scrollToComment(v.dataset.id)
            }
        })
    }

    function toggleReplyOnCurrent() {
        toggleCommentForm($(".comment-current").data("id"))
    }

    function updateComments() {
        load(window.targetId, window.targetType)
    }

    function updateCommentsSoft() {
        load(window.targetId, window.targetType, null, true)
    }

    function toggleReplyOnRoot() {
        toggleCommentForm(0)
    }

    function editCommentCurrent() {
        editComment($(".comment-current").data("id"))
    }

    function goToParent() {
        let oCommentCurrent = $(".comment-current")
        goToParentComment(oCommentCurrent.data("id"), oCommentCurrent.data("pid"))
    }

    function goToChild() {
        let oCommentCurrent = $(".comment-current")

        oCommentCurrent.find("." + options.classes.comment_goto_child).hide()
        scrollToComment(oCommentCurrent.data("cid"))
    }

    let despoilComment = function () {
        let current = $(".comment-current")
        let despoiled = current.hasClass("comment-despoiled")
        current.find(".spoiler-body").each(function (k, v) {
            if (v.style.display !== "block") {
                if (!despoiled) {
                	window.openSpoiler(v)
                }
            } else {
                if (despoiled) {
                    window.closeSpoiler(v)
                }
            }
        })
        if (despoiled) {
            $('.comment-current .spoiler-gray').removeClass("visible")
        } else {
            $('.comment-current .spoiler-gray').addClass("visible")
		}
        current.toggleClass("comment-despoiled")
    }.bind(this)

    function markAllChildAsRead() {
        let oCommentCurrent = $(".comment-current")

        let cmts = oCommentCurrent.nextAll('.comment')
        let level = parseInt(oCommentCurrent.data("level"))
		let found = false
		cmts.each(function(k,v){
			if (!found && parseInt(v.dataset.level)>level) {
				$(v).removeClass("comment-new")
			} else {
				found = true
			}
		})
        calcNewComments()
    }

    function voteUp() {
        Vote.vote($(".comment-current").data("id"), this, 1, "comment")
    }

    function voteDown() {
        Vote.vote($(".comment-current").data("id"), this, -1, "comment")
    }

    let shortcuts = {
	"ctrl+space": goToNextComment,
        "s": goToNextComment,
        "ctrl+shift+space": goToPrevComment,
	"w": goToPrevComment,
        "ctrl+up": goToPrevCommentS,
        "ctrl+down": goToNextCommentS,
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
        "alt+shift+e": editCommentCurrent,
        "alt+shift+p": goToParent,
        "alt+shift+c": goToChild,
        "alt+shift+m": markAllChildAsRead,
        "alt+shift+w": window.widemode,
        "alt+up": voteUp,
        "alt+down": voteDown,
    }

    let oFormText = $("#form_comment_text")

    for (let i in shortcuts) {
        $(document).on("keydown", null, i, function (e) {
            shortcuts[i].apply(this);
            e.preventDefault();
        }.bind(this))
        oFormText.on("keydown", null, i, function (e) {
        	if (i=="ctrl+end" || i=="ctrl+home" || i=="w" || i=="s")
        		return
            shortcuts[i].apply(this);
            e.preventDefault();
        }.bind(this))
    }

    window.foldBranch = function (id) {
        let foldings = localStorage.getItem('foldings_' + targetType + '_' + targetId) //||"".split(',') || []
        if (!foldings) {
            foldings = []
        } else {
            foldings = foldings.split(',')
        }
        foldings.push(id)
        console.log("FOLDINGS", foldings)
        localStorage.setItem('foldings_' + targetType + '_' + targetId, foldings.join(','))
        if ($("#folded_branch_" + id).length > 0) {
            $("#folded_branch_" + id).addClass("folded")
            $("#comment_id_" + id).addClass("comment-folding-start")
            calcNewComments()
            return
        }
        let comment = $(`[data-id="${id}"]`)
        let to_fold = []
        let found = false;
        window.cel = comment
        comment.nextAll('.comment').each(function(k,v){
            if (!found && parseInt(v.dataset.level) > parseInt(comment.attr("data-level"))) {
                to_fold.push(v)
				console.log("TO FOLD:",v)
            } else {
                found = true
            }
        })
        $(to_fold).wrapAll(`<div class='folding-comments'></div>`)
        $(to_fold[0]).parent().wrap(`<div class='folding folded' id="folded_branch_${id}" data-commentid='${id}'></div>`)
        $("#comment_id_" + id).addClass("comment-folding-start")
        calcNewComments()
        console.log(to_fold)

    }.bind(this)

    window.unfoldBranch = function (id) {
        let foldings = localStorage.getItem('foldings_' + targetType + '_' + targetId).split(',') || []
        foldings.pop(id)
        localStorage.setItem('foldings_' + targetType + '_' + targetId, foldings.join(','))
        console.log(id)
        $("#folded_branch_" + id).removeClass("folded")
        $("#comment_id_" + id).removeClass("comment-folding-start")
        calcNewComments()
    }.bind(this)
}

export function update_hidden() {
    let hidden = localStorage.getItem('comments_hide')
    if (hidden==null) {
        hidden = []
    } else {
        hidden = hidden.split(",")
    }
    console.log("hidden", hidden)
    for (let i=0; i<hidden.length; i++) {
        hideComment(hidden[i])
    }
}

export function init() {
	initEvent()
	initShortcuts()
	calcNewComments()
	checkFolding()
    calcNesting()
	if (location.pathname.match(/\/blog\/[a-zA-Z]+\/\d+/) || location.pathname.match(/\/talk\/read\/\d+/)) {
		update_hidden()
        updateNesting()
        toggleCommentForm(iCurrentShowFormComment)

        if (location.hash.startsWith("#comment") && location.hash !== "#comments") {
			setTimeout(scrollToComment(location.hash.replace("#comment", ""), 0, 350), 2000)
		}

        if (targetType == "topic") {
            sock.emit("listenTopic", {id: targetId})
            sock.on("reconnect", () => sock.emit("listenTopic", {id: targetId}))
        }

        Emitter.on('socket-edit-comment', (data) => updateCommentEdited(data.target_id, data.comment_extra.text))
        Emitter.on('socket-delete-comment', (data) => updateCommentDeleted(data.target_id, 1, data.comment_extra.deleteReason, data.comment_extra.deleteUserLogin))
        Emitter.on('socket-restore-comment', (data) => updateCommentDeleted(data.target_id, 0, data.comment_extra.deleteReason, data.comment_extra.deleteUserLogin))
        Emitter.on('socket-new-comment', (data) => {
            if (!document.getElementById('autoload').checked)
                return
            ls.comments.load(data.group_target_id, data.group_target_type, false, true)
        })
    }



	if (typeof(options.wysiwyg) !== "number") {
		options.wysiwyg = Boolean(BLOG_USE_TINYMCE && tinyMCE)
	}
	if ($("#form_comment_mark").length) {
        $("#form_comment_mark").on("change", (e) => localStorage.setItem('comment_use_mark', e.target.checked))
        $("#form_comment_mark")[0].checked = JSON.parse(localStorage.getItem('comment_use_mark'))
    }

	Emitter.emit('ls_comments_init_after',[],this)
}

export function updateCommentDeleted(id, deleted, reason, deleteUserLogin) {
    let cmt = $(`[data-id="${id}"]`)
    if (!cmt.length) {
        return
    }
    if (deleted) {
        cmt.addClass("comment-deleted")
        cmt.find(".comment-content").html(`
		Комментарий удален пользователем <a href="/profile/${deleteUserLogin}/" class="ls-user">${deleteUserLogin}</a><br/><b>По причине:</b><br/>
        <div class="delete-reason">
            ${reason || "Нет причины удаления"}
        </div>

            ${LOGGED_IN && (IS_ADMIN || cmt.hasClass("comment-self")) ? `<a href="#" onclick="ls.comments.showHiddenComment(${id}); return false;">Раскрыть комментарий</a>` : ""}`)
    } else {
        cmt.removeClass("comment-deleted")
        showHiddenComment(id)
    }
}

export function updateCommentEdited(id, sText) {
    let cmt = $(`[data-id="${id}"]`)
    if (!cmt.length) {
        return
    }
    cmt.find(".comment-edited").css("display", "inline-block")
    cmt.find('.comment-content').html(sText)
    cmt.find(`pre code`).each((k,el)=>hljs.highlightBlock(el))
}

export function initEvent() {
	$("#form_comment_text").bind("keyup", function (e) {
		let key = e.keyCode || e.which
		if (e.ctrlKey && (key === 13)) {
			$("#comment-button-submit").click()
			return false
		}
	})

	if (options.folding) {
		$(".folding").click(function (e) {
			if ($(e.target).hasClass("folded")) {
				expandComment(e.target)
			} else {
				collapseComment(e.target)
			}
		}.bind(this))
	}
}

export function toggleCommentForm(idComment, bNoFocus) {
	let oButtomSubmit = $("#comment-button-submit")

	if (typeof(sBStyle) !== "undefined")
		oButtomSubmit.css("display", sBStyle)
	
	if (typeof(cbsclick) !== "undefined") {
		oButtomSubmit.unbind("click")
		oButtomSubmit.attr("onclick", cbsclick)
	}

	let b = $("#comment-button-submit-edit")
	if (b.length)
		b.remove()

	b = $("#comment-button-history")
	if (b.length)
		b.remove()

	b = $("#comment-button-cancel")
	if (b.length)
		b.remove()

	_toggleCommentForm(idComment, bNoFocus)
}

export function cancelEditComment(idComment) {
	let reply = $("#reply")
	if (!reply.length) {
		return
	}

	reply.hide()
	setFormText("")
}

export function showHiddenComment(idComment) {
    idComment = parseInt(idComment)
    let hidden = localStorage.getItem('comments_hide')
    if (hidden==null) {
        hidden = []
    } else {
        hidden = hidden.split(",")
    }
    if (hidden.indexOf(idComment)) {
    	hidden.pop(idComment)
	}
    localStorage.setItem('comments_hide', hidden)
	let params = {'idComment': idComment}

	Ajax.ajax(options.type.comment.url, params, function(res) {
        let oComment = $("#comment_content_id_"+idComment)
        oComment.html(res.aComment.text).removeClass("content-hidden");
    })
}

export function hideComment(idComment) {
    let cmt = $(`[data-id="${idComment}"] .comment-content`)
	if (cmt.hasClass("content-hidden")) {
    	return
	}
	let hidden = localStorage.getItem('comments_hide')
	if (hidden==null) {
		hidden = []
	} else {
		hidden = hidden.split(",")
	}
	if (hidden.indexOf(idComment)<0)
		hidden.push(idComment)
    localStorage.setItem('comments_hide', hidden)
	cmt.addClass('content-hidden').html(`<a href="#" onclick="ls.comments.showHiddenComment(${idComment}); return false;">Раскрыть комментарий</a>`)
}

export function editComment(idComment) {
	let reply = $("#reply")
	if (!reply.length) {
		return
	}

	if (!(iCurrentShowFormComment === idComment && reply.is(":visible"))) {
		let thisObj = this
		$("#comment_content_id_" + idComment).addClass(options.classes.form_loader)
		Ajax.ajax(aRouter.ajax + "editcomment-getsource/", {
			"idComment": idComment,
		}, function (result) {
			$("#comment_content_id_" + idComment).removeClass(options.classes.form_loader)
			if (!result) {
				Msg.error("Error", "Please try again later")
				return
			}
			if (result.bStateError) {
				Msg.error(null, result.sMsg)
			} else {
				toggleCommentForm(idComment)

				let oButtomSubmit = $("#comment-button-submit")

				sBStyle = oButtomSubmit.css("display")
				let cbs = oButtomSubmit
				cbs.css("display", "none")
				cbsclick = oButtomSubmit.attr("onclick")

				oButtomSubmit.attr("onclick", "")
				oButtomSubmit.bind("click", function () {
					$("#comment-button-submit-edit").click()
					return false
				})

				cbs.after($(options.cancel_button_code))

				cbs.after($(options.edit_button_code))
				setFormText(result.sCommentSource)

				enableFormComment()
			}
		})
	} else {
		reply.hide()
	}
}

export function setFormText(sText) {
	let oFormComment = $("#form_comment_text")

	if (options.wysiwyg) {
		tinyMCE.execCommand("mceRemoveControl", false, "form_comment_text")
		oFormComment.val(sText)
		tinyMCE.execCommand("mceAddControl", true, "form_comment_text")
	} else if (typeof(oFormComment.getObject) === "function") {
		oFormComment.destroyEditor()
		oFormComment.val(sText)
		oFormComment.redactor()
	} else
		oFormComment.val(sText)
}

export function edit(formObject, targetId, targetType) {
	let oFormComment = $("#form_comment_text")

	if (options.wysiwyg) {
		$("#" + formObj + " textarea").val(tinyMCE.activeEditor.getContent())
	} else if (typeof(oFormComment.getObject) === "function") {
		$("#" + formObj + " textarea").val(oFormComment.getCode())
	}
	let formObj = $("#" + formObject)

	oFormComment.addClass(options.classes.form_loader).attr("readonly", true)
	$("#comment-button-submit").attr("disabled", "disabled")

	let lData = formObj.serializeJSON()
	let idComment = lData.reply

	Ajax.ajax(aRouter.ajax + "editcomment-edit/", lData, function (result) {
		$("#comment-button-submit").removeAttr("disabled")
		if (!result) {
			enableFormComment()
			Msg.error("Error", "Please try again later")
			return
		}
		if (result.bStateError) {
			enableFormComment()
			Msg.error(null, result.sMsg)
		} else {
			if (result.sMsg)
				Msg.notice(null, result.sMsg)

			enableFormComment()
			setFormText("")

			// Load new comments
			if (result.bEdited) {
				$("#comment_content_id_" + idComment).html(result.sCommentText)
                $(`.comment[data-id=${idComment}] pre code`).each((k,el)=>hljs.highlightBlock(el))
			}
			if (!result.bCanEditMore)
				$("#comment_id_" + idComment).find(".editcomment_editlink").remove()
			load(targetId, targetType, idComment, true)
			if (Blocks) {
				let curItemBlock = Blocks.getCurrentItem("stream")
				if (curItemBlock.data("type") === "comment") {
					Blocks.load(curItemBlock, "stream")
				}
			}

			Emitter.emit("ls_comments_edit_after", [formObj, targetId, targetType, result])
		}
	}.bind(this))
}

export function showHistory(cId) {
	let formObj = $("#form_comment")

	$("#form_comment_text").addClass(options.classes.form_loader).attr("readonly", true)
	$("#comment-button-submit-edit").attr("disabled", "disabled")

	let lData = formObj.serializeJSON()
	lData.form_comment_text = ""
	lData.reply = cId || lData.reply
	let idComment = cId || lData.reply
	Ajax.ajax(aRouter.ajax + "editcomment-gethistory/", lData, function (result) {
		$("#comment-button-submit-edit").removeAttr("disabled")
		if (!result) {
			enableFormComment()
			Msg.error("Error", "Please try again later")
			return
		}
		if (result.bStateError) {
			enableFormComment()
			Msg.error(null, result.sMsg)
		} else {
			if (result.sMsg)
				Msg.notice(null, result.sMsg)

			enableFormComment()
			$("#editcomment-history-content").html(result.sContent)
			$("#modal-editcomment-history").jqmShow()
		}
	}.bind(this))
}
