var classNames = require("classnames")
var dateFormat = require('dateformat');

export default class Comment {
    constructor(data) {
        this.id = data.id
        this.author = data.author
        this.date = data.date
        this.text = data.text
        this.isBad = data.isBad
        this.isDeleted = parseInt(data.isDeleted)
        this.deleteReason = data.deleteReason
        this.isFavourite = data.isFavourite
        this.countFavourite = data.countFavourite
        this.rating = data.rating
        this.voted = data.voted
        this.voteDirection = data.voteDirection
        this.targetType = data.targetType
        this.targetId = data.targetId
        this.level = parseInt(data.level)
        this.parentId = data.parentId
        this.isNew = data.isNew
        this.editCount = parseInt(data.editCount)
    }

    update_foldable(foldable) {
        let cmt = $(`#comment_id_${this.id}`)
        if (!cmt.length) {
            return false;
        }
        if (foldable) {
            cmt.addClass('comment-foldable')
        } else {
            cmt.removeClass('comment-foldable')
        }
    }

    update_edited(edited) {
        console.log("Update edited")
        if (!$(`#comment_id_${this.id}`).length) {
            return false;
        }
        let edit_el = $(`#comment_id_${this.id} .comment-edited`)
        if (edited) {
            edit_el.css('display', 'inline-block')
        } else {
            edit_el.css('display', 'none')
        }
        console.log("Updated edited")
    }

    render(folded, foldable) {
        let level = this.level > iMaxNesting? iMaxNesting : this.level
        return this.isBad?`<section id=${"comment_id_"+this.id} style="margin-left: ${level*20}px" data-id=${this.id} data-level=${this.level} data-pid=${this.parentId} data-author=${this.author.login}><span class="bad-placeholder">...</bad></section>`:`<section id=${"comment_id_"+this.id} data-author=${this.author.login} data-id=${this.id} data-level=${this.level} data-pid=${this.parentId} style="margin-left: ${level*20}px" class="${classNames({
            "comment": true,
            "comment-bad": this.rating < -5,
            "comment-self": USERNAME==this.author.login,
            "comment-new": this.isNew && USERNAME!=this.author.login,
            "comment-deleted": this.isDeleted,
            "comment-folding-start": folded,
            "comment-foldable": foldable
        })}">
    		<a name=${"comment"+this.id}></a>

    		<a href="/profile/${this.author.login}" target="_blank"><img src="${this.author.avatar}" alt="avatar" class="comment-avatar" /></a>
    		
    		<div class="fold" onclick="foldBranch('${this.id}')"><i class='material-icons'>keyboard_arrow_up</i></div>
    		<div class="unfold" onclick="unfoldBranch('${this.id}')"><i class='material-icons'>keyboard_arrow_down</i></div>
    
    
                <ul class="comment-info">
                    <li class="comment-author"><a href="/profile/${this.author.login}" target="_blank">${this.author.login}</a></li>
    		</ul>
    		
    		<div id=${"comment_content_id_"+this.id} class="comment-content text${this.isDeleted? " hided":""}">
            ${this.isDeleted? `<div class="delete-reason">${this.deleteReason||"Нет причины удаления"}</div>`:''}
    		${this.isDeleted && LOGGED_IN && (IS_ADMIN || USERNAME == this.author.login) ? `<a href="#" onclick="ls.comments.showDeletedComment(${this.id}); return false;">Раскрыть комментарий</a>` : ""}
    		${this.text}
    		</div>

    			<div class="comment-actions-wrapper">
    			    <ul class="comment-actions">
    				    <li class="comment-date">
    					    <a href=${"#comment"+this.id} onclick="ls.comments.scrollToComment(${this.id}); return false;" title="Ссылка на комментарий">
    						    <time dateTime=${this.date}>${dateFormat(new Date(this.date), "dd.mm.yy HH:MM:ss")}</time>
    					    </a>
    				    </li>
    				    <li class="comment-edited" ${this.editCount>0? `style="display: inline-block;"`:""}>(edited)</li>
    					${LOGGED_IN? `<span><a href="#" onclick="ls.comments.toggleCommentForm(${this.id}); return false;" class="reply-link">Ответить</a></span>` : "" }
    					<li class="action-hidden">
                            ${LOGGED_IN && (IS_ADMIN | USERNAME==this.author.login)? `<span>
                                <a href="#" class="editcomment_editlink" title="Редактировать комментарий" onclick="ls.comments.editComment(${this.id}); return false;">
                                    <i class="fa fa-pencil" title="Редактировать комментарий"></i>
                                </a>
                            </span>` : ""}
                            ${LOGGED_IN && (IS_ADMIN | USERNAME==this.author.login)? `<span>
                                <a href="#" class="editcomment_historylink" title="История редактирования" onclick="ls.comments.showHistory(${this.id}); return false;">
                                    <i class="fa fa-history" title="История редактирования"></i>
                                </a>
                            </span>` : ""}

                            ${LOGGED_IN && IS_ADMIN? `<span>
                                <a onclick="ls.comments.toggle(this,${this.id}); return false;" href="#" class="comment-delete">
                                    <i class="fa fa-trash" title="Удалить/восстановить комментарий"></i>
                                </a>
                            </span>` : "" }

    					    ${(LOGGED_IN | this.countFavourite)>0&&targetType!="talk"? `
                            <span class="comment-favourite">
    						    <div onclick="return ls.favourite.toggle(${this.id},this,'comment');" id=${"comment_favourite_"+this.id} class="${classNames({
            "fa": true,
            "fa-heart-o": true,
            "favourite": true,
            "active": this.isFavourite
        })}"></div>
                                <span class="favourite-count" id=${"fav_count_comment_"+this.id}>
                                    ${this.countFavourite>0? " "+this.countFavourite : ""}
                                </span>
                            </span>` : ""}
    					    
    					    <span>
                                <a onclick="ls.comments.hideComment(${this.id}); return false;" href="#" class="comment-hide">
                                    <i class="fa fa-close" title="Скрыть комментарий"></i>
                                </a>
                            </span>
         
    					    ${this.level>0? `<span class="goto-comment-parent">
                                <a href="#comment${this.parentId}" onclick="ls.comments.goToParentComment(${this.id},${this.parentId}); return false;" title="Перейти к родительскому комментарию">
                                    ↑
                                </a>
                            </span>`:""}
                        
                            <span style="display: none" class="goto-comment-child">
                                <a href="#" title="Вернуться к дочернему">
                                    ↓
                                </a>
                            </span>
                        </li>
                    </ul>
                    
                    <ul class="comment-actions">
    					<li id=${"vote_area_comment_"+this.id} class="${classNames({
            vote: true,
            "action-hidden": this.rating == 0,
            "vote-count-positive": this.rating > 0,
            "vote-count-negative": this.rating < 0,
            "voted": this.voted,
            "voted-up": this.voteDirection > 0,
            "voted-down": this.voteDirection < 0,
        })}">
                            ${LOGGED_IN? `
                            <div class="vote-up" onclick="return ls.vote.vote(${this.id},this,1,'comment');">
                                <i class="material-icons">keyboard_arrow_up</i>
                            </div>` : "" }
    					    <span class="vote-count" onclick="ls.vote.getVotes(${this.id},'comment',this); return false;" id=${"vote_total_comment_"+this.id}>
    						    ${this.rating > 0? "+" : ""}${this.rating}
    					    </span>
                            ${LOGGED_IN? `
                            <div class="vote-down" onclick="return ls.vote.vote(${this.id},this,-1,'comment');">
                                <i class="material-icons">keyboard_arrow_down</i>
                            </div>` : ""}
    					</li>
    			    </ul>
    			</div>
    </section>`
    }
}
