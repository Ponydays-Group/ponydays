import * as Comments from './comments'
import * as Favourite from './favourite'
import * as Vote from './vote'
import * as Msg from './msg'
import Emitter from "./emitter"

var classNames = require("classnames")
var dateFormat = require('dateformat');

export default function render_comment(data, maxNesting) {
    let level = data.level > maxNesting? maxNesting : data.level
    return data.isBad?`<section id=${"comment_id_"+data.id} data-id=${data.id} data-level=${level} data-pid=${data.parentId} data-author=${data.author.login}></section>`:`<section id=${"comment_id_"+data.id} data-author=${data.author.login} data-id=${data.id} data-level=${level} data-pid=${data.parentId} style="margin-left: ${level*20}px" class="${classNames({
        "comment": true,
        "comment-bad": data.rating < -5,
        "comment-deleted": data.isDeleted,
        "comment-self": USERNAME==data.author.login,
        "comment-new": data.isNew && USERNAME!=data.author.login
    })}">
    		<a name=${"comment"+data.id} />

    		<a href="/profile/${data.author.login}"><img src="${data.author.avatar}"" alt="avatar" class="comment-avatar" /></a>

    		<ul class="comment-info">
    			<li class="comment-author"><a href="/profile/${data.author.login}">${data.author.login}</a></li>
    		</ul>

    		<div id=${"comment_content_id_"+data.id} class="comment-content text">${data.text}</div>

    			<div class="comment-actions-wrapper"><ul class="comment-actions">
    				<li class="comment-date">
    					<a href=${"#comment"+data.id} onclick="ls.comments.scrollToComment(${data.id}); return false;" title="Ссылка на комментарий">
    						<time dateTime=${data.date}>${dateFormat(new Date(data.date), "dd.mm.yy HH:MM:ss")}</time>
    					</a>
    				</li>

    					${LOGGED_IN? `<li><a href="#" onclick="ls.comments.toggleCommentForm(${data.id}); return false;" class="reply-link">Ответить</a></li>` : "" }
                        ${LOGGED_IN && (IS_ADMIN | USERNAME==data.author.login)? `<li class="action-hidden">
                          <a href="#" class="editcomment_editlink" title="Редактировать комментарий" onclick="ls.comments.editComment(${data.id}); return false;">
                            <i class="fa fa-pencil" title="Редактировать комментарий"></i>
                          </a>
                        </li>` : ""}
                        ${LOGGED_IN && (IS_ADMIN | USERNAME==data.author.login)? `<li class="action-hidden">
                          <a href="#" class="editcomment_editlink" title="История редактирования" onclick="ls.comments.showHistory(${data.id}); return false;">
                            <i class="fa fa-history" title="История редактирования"></i>
                          </a>
                        </li>` : ""}

              ${LOGGED_IN && IS_ADMIN? `<li>
                <a onclick="ls.comments.toggle(this,${data.id}); return false;" href="#" class="comment-delete action-hidden">
                  <i class="fa fa-trash" title="Удалить/восстановить комментарий"></i>
                </a>
              </li>` : "" }

    					${(LOGGED_IN | data.countFavourite)>0? `<li class="comment-favourite action-hidden">
    						<div onclick="return ls.favourite.toggle(${data.id},this,'comment');" id=${"comment_favourite_"+data.id} class="${classNames({
                    fa: true,
                    "fa-heart-o": true,
                    "favourite": true,
                    "active": data.isFavourite
                })}" />
              <span class="favourite-count" id=${"fav_count_comment_"+data.id}>${data.countFavourite>0? " "+data.countFavourite : ""}</span>
          </li>` : ""}

    					${data.level>0? `<li class="goto-comment-parent action-hidden"><a href="#" onclick="ls.comments.goToParentComment(${data.id},${data.parentId}); return false;" title="Перейти к родительскому комментарию">↑</a></li>`:""}
                        <li style="display: none" class="goto-comment-child action-hidden"><a href="#" title="Вернуться к дочернему">↓</a></li>
            </ul><ul class="comment-actions">
    					<li id=${"vote_area_comment_"+data.id} class="${classNames({
                  vote: true,
                  "action-hidden": data.rating == 0,
                  "vote-count-positive": data.rating > 0,
                  "vote-count-negative": data.rating < 0,
                  "voted": data.voted,
                  "voted-up": data.voteDirection > 0,
                  "voted-down": data.voteDirection < 0,
              })}">
                ${LOGGED_IN? `<div class="vote-up fa fa-plus-square-o" onclick="return ls.vote.vote(${data.id},this,1,'comment');"></div>` : "" }
    						<span class="vote-count" onclick="ls.vote.getVotes(${data.id},'comment',this); return false;" id=${"vote_total_comment_"+data.id}>${data.rating > 0? "+" : ""}${data.rating}</span>
                ${LOGGED_IN? `<div class="vote-down fa fa-minus-square-o" onclick="return ls.vote.vote(${data.id},this,-1,'comment');"` : ""}
    					</li>
    			</ul></div>
    </section>`
}
