import $ from "jquery"

import * as Registry from "./registry"
import * as Blocks from "./blocks"
import Emitter from "./emitter"
import * as Msg from "./msg"
import * as Ajax from "./ajax"

import Tree from "./Tree.component"

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
export let bSuccessLoaded = true
// export let bStopAutoload = false
export let aCommentNew = []
export let aCommentOld = []
export let lastNewComment = 0

export async function loadComments() {
	let url = ""

	if ((DIR_WEB_ROOT + location.pathname).startsWith(aRouter["talk"])) {
		url = location.href.replace("read", "readcomments")
	} else {
		url = window.location.pathname + "/comments"
	}

	return await Ajax.asyncAjax(url, {})
}

export async function renderComments() {
	let CommentsTree = new Tree()

	let result = await loadComments()
	let comments = result.aComments

	lastNewComment = result.iMaxIdComment

	CommentsTree.mount($("#comments-tree"), comments)

	if (location.hash.startsWith("#comment") && location.hash !== "#comments") {
		setTimeout(scrollToComment(location.hash.replace("#comment", ""), 0, 350), 2000)
	}

	console.log("finish:", dateFormat(new Date(), "HH:MM:ss:l"))
}

// Добавляет комментарий
export function add(formObj, targetId, targetType) {
	if (options.wysiwyg) {
		$("#" + formObj + " textarea").val(tinyMCE.activeEditor.getContent())
	}

	formObj = $("#" + formObj)

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
	}.bind(this))
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
	reply.css("marginLeft", (comment.data("level") + 1) * 20)
	if (!comment) {
		reply.css("marginLeft", 0)
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
	bSuccessLoaded = false;

	if (aCommentNew !== []) {
		aCommentOld = aCommentNew
	}

	// Удаляем подсветку у комментариев
	if (!bNotFlushNew) {
		$(".comment").each(function (index, item) {
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
		bSuccessLoaded = true;

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
				console.log("after", lastNewComment)

				let countComments = $("#count-comments")
				countComments.text(parseInt(countComments.text()) + Object.keys(aCmt).length)
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

			let new_messages = document.getElementById("new_messages")
			let pm_title = ""

			if (result.iUserCurrentCountTalkNew > 0) {
				$(".toolbar-talk").css("display", "block")
				$(".toolbar-talk a")[0].title = `+${result.iUserCurrentCountTalkNew}`
				new_messages.classList.add("new-messages")
				pm_title = " (" + result.iUserCurrentCountTalkNew.toString() + ")"
			} else {
				$(".toolbar-talk").css("display", "none")
				new_messages.classList.remove("new-messages")
			}

			new_messages.childNodes[0].textContent = pm_title
			new_messages.parentNode.title = pm_title
			console.log("Ajax done", dateFormat(new Date(), "HH:MM:ss:l"))
		}

		let curItemBlock = Blocks.getCurrentItem("stream")

		if (Object.keys(aCmt).length > 0) {
			Blocks.load(curItemBlock, "stream")
			console.log("Load done", dateFormat(new Date(), "HH:MM:ss:l"))
		}
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
	let url = aRouter["ajax"] + "comment/delete/"
	let params = {
		idComment: commentId,
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

			let oCommentId = $("#comment_id_" + commentId)
			oCommentId.removeClass(options.classes.comment_self + " " + options.classes.comment_new + " " + options.classes.comment_deleted + " " + options.classes.comment_current)
			if (result.bState) {
				oCommentId.addClass(options.classes.comment_deleted)
			}
			$(obj).text(result.sTextToggle)
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
	// // TODO that will work good only if there are no any other title modificators!
	// if (!options.pageTitle)
	//     options.pageTitle = document.title
	// if (count > 0) {
	//     $('#new_comments_counter').show().text(count)
	//     if (document.getElementById('autoload').checked) {
	//         document.title = '(' + count + ') ' + options.pageTitle
	//     }
	// } else {
	//     $('#new_comments_counter').text(0).hide()
	//     document.title = options.pageTitle
}

// Вычисляет кол-во новых комментариев
export function calcNewComments() {
	let aCommentsNew = $("." + options.classes.comment + "." + options.classes.comment_new)
	$.each(aCommentsNew, function (k, v) {
		//console.log(isCollapsed(v))
	}.bind(this))

	let count = aCommentsNew.length
	$.each(aCommentsNew, function (k, v) {
		if (!isCollapsed(v)) {
			aCommentNew.push(parseInt($(v).attr("id").replace("comment_id_", "")))
		} else {
			count--
		}
	}.bind(this))
	setCountNewComment(count)
}

// Переход к следующему комментарию
export function goToNextComment() {
	Emitter.emit("go-to-next-comment")
	// if (lastNewComment>0) {
	//     aCommentOld.push(lastNewComment)
	// }
	// if (aCommentNew[0]) {
	//     if ($('#comment_id_' + aCommentNew[0]).length) {
	//         scrollToComment(aCommentNew[0])
	//         $('#comment_id_' + aCommentNew[0]).removeClass(options.classes.comment_new)
	//     }
	//     lastNewComment = aCommentNew.shift()
	// }
	// setCountNewComment(aCommentNew.length)
}

export function goToPrevComment() {
	Emitter.emit("go-to-prev-comment")
}

// Прокрутка к комментарию
export function scrollToComment(idComment, offset, speed) {
	// offset = offset || 250
	// speed = speed || 150
	// $('html, body').animate({
	//     scrollTop: $('#comment_id_' + idComment).offset().top - offset
	// }, speed)

	// if (iCurrentViewComment) {
	//     $('#comment_id_' + iCurrentViewComment).removeClass(options.classes.comment_current)
	//     $('#comment_id_' + iCurrentViewComment).removeClass(options.classes.comment_new)
	// }
	// $('#comment_id_' + idComment).addClass(options.classes.comment_current)
	// iCurrentViewComment = idComment
	Emitter.emit("go-to-comment", idComment)
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
	})
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

export function init() {
	initEvent()
	calcNewComments()
	checkFolding()
	toggleCommentForm(iCurrentShowFormComment)

	if (typeof(options.wysiwyg) !== "number") {
		options.wysiwyg = Boolean(BLOG_USE_TINYMCE && tinyMCE)
	}
	//Emitter.emit('ls_comments_init_after',[],this)
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

export function editComment(idComment) {
	let reply = $("#reply")
	if (!reply.length) {
		return
	}

	if (!(iCurrentShowFormComment === idComment && reply.is(":visible"))) {
		let thisObj = this
		$("#comment_content_id_" + idComment).addClass(thisObj.options.classes.form_loader)
		Ajax.ajax(aRouter.ajax + "editcomment-getsource/", {
			"idComment": idComment,
		}, function (result) {
			$("#comment_content_id_" + idComment).removeClass(thisObj.options.classes.form_loader)
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

				cbs.after($(thisObj.options.cancel_button_code))

				cbs.after($(thisObj.options.edit_button_code))
				ls.comments.setFormText(result.sCommentSource)

				thisObj.enableFormComment()
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

// export function init_editcomment = function () {
//        toggleCommentForm = that.superior("toggleCommentForm")
//        ls.comments.toggleCommentForm = mytoggleCommentForm
//    }
