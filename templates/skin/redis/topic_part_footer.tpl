	{assign var="oBlog" value=$oTopic->getBlog()}
	{assign var="oUser" value=$oTopic->getUser()}
	{assign var="oVote" value=$oTopic->getVote()}
	{assign var="oFavourite" value=$oTopic->getFavourite()}
	{assign var="bEnableTopicVoteInfo" value=$LS->ACL_CheckSimpleAccessLevel(Config::Get('acl.vote_list.topic.ne_enable_level'), $oUserCurrent, $oTopic, 'topic')}


	<footer class="topic-footer">
		<ul class="topic-tags js-favourite-insert-after-form js-favourite-tags-topic-{$oTopic->getId()}">
			<li>{$aLang.topic_tags}:</li>
			
			{strip}
				{if $oTopic->getTagsArray()}
					{foreach from=$oTopic->getTagsArray() item=sTag name=tags_list}
						<li>{if !$smarty.foreach.tags_list.first}, {/if}<a rel="tag" href="{router page='tag'}{$sTag|escape:'url'}/">{$sTag|escape:'html'}</a></li>
					{/foreach}
				{else}
					<li>{$aLang.topic_tags_empty}</li>
				{/if}
				
				{if $oUserCurrent}
					{if $oFavourite}
						{foreach from=$oFavourite->getTagsArray() item=sTag name=tags_list_user}
							<li class="topic-tags-user js-favourite-tag-user">, <a rel="tag" href="{$oUserCurrent->getUserWebPath()}favourites/topics/tag/{$sTag|escape:'url'}/">{$sTag|escape:'html'}</a></li>
						{/foreach}
					{/if}
					
					<li class="topic-tags-edit js-favourite-tag-edit" {if !$oFavourite}style="display:none;"{/if}>
						<a href="#" onclick="return ls.favourite.showEditTags({$oTopic->getId()},'topic',this);" class="link-dotted">{$aLang.favourite_form_tags_button_show}</a>
					</li>
				{/if}
			{/strip}
		</ul>


		<div class="topic-info {if $bEnableTopicVoteInfo}vote-info-enable{/if}">
				<a href="#" class="vote-up {if $oVote}{if $oVote->getDirection() > 0}voted{/if}{/if}" onclick="return ls.vote.vote({$oTopic->getId()},this,1,'topic');"><i class="material-icons">keyboard_arrow_up</i></a>
				<a href="javascript://" class="vote-count {if $oTopic->getRating() > 0}
																		vote-count-positive
																	{elseif $oTopic->getRating() < 0}
																		vote-count-negative
																	{elseif $oTopic->getRating() == 0 and $oTopic->getCountVote() > 0}
																		vote-count-mixed
																	{/if} {if false and $bEnableTopicVoteInfo}js-infobox-vote-topic{/if}" {if $bEnableTopicVoteInfo}onclick="ls.vote.getVotes({$oTopic->getId()},'topic',this,true); return false;" data-count="{$oTopic->getCountVote()}"{/if} id="vote_total_topic_{$oTopic->getId()}" title="{$aLang.topic_vote_count}: {$oTopic->getCountVote()}">
						{if $oTopic->getRating() > 0}+{/if}{$oTopic->getRating()}
				</a>
				<a href="#" class="vote-down {if $oVote}{if $oVote->getDirection() < 0}voted{/if}{/if}"
					onclick="return ls.vote.vote({$oTopic->getId()},this,-1,'topic');"><i class="material-icons">keyboard_arrow_down</i></a>
				{if false and $bEnableTopicVoteInfo}
					<div id="vote-info-topic-{$oTopic->getId()}" style="display: none;">
						+ {$oTopic->getCountVoteUp()}<br/>
						- {$oTopic->getCountVoteDown()}<br/>
						&nbsp; {$oTopic->getCountVoteAbstain()}<br/>
						{hook run='topic_show_vote_stats' topic=$oTopic}
					</div>
				{/if}

			<a href="#" class="topic-info-favourite">
				<i onclick="return ls.favourite.toggle({$oTopic->getId()},this,'topic');" class="fa fa-heart-o favourite {if $oUserCurrent && $oTopic->getIsFavourite()}active{/if}"></i>
				<span class="favourite-count" id="fav_count_topic_{$oTopic->getId()}"></span>
			</a>
			
			{if $bTopicList}
				<a href="{$oTopic->getUrl()}#comments" title="{$aLang.topic_comment_read}" class="topic-info-comments">
					<i class="fa fa-comment-o"></i> {$oTopic->getCountComment()}
					{if $oTopic->getCountCommentNew()}<span>+{$oTopic->getCountCommentNew()}</span>{/if}
				</a>
			{/if}
			
			{hook run='topic_show_info' topic=$oTopic}
		</div>

		
		{if !$bTopicList}
			{hook run='topic_show_end' topic=$oTopic}
		{/if}
	</footer>
</article> <!-- /.topic -->