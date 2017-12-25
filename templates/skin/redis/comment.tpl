{assign var="oUser" value=$oComment->getUser()}
{assign var="bCanEdit" value=$LS->ACL_UserCanEditComment($oUserCurrent, $oComment,1)}
{assign var="bCanDelete" value=$LS->ACL_UserCanDeleteComment($oUserCurrent, $oComment,1)}
{if $oComment->isBad()}
<section
		id="comment_id_{$oComment->getId()}"
		data-author="{$oComment->getUser()->getLogin()}"
		data-id="{$oComment->getId()}"
		data-level="{$cmtlevel}"
		data-pid="{$oComment->getPid()}"
		style="margin-left: {$cmtlevel*20}px"
		class="bad-placeholder">
	<span>...</span>
</section>
{else}
<section
		id="comment_id_{$oComment->getId()}"
		data-author="{$oComment->getUser()->getLogin()}"
		data-id="{$oComment->getId()}"
		data-level="{$cmtlevel}"
		data-pid="{$oComment->getPid()}"
		style="margin-left: {$cmtlevel*20}px"
		class="comment
			{if $sDateReadLast <= $oComment->getDate() && !($oUserCurrent and $oComment->getUserId() == $oUserCurrent->getId())}comment-new{/if}
			{if $oUserCurrent && $oComment->getUserId()==$oUserCurrent->getId()}comment-self{/if}
			{if $oComment->getDelete()}comment-deleted{/if}">

	<a name="comment{$oComment->getId()}"></a>

	<a href="{$oUser->getUserWebPath()}" target="_blank"><img src="{$oUser->getProfileAvatarPath(48)}" alt="avatar" class="comment-avatar" /></a>

	<div class="fold" onclick="foldBranch('{$oComment->getId()}')"><i class='material-icons'>keyboard_arrow_up</i></div>
	<div class="unfold" onclick="unfoldBranch('{$oComment->getId()}')"><i class='material-icons'>keyboard_arrow_down</i></div>


	<ul class="comment-info">
		<li class="comment-author"><a href="{$oUser->getUserWebPath()}" target="_blank">{$oUser->getLogin()}</a></li>

        {*{if $oUser->getRank()}<li class="author-rank">{$oUser->getRank()}</li>{/if}*}

	</ul>

	<div id="comment_content_id_{$oComment->getId()}" class="comment-content text {if $oComment->getDelete()}hided{/if}">
		{if $oComment->getDelete()}<div class="delete-reason">{if $oComment->getDeleteReason()}{$oComment->getDeleteReason()}{else}Нет причины удаления{/if}</div>{/if}
		{if $oUserCurrent && ($oComment->getDelete() && ($bCanDelete || $oUserCurrent->getId()==$oUser->getId()))}
			<a href="#" onclick="ls.comments.showHiddenComment({$oComment->getId()}); return false;">Раскрыть комментарий</a>
		{/if}
		{if !$oComment->getDelete()}
			{$oComment->getText()}
		{/if}
	</div>

	<div class="comment-actions-wrapper">
		<ul class="comment-actions">
			<li class="comment-date">
				<a href="#comment{$oComment->getId()}" onclick="ls.comments.scrollToComment({$oComment->getId()}); return false;" title="Ссылка на комментарий">
					<time dateTime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() format="j F Y, H:i"}</time>
				</a>
			</li>
			<li class="comment-edited" {if $oComment->getEditCount()}style="display: inline-block;"{/if}>(edited)</li>
			{if $oUserCurrent}<span><a href="#" onclick="ls.comments.toggleCommentForm({$oComment->getId()}); return false;" class="reply-link">Ответить</a></span>{/if}






			<li class="action-hidden">
                {if $bCanEdit}
					<span>
                		<a href="#" class="editcomment_editlink" title="Редактировать комментарий" onclick="ls.comments.editComment({$oComment->getId()}); return false;">
                			<i class="fa fa-pencil" title="Редактировать комментарий"></i>
                		</a>
                	</span>
					<span>
                		<a href="#" class="editcomment_historylink" title="История редактирования" onclick="ls.comments.showHistory({$oComment->getId()}); return false;">
                			<i class="fa fa-history" title="История редактирования"></i>
                		</a>
                	</span>
				{/if}

                {if $bCanDelete}
					<span>
                		<a onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;" href="#" class="comment-delete">
			                <i class="fa fa-trash" title="Удалить/восстановить комментарий"></i>
            		    </a>
                	</span>
				{/if}

				{if $oUserCurrent && $oComment->getTargetType()!="talk"}
                	<span class="comment-favourite">
                		<div onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');" id="comment_favourite_{$oComment->getId()}" class="fa fa-heart-o favourite {if $oComment->getIsFavourite()}active{/if}"></div>
						<span class="favourite-count" id="fav_count_comment_"{$oComment->getId()}>{if $oComment->getCountFavourite()}{$oComment->getCountFavourite()}{/if}</span>
                	</span>
				{/if}

				<span>
					<a onclick="ls.comments.hideComment({$oComment->getId()}); return false;" href="#" class="comment-hide">
						<i class="fa fa-close" title="Скрыть комментарий"></i>
                    </a>
				</span>

				{if $oComment->getPid()}
					<span class="goto-comment-parent">
                		<a href="#comment{$oComment->getPid()}" onclick="ls.comments.goToParentComment({$oComment->getId()},{$oComment->getPid()}); return false;" title="Перейти к родительскому комментарию">
                			↑
		                </a>
        	        </span>
				{/if}

				<span style="display: none" class="goto-comment-child">
					<a href="#" title="Вернуться к дочернему">
						↓
					</a>
				</span>
			</li>
		</ul>

		<ul class="comment-actions">
			<li id="vote_area_comment_{$oComment->getId()}" class="vote {if $oComment->getRating() == 0}
																			action-hidden
																		{/if}
																		{if $oComment->getRating() > 0}
																			vote-count-positive
																		{elseif $oComment->getRating() < 0}
																			vote-count-negative
																		{/if}

																		{if $oVote}
																			voted

																			{if $oVote->getDirection() > 0}
																				voted-up
																			{else}
																				voted-down
																			{/if}
																		{/if}">

				{if $oUserCurrent}
                <div class="vote-up" onclick="return ls.vote.vote({$oComment->getId()},this,1,'comment');">
                <i class="material-icons">keyboard_arrow_up</i>
                </div>
				<span class="vote-count" onclick="ls.vote.getVotes({$oComment->getId()},'comment',this); return false;" id="vote_total_comment_{$oComment->getId()}">
    						    {if $oComment->getRating()>0}+{/if}{$oComment->getRating()}
    					    </span>
                <div class="vote-down" onclick="return ls.vote.vote({$oComment->getId()},this,-1,'comment');">
                <i class="material-icons">keyboard_arrow_down</i>
                </div>
				{/if}
			</li>
		</ul>
	</div>
</section>
{/if}