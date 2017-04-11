var dateFormat = require('dateformat');
import render_comment from "./Comment.component"
import $ from "jquery"
import * as Comments from './comments'
import Emitter from "./emitter"

export default class Tree {
  
  obj = $("#comments-tree")[0]

  state = {
    sorted_ids: [],
    comments: [],
    max_nesting: parseInt (($("#comments").width()-250)/20),
    commentsNew: [],
    commentsOld: [],
    lastNewComment: 0,
  }

  calcNesting() {
    this.setState({
      max_nesting: parseInt(($("#comments").width()-250)/20)
    })
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
    console.log("finished render")
  }
  
  checkEdited(edited_comments) {
    for (let id in edited_comments) {
      if (this.state.comments[id].text != edited_comments[id].text && !$(`[data-id=${id}]`).hasClass('comment-self')) {
        $(`#comment_content_id_${id}`)[0].innerHTML = edited_comments[id].text
        this.state.comments[id].text = edited_comments[id].text
        $(`[data-id=${id}]`).addClass('comment-new')
      }
    }
    Comments.calcNewComments()
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
    function sortByTree(a,b) {
      let a_index = this.state.sorted_ids.indexOf(a)
      let b_index = this.state.sorted_ids.indexOf(b)
      if (a_index < b_index) {
        return -1
      } else {
        return 1
      }
    }
    let new_comments_ids = Object.keys(new_comments)
    new_comments_ids.sort(sortByTree.bind(this))
    if (!soft) {
      this.state.commentsNew = []
    } 
    this.state.commentsNew.push.apply(this.state.commentsNew, new_comments_ids)
    console.log(this.state.commentsNew)
    this.renderNewComments(new_comments, new_comments_ids)
    let ids = []
    ids.push.apply(ids, this.state.commentsNew)
    for (let key in ids) {
      let id = ids[key]
      let cmt = this.state.comments[id]
      if (cmt.author.login == USERNAME) {
        this.state.commentsNew.splice(this.state.commentsNew.indexOf(""+cmt.id),1)
      }
    }
    this.updateCommentsNewCount()
    if (selfIdComment && $('#comment_id_' + selfIdComment).length) {
      Comments.scrollToComment(selfIdComment);
    } 
  }
  
  updateCommentsNewCount() {
    let len = this.state.commentsNew.length
    console.log("Comments new length", len)
    $("#new_comments_counter")[0].innerText = len
    if (len==0) {
      $("#new_comments_counter").hide()
    } else if (!$("#new_comments_counter").is(':visible')) {
      $("#new_comments_counter").show()
    }
  }
  
  goToNextComment() {
    if (this.state.lastNewComment>0) {
      this.state.commentsOld.push(this.state.lastNewComment)
    }
    Comments.scrollToComment(this.state.commentsNew[0])
    this.state.lastNewComment = this.state.commentsNew.shift();
    this.updateCommentsNewCount()
  }
  
  goToPrevComment() {
    if (!this.state.commentsOld.length) {
        return
    }
    Comments.scrollToComment(this.state.commentsOld.pop())
  }
  
  goToComment(id) {
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
    if (this.state.commentsNew.indexOf(""+id)>0) {
      this.state.commentsNewt.splice(this.state.commentsNew.indexOf(""+id), 1)
    }
    this.updateCommentsNewCount()
    Comments.iCurrentViewComment = id;
  }

  mount(obj, comments, ids) {
    this.obj = obj
    $(window).on('resize', this.calcNesting.bind(this))

    let sorted_ids = this.sortTree(ids, comments)

    this.state.sorted_ids =  sorted_ids
    this.state.comments =  comments

    this.render(this.obj)
    
    $(".comment-new").each(function(k,v){
      this.state.commentsNew.push(""+$(v).data('id'))
    }.bind(this))
    
    this.updateCommentsNewCount()

    Emitter.on("comments-new-loaded", this.handleNewComments.bind(this))
    Emitter.on("comments-edited-loaded", this.checkEdited.bind(this))
    Emitter.on("go-to-next-comment", this.goToNextComment.bind(this))
    Emitter.on("go-to-prev-comment", this.goToPrevComment.bind(this))
    Emitter.on("go-to-comment", this.goToComment.bind(this))
    
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
    
    let closed = true

    let despoilComment = function() {
      $('.comment-current').find(".spoiler-body").each(function(k, v) {
          v.style.display = closed ? "none" : "block"
          window.spoiler(v)
      })
      closed = closed ? false : true
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
      }
      Comments.calcNewComments()
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
      'alt+shift+m': markAllChildAsRead.bind(this)
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
    this.obj.innerHTML = `<div>${this.state.sorted_ids.map(function(id){
      return render_comment(this.state.comments[id], this.state.max_nesting)
    }.bind(this)).join("")}</div>`
  }
}
