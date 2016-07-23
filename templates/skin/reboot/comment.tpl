{assign var="oUser" value=$oComment->getUser()} <!-- i defined that in comment_tree. Do we still need that here? -->
{assign var="oVote" value=$oComment->getVote()}
{assign var="oId" value=$oComment->getTarget()}

<section id="comment_id_{$oComment->getId()}" class="comment user-login-{$oUser->getLogin()} 
														{if $oComment->isBad()}
															comment-bad
														{/if}
														{if $oComment->getDelete()}
															comment-deleted
														{elseif $oUserCurrent and $oComment->getUserId() == $oUserCurrent->getId()}
															comment-self
														{elseif $sDateReadLast <= $oComment->getDate()}
															comment-new
														{/if}
														">
	{if !$oComment->getDelete() or ($oUserCurrent and $oUserCurrent->isGlobalModerator()) or ($oUserCurrent and $oUserCurrent->isAdministrator())}
		<a name="comment{$oComment->getId()}"></a>
		<div class="folding fa fa-minus-square" id="folding"></div>

		<a target="_blank" href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(48)}" alt="avatar" class="comment-avatar" /></a>


		<ul class="comment-info">
			<li class="comment-author">
				<a target="_blank" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
			</li>
			<li class="comment-date">
				<a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}#comment{/if}{$oComment->getId()}" class="link-dotted" title="{$aLang.comment_url_notice}">
					<time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() format="j F Y, H:i"}</time>
				</a>
			</li>

			{if $oComment->getPid()}
				<li class="goto-comment-parent"><a href="#" onclick="ls.comments.goToParentComment({$oComment->getId()},{$oComment->getPid()}); return false;" title="{$aLang.comment_goto_parent}">↑</a></li>
			{/if}
			<li class="goto-comment-child"><a href="#" title="{$aLang.comment_goto_child}">↓</a></li>




			{if $oUserCurrent and !$bNoCommentFavourites}
				<li class="comment-favourite">
					<div onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');" class="favourite {if $oComment->getIsFavourite()}active{/if}"><i class="favourite-icon fa fa-heart"></i></div>
					<span class="favourite-count" id="fav_count_comment_{$oComment->getId()}">{if $oComment->getCountFavourite() > 0}{$oComment->getCountFavourite()}{/if}</span>
				</li>
			{/if}
{if $oComment->getTargetType() != 'talk'}
				{if !$oComment->getDelete() and $oUserCurrent and ($oUserCurrent->isAdministrator() or ($oUserCurrent->isGlobalModerator() and $oComment->getTarget()->getBlog()->getType()=="open"))}
					<li><a href="#" class="comment-delete link-dotted" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_delete}</a></li>
				{/if}

				{if $oComment->getDelete() and $oUserCurrent and ($oUserCurrent->isAdministrator() or $oUserCurrent->isGlobalModerator())}
					<li><a href="#" class="comment-repair link-dotted" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_repair}</a></li>
				{/if}
{/if}
				{hook run='comment_action' comment=$oComment}
		</ul>


		<div id="comment_content_id_{$oComment->getId()}" class="comment-content text">
			{if $oComment->isBad()}
				<span onclick="children[0].style.display = 'block'; children[1].style.display='none' "><span style="display: none;">{$oComment->getText()}</span><a href="#" onclick="return false">Комментарий скрыт. Кликните, чтобы раскрыть.</a></span>
			{else}
				{$oComment->getText()}
			{/if}
		</div>


		{if $oUserCurrent}
			<ul class="comment-actions">
				{if !$oComment->getDelete() and !$bAllowNewComment}
					<li><a href="#" onclick="ls.comments.toggleCommentForm({$oComment->getId()}); return false;" class="reply-link link-dotted">{$aLang.comment_answer}</a></li>
				{/if}
		{/if}
{if $oComment->getTargetType() != 'talk'}

				{if $oComment->getTargetType() != 'talk'}
				<li style="margin-right: 0px;" id="vote_area_comment_{$oComment->getId()}" class="comment-vote vote
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
					
					<div class="vote-down" onclick="return ls.vote.vote({$oComment->getId()},this,-1,'comment');"><i class="fa fa-arrow-down"></i></div>
					<span class="vote-count" id="vote_total_comment_{$oComment->getId()}">{if $oComment->getRating() > 0}+{/if}{$oComment->getRating()}</span>
					<div class="vote-up" onclick="return ls.vote.vote({$oComment->getId()},this,1,'comment');"><i class="fa fa-arrow-up"></i></div>
				</li>
			{/if}
			</ul>
		{/if}
	{else}
		{$aLang.comment_was_delete}
	{/if}
</section>
