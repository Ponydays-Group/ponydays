{assign var="oUser" value=$oComment->getUser()}
{assign var="oVote" value=$oComment->getVote()}


<section id="comment_id_{$oComment->getId()}" class="comment
														{if $oComment->isBad()}
															comment-bad
														{/if}

														{if $oComment->getDelete()}
															comment-deleted
														{elseif $oUserCurrent and $oComment->getUserId() == $oUserCurrent->getId()}
															comment-self
														{elseif $sDateReadLast <= $oComment->getDate()}
															comment-new
														{/if}">
	{if !$oComment->getDelete() or $bOneComment or ($oUserCurrent and $oUserCurrent->isAdministrator())}
		<a name="comment{$oComment->getId()}"></a>


		<a href="{$oUser->getUserWebPath()}" target="_blank"><img src="{$oUser->getProfileAvatarPath(48)}" 
alt="avatar" 
class="comment-avatar" /></a>


		<ul class="comment-info">
			<li class="comment-author"><a href="{$oUser->getUserWebPath()}" target="_blank">{$oUser->getLogin()}</a></li>
		</ul>


		<div id="comment_content_id_{$oComment->getId()}" class="comment-content text">
			{$oComment->getText()}
		</div>



			<ul class="comment-actions">
				<li class="comment-date">
					<a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}#comment{/if}{$oComment->getId()}" class="" title="{$aLang.comment_url_notice}">
						<time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() format="j F Y, H:i"}</time>
					</a>
				</li>
				{if $oUserCurrent}

				{if !$oComment->getDelete() and !$bAllowNewComment}
					<li><a href="#" onclick="ls.comments.toggleCommentForm({$oComment->getId()}); return false;" class="reply-link">{$aLang.comment_answer}</a></li>
				{/if}

				{if !$oComment->getDelete() and $oUserCurrent and $oUserCurrent->isAdministrator()}
					<li><a href="#" class="comment-delete action-hidden" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;"><i class="fa fa-trash" title="{$aLang.comment_delete}"></i></a></li>
				{/if}

				{if $oComment->getDelete() and $oUserCurrent and $oUserCurrent->isAdministrator()}
					<li><a href="#" class="comment-repair action-hidden" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_repair}</a></li>
				{/if}

                {hook run='comment_action' comment=$oComment}

                {if $oUserCurrent and !$bNoCommentFavourites}
					<li class="comment-favourite action-hidden">
						<div onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');" class="fa fa-heart-o favourite {if $oComment->getIsFavourite()}active{/if}"></div>
						<span class="favourite-count" id="fav_count_comment_{$oComment->getId()}">{if $oComment->getCountFavourite() > 0}{$oComment->getCountFavourite()}{/if}</span>
					</li>
                {/if}
								{/if}

                {if $oComment->getPid()}
					<li class="goto-comment-parent action-hidden"><a href="#" onclick="ls.comments.goToParentComment({$oComment->getId()},{$oComment->getPid()}); return false;" title="{$aLang.comment_goto_parent}">↑</a></li>
                {/if}
				<li class="goto-comment-child action-hidden"><a href="#" title="{$aLang.comment_goto_child}">↓</a></li>

                {if $oComment->getTargetType() != 'talk'}
					<li id="vote_area_comment_{$oComment->getId()}" class="vote
																		{if $oComment->getRating() == 0}
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
						{if $oUserCurrent}<div class="vote-down fa fa-minus-square-o" onclick="return ls.vote.vote({$oComment->getId()},this,-1,'comment');"></div>{/if}
						<span class="vote-count" id="vote_total_comment_{$oComment->getId()}">{if $oComment->getRating() > 0}+{/if}{$oComment->getRating()}</span>
						{if $oUserCurrent}<div class="vote-up fa fa-plus-square-o" onclick="return ls.vote.vote({$oComment->getId()},this,1,'comment');"></div>{/if}
					</li>
                {/if}
			</ul>
	{else}
		{$aLang.comment_was_delete}
	{/if}
</section>
