<div class="comments comment-list {if $bEnableCommentsVoteInfo}vote-info-enable{/if}">
	{foreach from=$aComments item=oComment}
		{assign var="oUser" value=$oComment->getUser()}
		{assign var="oTopic" value=$oComment->getTarget()}
		{if $oTopic}{assign var="oBlog" value=$oTopic->getBlog()}{/if}

		<div class="comment-path">
			{if $oTopic}
				<a href="{$oBlog->getUrlFull()}" class="blog-name">{$oBlog->getTitle()|escape:'html'}</a> &rarr;
				<a href="{$oTopic->getUrl()}">{$oTopic->getTitle()|escape:'html'}</a>
				<a href="{$oTopic->getUrl()}#comments">({$oTopic->getCountComment()})</a>
			{else}
				<p style="color: red">[Удалено]</p>
			{/if}
		</div>

		<section class="comment">
			<a href="{$oUser->getUserWebPath()}"><img src="{$oComment->getUserAvatar()}" height="48" width="48" alt="avatar" class="comment-avatar" /></a>

			<ul class="comment-info clearfix">
				<li class="comment-author"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
				<li class="comment-date">
					{if $oTopic}<a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}{$oTopic->getUrl()}#comment{/if}{$oComment->getId()}" class="link-dotted" title="{$aLang.comment_url_notice}">{/if}
						<time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</time>
					{if $oTopic}</a>{/if}
				</li>
				{if $oUserCurrent and !$bNoCommentFavourites}
					<li class="comment-favourite">
						<div onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');" class="fa fa-heart-o favourite{if $oComment->getIsFavourite()} active{/if}"></div>
						<span class="favourite-count" id="fav_count_comment_{$oComment->getId()}">{if $oComment->getCountFavourite() > 0}{$oComment->getCountFavourite()}{/if}</span>
					</li>
				{/if}
				<li id="vote_area_comment_{$oComment->getId()}" class="vote 
																		{if $oComment->getRating() > 0}
																			vote-count-positive
																		{elseif $oComment->getRating() < 0}
																			vote-count-negative
																		{elseif $oComment->getCountVote() > 0}
																			vote-count-mixed
																		{else}
																			vote-count-zero
																		{/if}">
					<span class="vote-count" {if $bEnableCommentsVoteInfo}onclick="ls.vote.getVotes({$oComment->getId()},'comment',this); return false;" data-count="{$oComment->getCountVote()}"{/if} id="vote_total_comment_{$oComment->getId()}">{$oComment->getRating()}</span>
				</li>
			</ul>

			<div class="comment-content text">						
				{if $oComment->isBad()}
					{$oComment->getText()}						
				{else}
					{$oComment->getText()}
				{/if}		
			</div>
		</section>
	{/foreach}	
</div>

{include file='paging.tpl' aPaging=$aPaging}
