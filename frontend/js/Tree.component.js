import React from "react"

import render_comment from "./Comment.component"

import Emitter from "./emitter"

export default class Tree extends React.Component {

  state = {
    sorted_ids: this.props.ids,
    comments: this.props.comments,
    max_nesting: parseInt (($("#comments").width()-250)/20)
  }

  calcNesting() {
    this.setState({
      max_nesting: parseInt(($("#comments").width()-250)/20)
    })
  }

  componentWillMount() {

      console.log(Comment)
      console.log(Comment.render)

    $(window).on('resize', this.calcNesting.bind(this))

    let comments = this.props.comments
    let ids = this.props.ids
    let sorted_ids = this.sortTree(ids, comments)

    this.setState({sorted_ids: sorted_ids})
    window.ids = sorted_ids
    Emitter.on("comments-new-loaded", function(new_comments){
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
      this.setState({
        comments: comments,
        sorted_ids: new_sorted_ids
      })
    }.bind(this))
  }

  componentDidMount() {
    // updateImgs()
  }

  componentDidUpdate() {
    // updateImgs()
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
    return sorted_ids
  }

  render() {
    return <div>{this.state.sorted_ids.map(function(id){
      return <div dangerouslySetInnerHTML={{__html: render_comment(this.props.comments[id], this.state.max_nesting)}}/>
    }.bind(this))}</div>
  }
}
