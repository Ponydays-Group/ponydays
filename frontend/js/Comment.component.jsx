import * as Comments from './comments'
import * as Favourite from './favourite'
import * as Vote from './vote'
import * as Msg from './msg'
import Emitter from "./emitter"

var classNames = require("classnames")
var dateFormat = require('dateformat');

export default function render_comment(data, maxNesting) {
    let level = data.level > maxNesting? maxNesting : data.level
    return data.isBad?`<section id=${"comment_id_"+data.id} style="margin-left: ${level*20}px" data-id=${data.id} data-level=${data.level} data-pid=${data.parentId} data-author=${data.author.login}><span class="bad-placeholder">...</bad></section>`:`<section id=${"comment_id_"+data.id} data-author=${data.author.login} data-id=${data.id} data-level=${data.level} data-pid=${data.parentId} style="margin-left: ${level*20}px" class="${classNames({
        "comment": true,
        "comment-bad": data.rating < -5,
        "comment-self": USERNAME==data.author.login,
        "comment-new": data.isNew && USERNAME!=data.author.login,
        "comment-deleted": data.isDeleted,
    })}">
    		<a name=${"comment"+data.id}></a>

    		<a href="/profile/${data.author.login}" target="_blank"><img src="${data.author.avatar}"" alt="avatar" class="comment-avatar" /></a>

    		<ul class="comment-info">
    			<li class="comment-author"><a href="/profile/${data.author.login}" target="_blank">${data.author.login}</a></li>
    		</ul>
    		
    		<div id=${"comment_content_id_"+data.id} class="comment-content text">
    		${data.isDeleted && LOGGED_IN && (IS_ADMIN | USERNAME == data.author.login) ? `<span onclick="window.openSpoiler(children[0]); children[1].style.display='none' ">
                <span class="spoiler-body" style="display: none; padding: 0; margin: unset;">` : ""}
    		    ${data.text}
    		${data.isDeleted && LOGGED_IN && (IS_ADMIN | USERNAME == data.author.login) ? `</span><a href="#" onclick="return false">Раскрыть комментарий</a></span>` : ""}
    		</div>

    			<div class="comment-actions-wrapper"><ul class="comment-actions">
    				<li class="comment-date">
    					<a href=${"#comment"+data.id} onclick="ls.comments.scrollToComment(${data.id}); return false;" title="Ссылка на комментарий">
    						<time dateTime=${data.date}>${dateFormat(new Date(data.date), "dd.mm.yy HH:MM:ss")}</time>
    					</a>
    				</li>
    					${LOGGED_IN? `<span><a href="#" onclick="ls.comments.toggleCommentForm(${data.id}); return false;" class="reply-link">Ответить</a></span>` : "" }
    					<li class="action-hidden">
                        ${LOGGED_IN && (IS_ADMIN | USERNAME==data.author.login)? `<span>
                          <a href="#" class="editcomment_editlink" title="Редактировать комментарий" onclick="ls.comments.editComment(${data.id}); return false;">
                            <i class="fa fa-pencil" title="Редактировать комментарий"></i>
                          </a>
                        </span>` : ""}
                        ${LOGGED_IN && (IS_ADMIN | USERNAME==data.author.login)? `<span>
                          <a href="#" class="editcomment_historylink" title="История редактирования" onclick="ls.comments.showHistory(${data.id}); return false;">
                            <i class="fa fa-history" title="История редактирования"></i>
                          </a>
                        </span>` : ""}

              ${LOGGED_IN && IS_ADMIN? `<span>
                <a onclick="ls.comments.toggle(this,${data.id}); return false;" href="#" class="comment-delete">
                  <i class="fa fa-trash" title="Удалить/восстановить комментарий"></i>
                </a>
              </span>` : "" }

    					${(LOGGED_IN | data.countFavourite)>0&&targetType!="talk"? `<span class="comment-favourite">
    						<div onclick="return ls.favourite.toggle(${data.id},this,'comment');" id=${"comment_favourite_"+data.id} class="${classNames({
                    fa: true,
                    "fa-heart-o": true,
                    "favourite": true,
                    "active": data.isFavourite
                })}" />
              <span class="favourite-count" id=${"fav_count_comment_"+data.id}>${data.countFavourite>0? " "+data.countFavourite : ""}</span>
          </span></div></span>` : ""}
         
    					${data.level>0? `<span class="goto-comment-parent"><a href="#" onclick="ls.comments.goToParentComment(${data.id},${data.parentId}); return false;" title="Перейти к родительскому комментарию">↑</a></span>`:""}
                        <span style="display: none" class="goto-comment-child"><a href="#" title="Вернуться к дочернему">↓</a></span></li>
            </ul><ul class="comment-actions">
    					<span id=${"vote_area_comment_"+data.id} class="${classNames({
                  vote: true,
                  "action-hidden": data.rating == 0,
                  "vote-count-positive": data.rating > 0,
                  "vote-count-negative": data.rating < 0,
                  "voted": data.voted,
                  "voted-up": data.voteDirection > 0,
                  "voted-down": data.voteDirection < 0,
              })}">
                ${LOGGED_IN? `<div class="vote-up" onclick="return ls.vote.vote(${data.id},this,1,'comment');"><i class="material-icons">keyboard_arrow_up</i></div>` : "" }
    						<span class="vote-count" onclick="ls.vote.getVotes(${data.id},'comment',this); return false;" id=${"vote_total_comment_"+data.id}>${data.rating > 0? "+" : ""}${data.rating}</span>
                ${LOGGED_IN? `<div class="vote-down" onclick="return ls.vote.vote(${data.id},this,-1,'comment');"><i class="material-icons">keyboard_arrow_down</i></div>` : ""}
    					</li>
    			</ul></div>
    </section>`
}
