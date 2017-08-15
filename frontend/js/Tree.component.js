var dateFormat = require('dateformat');
import render_comment from "./Comment.component"
import $ from "jquery"
import * as Comments from './comments'
import * as Vote from './vote'
import Emitter from "./emitter"
import {updateImgs} from './template.js'

export default class Tree {

  obj = $("#comments-tree")[0]

  state = {
    sorted_ids: [],
    comments: [],
    max_nesting: 0,
    commentsNew: [],
    commentsOld: [],
    lastNewComment: 0,
  }

  calcNesting() {
    let minWidth = parseInt(localStorage.getItem("min_comment_width"))
    if (!minWidth) {
      localStorage.setItem("min_comment_width", 250)
      minWidth = 250
    }
    this.state.max_nesting = parseInt(($("#comments").width()-minWidth)/20)
  }

  renderNewComments(new_comments, new_comments_ids){
    console.log("start render")
    for (let key in new_comments_ids) {
      let cmt = new_comments[new_comments_ids[key]]
      if ($(`[data-id=${cmt.id}]`).length == 0) {
        let cmt_html = render_comment(cmt, this.state.max_nesting)
        if ($(`[data-id=${this.state.sorted_ids[this.state.sorted_ids.indexOf(cmt.id)-1]}]`).length != 1) {
          console.info("No parent comment in DOM!", cmt, this.state.sorted_ids)
          $(this.obj).append($(cmt_html))
        }
        $(cmt_html).insertAfter(`[data-id=${this.state.sorted_ids[this.state.sorted_ids.indexOf(cmt.id)-1]}]`)
        if ($(`[data-id=${cmt.id}]`).length != 1) {
          console.error("No inserted comment in DOM!", cmt, this.state.sorted_ids)
        }
      }
    }
    updateImgs()
    console.log("finished render")
  }

  checkEdited(edited_comments) {
    for (let id in edited_comments) {
      if (this.state.comments[id].text != edited_comments[id].text && !$(`[data-id=${id}]`).hasClass('comment-self')) {
        $(`#comment_content_id_${id}`)[0].innerHTML = edited_comments[id].text
        this.state.comments[id].text = edited_comments[id].text
        $(`[data-id=${id}]`).addClass('comment-new')
        this.state.commentsNew.push(id)
        this.state.commentsNew.sort(this.sortByTree.bind(this))
        this.updateCommentsNewCount()
      }
    }
    updateImgs()
    Comments.calcNewComments()
  }

  sortByTree(a,b) {
    let a_index = this.state.sorted_ids.indexOf(a)
    let b_index = this.state.sorted_ids.indexOf(b)
    if (a_index < b_index) {
      return -1
    } else {
      return 1
    }
  }

  handleNewComments(new_comments,selfIdComment,soft) {
    let new_sorted_ids = this.state.sorted_ids
    let comments = this.state.comments

    for (let key in new_comments) {
      let cmt = new_comments[key]
      if (cmt.parentId) {
        let parent = comments[cmt.parentId]
        cmt.level = parseInt(parent.level) + 1
      }
      else {
        cmt.level = 0
      }
      comments[cmt.id] = cmt
      new_sorted_ids.push(cmt.id)
    }
    new_sorted_ids = this.sortTree(new_sorted_ids, comments)
    this.state.comments = comments
    this.state.sorted_ids = new_sorted_ids

    let new_comments_ids = Object.keys(new_comments)
    new_comments_ids.sort(this.sortByTree.bind(this))
    if (!soft) {
      this.state.commentsNew = []
    }
    this.state.commentsNew.push.apply(this.state.commentsNew, new_comments_ids)
    this.state.commentsNew.sort(this.sortByTree.bind(this))
    this.renderNewComments(new_comments, new_comments_ids)
    let ids = []
    ids.push.apply(ids, this.state.commentsNew)
    for (let key in ids) {
      let id = ids[key]
      let cmt = this.state.comments[id]
      if (cmt.author.login == USERNAME || cmt.isBad) {
        this.state.commentsNew.splice(this.state.commentsNew.indexOf(""+cmt.id),1)
      }
    }
    this.updateCommentsNewCount()
    if (selfIdComment && $('#comment_id_' + selfIdComment).length) {
      Comments.scrollToComment(selfIdComment);
    }
  }

  updateCommentsNewCount() {
    if (!$("#new_comments_counter").length) {
      return
    }
    let clear = []
    for (let i=0; i<this.state.commentsNew.length; i++) {
      let id = this.state.commentsNew[i]
      if (clear.indexOf(id)>=0) {
        continue
      }
      clear.push(id)
    }
    this.state.commentsNew = clear
    let len = this.state.commentsNew.length
    $("#new_comments_counter")[0].innerText = len
    if (len==0) {
      $("#new_comments_counter").hide()
      document.title = $("title").data("title")
    } else if (!$("#new_comments_counter").is(':visible')) {
      $("#new_comments_counter").show()
      document.title = `(${len}) `+TITLE
    } else {
      document.title = `(${len}) `+TITLE
    }
  }

  goToNextComment() {
    console.log(this.state.commentsNew)
    if (this.state.lastNewComment>0) {
      this.state.commentsOld.push(this.state.lastNewComment)
    }
    let id = this.state.commentsNew[0]
    this.state.comments[id].isNew = false
    Comments.scrollToComment(id)
    this.state.lastNewComment = id;
    this.updateCommentsNewCount()
  }

  goToPrevComment() {
    if (!this.state.commentsOld.length) {
        return
    }
    Comments.scrollToComment(this.state.commentsOld.pop())
  }

  goToComment(id) {
    if (this.state.comments[id].isBad) {
      return
    }
    if (!$(`[data-id=${id}]`).length) {
      return false
    }
    $('html, body').animate({
        scrollTop: $(`[data-id=${id}]`).offset().top - 250
    }, 150);
    if ($('.comment-current').length){
      $('.comment-current').removeClass('comment-current')
    }
    $(`[data-id=${id}]`).addClass('comment-current')
    $(`[data-id=${id}]`).removeClass('comment-new')
    if (this.state.commentsNew.indexOf(""+id)>(-1)) {
      this.state.commentsNew.splice(this.state.commentsNew.indexOf(""+id), 1)
    }
    this.updateCommentsNewCount()
    Comments.iCurrentViewComment = id;
  }

  mount(obj, comments, ids) {
    function updateNesting(){this.calcNesting();this.render()}
    this.obj = obj
    //$(window).on('resize', updateNesting.bind(this))

    let sorted_ids = this.sortTree(ids, comments)

    this.state.sorted_ids =  sorted_ids
    this.state.comments =  comments

    updateNesting.bind(this)()

    $(".comment-new").each(function(k,v){
      this.state.commentsNew.push(""+$(v).data('id'))
    }.bind(this))

    this.updateCommentsNewCount()

    Emitter.on("comments-new-loaded", this.handleNewComments.bind(this))
    Emitter.on("comments-edited-loaded", this.checkEdited.bind(this))
    Emitter.on("go-to-next-comment", this.goToNextComment.bind(this))
    Emitter.on("go-to-prev-comment", this.goToPrevComment.bind(this))
    Emitter.on("go-to-comment", this.goToComment.bind(this))
    Emitter.on("comments-calc-nesting", updateNesting.bind(this))

    this.initShortcuts()
  }

  initShortcuts() {
    function goToPrevComment(){
      Comments.scrollToComment(this.state.sorted_ids[this.state.sorted_ids.indexOf(""+$('.comment-current').data('id'))-1])
    }
    function goToNextComment(){
      Comments.scrollToComment(this.state.sorted_ids[this.state.sorted_ids.indexOf(""+$('.comment-current').data('id'))+1])
    }

    function goToLastComment(){
      Comments.scrollToComment(this.state.sorted_ids[this.state.sorted_ids.length-1])
    }

    function goToFirstComment(){
      Comments.scrollToComment(this.state.sorted_ids[0])
    }

    function goToNextBranch(){
      let cur_id = $('.comment-current').data("id")
      let cur_cmt = this.state.comments[cur_id]
      let data = this.state.sorted_ids.slice(this.state.sorted_ids.indexOf(""+cur_id)+1)
      let prev_branch = this.state.sorted_ids[0]
      for (let key in data) {
        let id = data[key]
        let cmt = this.state.comments[id]
        if (cmt.level == 0) {
          prev_branch = cmt.id
          break
        }
      }
      Comments.scrollToComment(prev_branch)
    }

    function goToPrevBranch(){
      let cur_id = $('.comment-current').data("id")
      let cur_cmt = this.state.comments[cur_id]
      let data = this.state.sorted_ids.slice(0, this.state.sorted_ids.indexOf(""+cur_id)).reverse()
      let prev_branch = this.state.sorted_ids[0]
      for (let key in data) {
        let id = data[key]
        let cmt = this.state.comments[id]
        if (cmt.level == 0) {
          prev_branch = cmt.id
          break
        }
      }
      Comments.scrollToComment(prev_branch)
    }

    function toggleReplyOnCurrent(){
      Comments.toggleCommentForm($('.comment-current').data("id"))
    }

    function updateComments(){
      Comments.load(window.targetId, window.targetType)
    }

    function updateCommentsSoft(){
      Comments.load(window.targetId, window.targetType, null, true)
    }

    function toggleReplyOnRoot() {
      Comments.toggleCommentForm(0)
    }

    function editComment() {
      Comments.editComment($('.comment-current').data('id'))
    }

    function goToParent() {
      Comments.goToParentComment($('.comment-current').data('id'),$('.comment-current').data('pid'))
    }

    function goToChild() {
      $('.comment-current').find('.' + Comments.options.classes.comment_goto_child).hide()
      Comments.scrollToComment($('.comment-current').data('cid'))
    }

    let despoilComment = function() {
      $('.comment-current').find(".spoiler-body").each(function(k, v) {
        window.spoiler(v)
      })
    }.bind(this)

    function markAllChildAsRead() {
      let ids = this.state.sorted_ids.slice(this.state.sorted_ids.indexOf(""+$('.comment-current').data("id"))+1)
      let level = $('.comment-current').data("level")
      for (let i in ids) {
        let id = ids[i]
        let cmt = this.state.comments[id]
        if (cmt.level <= level) {
          break
        }
        $(`[data-id=${id}]`).removeClass('comment-new')
        this.state.commentsNew.splice(this.state.commentsNew.indexOf(""+id), 1)
      }
      this.updateCommentsNewCount()
    }

    function voteUp() {
      Vote.vote($('.comment-current').data('id'), this, 1, 'comment')
    }

    function voteDown() {
      Vote.vote($('.comment-current').data('id'), this, -1, 'comment')
    }

    let shortcuts = {
      'ctrl+space': Comments.goToNextComment,
      'ctrl+shift+space': Comments.goToPrevComment,
      'ctrl+up': goToPrevComment.bind(this),
      'ctrl+down': goToNextComment.bind(this),
      'ctrl+end': goToLastComment.bind(this),
      'ctrl+home': goToFirstComment.bind(this),
      'alt+pagedown': goToNextBranch.bind(this),
      'alt+pageup': goToPrevBranch.bind(this),
      'alt+r': toggleReplyOnCurrent.bind(this),
      'alt+u': updateComments,
      'alt+shift+u': updateCommentsSoft,
      'alt+shift+d': window.despoil,
      'alt+shift+s': despoilComment,
      'alt+n': toggleReplyOnRoot,
      'alt+shift+e': editComment,
      'alt+shift+p': goToParent,
      'alt+shift+c': goToChild,
      'alt+shift+m': markAllChildAsRead.bind(this),
      'alt+shift+w': window.widemode,
      'alt+up': voteUp.bind(this),
      'alt+down': voteDown.bind(this),
    }

    for (let i in shortcuts) {
      $(document).on('keydown', null, i, shortcuts[i])
      $('#form_comment_text').on('keydown', null, i, shortcuts[i])
    }
    $('#form_comment_text').off('keydown', shortcuts['ctrl+end'])
    $('#form_comment_text').off('keydown', shortcuts['ctrl+home'])
  }


  sortTree(r_ids, comments) {
    let ids = []
    for (let i in r_ids) {
      if (ids.indexOf(r_ids[i])>=0) {
        continue
      }
      ids.push(r_ids[i])
    }
    let sorted_ids = []
    for (let i in ids) {
      if (!comments[ids[i]].parentId) {
        sorted_ids.push(ids[i])
      }
    }

    for (let i=0; i<sorted_ids.length; i++) {
      let id = sorted_ids[i]

      let data = ids.slice(ids.indexOf(id)+1).reverse()
      for (let y in data) {
        if (comments[data[y]].parentId == id) {
          sorted_ids.splice(i+1, 0, data[y])
        }
      }
    }
    window.ids = sorted_ids
    return sorted_ids
  }

  render(obj) {
    if (!this.obj) {
      this.obj = $('#comments-tree')
    }

    this.obj.innerHTML = `<div>${this.state.sorted_ids.map(function(id){
      return render_comment(this.state.comments[id], this.state.max_nesting)
    }.bind(this)).join("")}</div>`
    updateImgs()
  }
}
