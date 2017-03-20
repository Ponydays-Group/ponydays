var dateFormat = require('dateformat');
import render_comment from "./Comment.component"

import Emitter from "./emitter"

export default class Tree {

  state = {
    sorted_ids: [],
    comments: [],
    max_nesting: parseInt (($("#comments").width()-250)/20)
  }

  calcNesting() {
    this.setState({
      max_nesting: parseInt(($("#comments").width()-250)/20)
    })
  }

  renderNewComments(new_comments){
    let new_sorted_ids = this.state.sorted_ids
    let comments = this.state.comments

    for (let key in new_comments) {
      let cmt = new_comments[key]
      if (cmt.parentId) {
        let parent = comments[cmt.parentId]
        cmt.level = parseInt(parent.level) + 1
      }
      comments[cmt.id] = cmt
      new_sorted_ids.push(cmt.id)
    }
    new_sorted_ids = this.sortTree(new_sorted_ids, comments)
    this.state.comments = comments
    this.state.sorted_ids = new_sorted_ids
    for (let key in new_comments) {
        this.insertComment(obj, new_comments[key].id)
    }
  }

  mount(obj, comments, ids) {
    $(window).on('resize', this.calcNesting.bind(this))

    let sorted_ids = this.sortTree(ids, comments)

    this.state.sorted_ids =  sorted_ids
    this.state.comments =  comments

    this.render(obj)

    Emitter.on("comments-new-loaded", function(new_comments){
        console.log("New comments catched", dateFormat(new Date(), "HH:MM:ss"))
      let new_sorted_ids = this.state.sorted_ids
      let comments = this.state.comments

      for (let key in new_comments) {
        let cmt = new_comments[key]
        if (cmt.parentId) {
          let parent = comments[cmt.parentId]
          cmt.level = parseInt(parent.level) + 1
        }
        //console.log(key, cmt)
        comments[cmt.id] = cmt
        new_sorted_ids.push(cmt.id)
      }
      new_sorted_ids = this.sortTree(new_sorted_ids, comments)
      this.state.comments = comments
      this.state.sorted_ids = new_sorted_ids
      console.log("Before insert", dateFormat(new Date(), "HH:MM:ss"))
      for (let key in new_comments) {
          this.insertComment(obj, new_comments[key].id)
      }
      console.log("After insert", dateFormat(new Date(), "HH:MM:ss"))
    }.bind(this))
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
    obj.innerHTML = `<div>${this.state.sorted_ids.map(function(id){
      return render_comment(this.state.comments[id], this.state.max_nesting)
    }.bind(this)).join("")}</div>`
  }

  insertComment(obj, id) {
      console.log("Insert!", id, this.state.comments[id].parentId, dateFormat(new Date(), "HH:MM:ss"))
      if (parseInt(this.state.comments[id].parentId) == 0) {
          console.log("Root comment")
          $(obj).append(render_comment(this.state.comments[id], this.state.max_nesting))
          return
      }
      if ($(`[data-id=${id}]`).length != 0) {
          console.log("Comment exists")
          return
      }
      $(
          render_comment(this.state.comments[id], this.state.max_nesting)
      ).insertAfter("#comment_id_"+this.state.sorted_ids[this.state.sorted_ids.indexOf(id)-1])
      console.log("Comment inserted!", id, dateFormat(new Date(), "HH:MM:ss"))
  }
}
