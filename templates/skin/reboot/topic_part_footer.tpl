	{assign var="oBlog" value=$oTopic->getBlog()}
	{assign var="oUser" value=$oTopic->getUser()}
	{assign var="oVote" value=$oTopic->getVote()}
	{assign var="oFavourite" value=$oTopic->getFavourite()}


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


		<ul class="topic-info">
			<li id="vote_area_topic_{$oTopic->getId()}" class="stickyDa vote 
																{if $oVote || ($oUserCurrent && $oTopic->getUserId() == $oUserCurrent->getId()) || strtotime($oTopic->getDateAdd()) < $smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')}
																	{if $oTopic->getRating() > 0}
																		vote-count-positive
																	{elseif $oTopic->getRating() < 0}
																		vote-count-negative
																	{/if}
																{/if}
																
																{if $oVote} 
																	voted
																	
																	{if $oVote->getDirection() > 0}
																		voted-up
																	{elseif $oVote->getDirection() < 0}
																		voted-down
																	{/if}
																{/if}">
				{if $oVote || ($oUserCurrent && $oTopic->getUserId() == $oUserCurrent->getId()) || strtotime($oTopic->getDateAdd()) < $smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')}
					{assign var="bVoteInfoShow" value=true}
				{/if}
				{if $oUserCurrent}
					{if $oUserCurrent->getId() != $oUser->getId()}
				<div class="vote-up" onclick="return ls.vote.vote({$oTopic->getId()},this,1,'topic');"><i class="fa fa-chevron-up"></i></div>
				<div class="vote-down" onclick="return ls.vote.vote({$oTopic->getId()},this,-1,'topic');"><i class="fa fa-chevron-down"></i></div>
					{/if}
				{/if}
				{if $bVoteInfoShow}
					<div id="vote-info-topic-{$oTopic->getId()}" style="display: none;">
						+ {$oTopic->getCountVoteUp()}<br/>
						- {$oTopic->getCountVoteDown()}<br/>
						&nbsp; {$oTopic->getCountVoteAbstain()}<br/>
						{hook run='topic_show_vote_stats' topic=$oTopic}
					</div>
				{/if}
			</li>

			<li class="topic-info-author"><a rel="author" href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
			<li class="topic-info-favourite">
				<div onclick="return ls.favourite.toggle({$oTopic->getId()},this,'topic');" class="favourite {if $oUserCurrent && $oTopic->getIsFavourite()}active{/if}"><i class="favourite-icon fa fa-heart"></i></div>
				<span class="favourite-count" id="fav_count_topic_{$oTopic->getId()}">{$oTopic->getCountFavourite()}</span>
			</li>
						
			{if $bTopicList}
				<li class="topic-info-comments">
					<a href="{$oTopic->getUrl()}#comments" title="{$aLang.topic_comment_read}">{$oTopic->getCountComment()} <i class="fa fa-comments"></i></a>
					{if $oTopic->getCountCommentNew()}<span>+{$oTopic->getCountCommentNew()}</span>{/if}
				</li>
			{/if}
			{if $bTopicList}
				{if $oTopic->getTextShort()!=$oTopic->getText()}
					<li class="cut">
					<a href="{$oTopic->getUrl()}#cut" title="{$aLang.topic_read_more}">
						{if $oTopic->getCutText()}
							{$oTopic->getCutText()}
						{else}
							{$aLang.topic_read_more}
						{/if}
					</a>
						</li>
				{/if}
			{/if}
			{hook run='topic_show_info' topic=$oTopic}
		</ul>

		
		{if !$bTopicList}
			{hook run='topic_show_end' topic=$oTopic}
		{/if}
	</footer>
</article> <!-- /.topic -->
