<div class="comments comment-list">
	{foreach from=$aComments item=oComment}
		{assign var="oUser" value=$oComment->getUser()}
		{assign var="oTopic" value=$oComment->getTarget()}
		{assign var="oBlog" value=$oTopic->getBlog()}
		{assign var="bVoteInfoEnabled" value=$LS->ACL_CheckSimpleAccessLevel(Config::Get('acl.vote_state.comment.ne_enable_level'), $oUserCurrent, $oComment, 'comment')}



		<div class="comment-path">
			<a href="{$oBlog->getUrlFull()}" class="blog-name">{$oBlog->getTitle()|escape:'html'}</a> &rarr;
			<a href="{$oTopic->getUrl()}">{$oTopic->getTitle()|escape:'html'}</a>
			<a href="{$oTopic->getUrl()}#comments">({$oTopic->getCountComment()})</a>
		</div>


		<section class="comment">
			<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(48)}" alt="avatar" class="comment-avatar" /></a>


			<ul class="comment-info clearfix {if $bVoteInfoEnabled}voteInfo-enable{/if}">
				<li class="comment-author"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
				<li class="comment-date">
					<a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}{$oTopic->getUrl()}#comment{/if}{$oComment->getId()}" class="link-dotted" title="{$aLang.comment_url_notice}">
						<span title="{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}">{date_format date=$oComment->getDate() format="j F Y, H:i"}</span>
					</a>
				</li>
				{if $oUserCurrent and !$bNoCommentFavourites}
					<li class="comment-favourite">
						<div onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');" class="fa fa-heart favourite favourite-icon {if $oComment->getIsFavourite()}active{/if}"></div>
						<span class="favourite-count" id="fav_count_comment_{$oComment->getId()}">{if $oComment->getCountFavourite() > 0}{$oComment->getCountFavourite()}{/if}</span>
					</li>
				{/if}
				<li id="vote_area_comment_{$oComment->getId()}" class="vote 
																		{if $oComment->getRating() > 0}
																			vote-count-positive
																		{elseif $oComment->getRating() < 0}
																			vote-count-negative
																		{elseif $oComment->getRating() == 0 and $oComment->getCountVote() > 0 and $bVoteInfoEnabled}
																			vote-count-mixed
																		{/if}">
					<span class="vote-count" id="vote_total_comment_{$oComment->getId()}">{$oComment->getRating()}</span>
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
