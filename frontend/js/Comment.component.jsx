import React from "react"

import * as Comments from './comments'
import * as Favourite from './favourite'
import * as Vote from './vote'
import * as Msg from './msg'
import Emitter from "./emitter"

var classNames = require("classnames")

export default class Comment extends React.Component {

  editComment(e) {
    e.preventDefault()
    Comments.editComment(this.props.data.id)
    return
  }

  toggleCommentForm(e) {
    e.preventDefault()
    Comments.toggleCommentForm(this.props.data.id)
    return
  }

  toggleDelete(e) {
    e.preventDefault()
    Comments.toggle({}, this.props.data.id)
    return
  }

  toggleFavourite(e) {
    if (!LOGGED_IN) {
      Msg.error("Ошибка", "Войдите, чтобы добавить в избранное")
      return
    }
    e.preventDefault()
    Favourite.toggle(this.props.data.id,this.refs["comment_id_"+this.props.data.id],'comment')
    return
  }

  goToParentComment(e) {
    e.preventDefault()
    Comments.goToParentComment(this.props.data.id, this.props.data.parentId)
    return
  }

  voteUp(e) {
    e.preventDefault()
    Vote.vote(this.props.data.id, {}, 1, "comment")
    return
  }

  voteDown(e) {
    e.preventDefault()
    Vote.vote(this.props.data.id, {}, -1, "comment")
    return
  }

  render() {
    let data = this.props.data
    let level = data.level > this.props.maxNesting? this.props.maxNesting : data.level
    console.log(data.level > this.props.maxNesting)
    return <section id={"comment_id_"+data.id} ref={"comment_id_"+data.id} data-level={level} style={{marginLeft:level*20}} className={classNames({
        "comment": true,
        "comment-bad": data.rating < -5,
        "comment-deleted": data.isDeleted,
        "comment-self": USERNAME==data.author.login,
        "comment-new": data.isNew && USERNAME!=data.author.login
      })}>
    		<a name={"comment"+data.id} />

    		<a href={"/profile/"+data.author.login}><img src={data.author.avatar} alt="avatar" className="comment-avatar" /></a>

    		<ul className="comment-info">
    			<li className="comment-author"><a href={"/profile/"+data.author.login}>{data.author.login}</a></li>
    		</ul>

    		<div id={"comment_content_id_"+data.id} className="comment-content text" dangerouslySetInnerHTML={{__html: data.text}} />

    			<ul className="comment-actions">
    				<li className="comment-date">
    					<a href={"#comment"+data.id} title="Ссылка на комментарий">
    						<time dateTime={data.date}>{data.date}</time>
    					</a>
    				</li>

    					{LOGGED_IN? <li><a href="#" onClick={this.toggleCommentForm.bind(this)} className="reply-link">Ответить</a></li> : "" }
              {LOGGED_IN && (IS_ADMIN | USERNAME==data.author.login)? <li className="action-hidden">
                <a href="#" className="editcomment_editlink" title="Редактировать комментарий" onClick={this.editComment.bind(this)}>
                  <i className="fa fa-pencil" title="Редактировать комментарий" />
                </a>
              </li> : ""}

              {LOGGED_IN && IS_ADMIN? <li>
                <a href="#" className="comment-delete action-hidden" onClick={this.toggleDelete.bind(this)}>
                  <i className="fa fa-trash" title="Удалить/восстановить комментарий" />
                </a>
              </li> : "" }

    					{(LOGGED_IN | data.favouriteCount)>0? <li className="comment-favourite action-hidden">
    						<div onClick={this.toggleFavourite.bind(this)} className={classNames({
                    fa: true,
                    "fa-heart-o": true,
                    "favourite": true,
                    "active": data.isFavourite
                  })} />
                <span className="favourite-count" id={"fav_count_comment_"+data.id}>{data.favouriteCount? data.favouriteCount : ""}</span>
    					</li> : ""}

    					{data.level>0? <li className="goto-comment-parent action-hidden"><a href="#" onClick={this.goToParentComment.bind(this)} title="Перейти к родительскому комментарию">↑</a></li>:""}

    					<li id={"vote_area_comment_"+data.id} className={classNames({
                  vote: true,
                  "action-hidden": data.rating == 0,
                  "vote-count-positive": data.rating > 0,
                  "vote-count-negative": data.rating < 0,
                  "voted": data.voted,
                  "voted-up": data.voteDirection > 0,
                  "voted-down": data.voteDirection < 0,
                })}>
                {LOGGED_IN? <div className="vote-up fa fa-plus-square-o" onClick={this.voteUp.bind(this)}></div> : "" }
    						<span className="vote-count" id={"vote_total_comment_"+data.id}>{data.rating > 0? "+" : ""}{data.rating}</span>
                {LOGGED_IN? <div className="vote-down fa fa-minus-square-o" onClick={this.voteDown.bind(this)}></div> : ""}
    					</li>
    			</ul>
    </section>
  }
}
