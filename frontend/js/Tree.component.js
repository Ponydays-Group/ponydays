import React from "react"

import Comment from "./Comment.component"

import Emitter from "./emitter"

export default class Tree extends React.Component {

  state = {
    sorted_ids: this.props.ids,
    comments: this.props.comments
  }



  componentDidMount() {
    let comments = this.props.comments
    let ids = this.props.ids
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

    this.setState({sorted_ids: sorted_ids})
    window.ids = sorted_ids
    Emitter.on("comments-new-loaded", function(new_comments){
      console.log("GOTCHA!")
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
        new_sorted_ids = this.injectComment(cmt, new_sorted_ids, comments)
      }
      this.setState({
        comments: comments,
        sorted_ids: new_sorted_ids
      })
    }.bind(this))
  }
  injectComment(new_comment, sorted_ids, comments) {
	      var search = sorted_ids.slice(sorted_ids.indexOf(new_comment.parentId) + 1, sorted_ids.length);
	      var insert_before = null;
	      var parent = comments[new_comment.parentId];
        if (sorted_ids.indexOf(new_comment.id)>0) {
          return sorted_ids
        }
	      if (parent) {
	        for (var i in search) {
	          var id = search[i];
	          var cmt = comments[id];
	          if (cmt.level <= parent.level && cmt.id != parent.id) {
	            insert_before = id;
	            break;
	          }
	        }
	      }
	      if (insert_before) {
	        sorted_ids.splice(sorted_ids.indexOf(insert_before), 0, new_comment.id);
	      } else {
	        sorted_ids.push(new_comment.id);
	      }
        console.log(insert_before, ids, search, search.length+sorted_ids.slice(0, sorted_ids.indexOf(new_comment.parentId)+1).length)
	      return sorted_ids;
      }

  _injectComment(comment, ids, comments) {
    if (!comment.parentId) {
      ids.push(comment.id)
      return ids
    }

    let parent_index = ids.indexOf(comment.parentId)
    let parent = comments[ids[parent_index]]

    let comments_search = ids.slice(parent_index+1)
    //comments_search.reverse()

    let insert_before = null
    let insert_after = null

    for (let i in comments_search) {
      let curr = comments[comments_search[i]]
      if (curr.level <= parent.level && curr != parent) {
        insert_before = ids.indexOf(curr)
      }
    }

    if (insert_before) {
      ids.splice(insert_before, 0, comment.id)
    } else {
      ids.push(comment.id)
    }
    console.log(ids, comments)
    return ids
  }

  render() {
    return <div>{this.state.sorted_ids.map(function(id){
      return <Comment key={"comment_"+id} data={this.props.comments[id]} />
    }.bind(this))}</div>
  }
}
