{include file='header.tpl'}
{assign var="oUserOwner" value=$oBlog->getOwner()}
{assign var="oVote" value=$oBlog->getVote()}

<div class="modal modal-description" id="window_blog_description">
	<header class="modal-header">
		<h3>Описание блога "{$oBlog->getTitle()}"</h3>
		<a href="#" class="close jqmClose fa fa-close"></a>
	</header>

	<div class="modal-content">
			{$oBlog->getDescription()}
	</div>
</div>

<script type="text/javascript">
	jQuery(function($){
		ls.lang.load({lang_load name="blog_fold_info,blog_expand_info"});
	});
</script>


{if $oUserCurrent and $oUserCurrent->isAdministrator()}
	<div id="blog_delete_form" class="modal">
		<header class="modal-header">
			<h3>{$aLang.blog_admin_delete_title}</h3>
			<a href="#" class="close jqmClose"></a>
		</header>


		<form action="{router page='blog'}delete/{$oBlog->getId()}/" method="POST" class="modal-content">
			<p><label for="topic_move_to">{$aLang.blog_admin_delete_move}:</label>
			<select name="topic_move_to" id="topic_move_to" class="input-width-full">
				<option value="-1">{$aLang.blog_delete_clear}</option>
				{if $aBlogs}
					<optgroup label="{$aLang.blogs}">
						{foreach from=$aBlogs item=oBlogDelete}
							<option value="{$oBlogDelete->getId()}">{$oBlogDelete->getTitle()|escape:'html'}</option>
						{/foreach}
					</optgroup>
				{/if}
			</select></p>

			<input type="hidden" value="{$LIVESTREET_SECURITY_KEY}" name="security_ls_key" />
			<button type="submit" class="button button-primary">{$aLang.blog_delete}</button>
		</form>
	</div>
{/if}

<div class="blog-fullheader">
	<header>
		<div id="test-trigger" class="blog-desc-checkbox-label"><i class="fa fa-info-circle"></i></div>
		<div class="blog-info">
		<span class="blog-title">{$oBlog->getTitle()}</span>
		<span class="blog-description">{$oBlog->getDescription()|strip_tags|trim|truncate:50:'...'}</span>
		<div class="blog-admins">
			<a href="{$oUserOwner->getUserWebPath()}" class="user">@{$oUserOwner->getLogin()}</a>
	        {if $aBlogAdministrators}
	        {foreach from=$aBlogAdministrators item=oBlogUser}
	            {assign var="oUser" value=$oBlogUser->getUser()}
				<a href="{$oUser->getUserWebPath()}" class="user">@{$oUser->getLogin()}</a>
	        {/foreach}
	        {/if}
		</div>
		<div class="blog-subscribe">
						{if $oUserCurrent and $oUserCurrent->getId()!=$oBlog->getOwnerId()}
			{if $oBlog->getType()!="close"}
				<span><a href="#" class="blog-join" onclick="ls.blog.toggleJoin(this,{$oBlog->getId()}); return false;" class="link-dotted">{if $oBlog->getUserIsJoin()}{$aLang.blog_leave}{else}{$aLang.blog_join}{/if}</a></span>
			{else}
				<span class="blog-join"><a href="#" class="blog-join" onclick="ls.blog.toggleJoin(this,{$oBlog->getId()}); return false;" class="link-dotted">{if $oBlog->getUserIsJoin()}{$aLang.blog_leave}{/if}</a></span>
			{/if}
			{/if}
		</div>
		<div class="blog-stat">
			<div class="blog-readers">
				<span class="title">{$aLang.blog_user_readers}:</span>
				<span><i class="fa fa-user-o"></i> {$iCountBlogUsers}</span>
			</div>
			<div class="blog-topics">
				<span class="title">{$aLang.blog_topics_count}:</span>
				<span>{$iCountBlogTopics}</span>
			</div>
			<div id="vote_area_blog_{$oBlog->getId()}" class="vote {if $oBlog->getRating() > 0}vote-count-positive{elseif $oBlog->getRating() < 0}vote-count-negative{/if} {if $oVote} voted {if $oVote->getDirection()>0}voted-up{elseif $oVote->getDirection()<0}voted-down{/if}{/if}">
				<div class="vote-label">Рейтинг</div>
				<div style="flex-direction: row;">
					<a href="#" class="vote-up fa fa-chevron-up" onclick="return ls.vote.vote({$oBlog->getId()},this,1,'blog');"></a>
					<div id="vote_total_blog_{$oBlog->getId()}" class="vote-count count" title="{$aLang.blog_vote_count}: {$oBlog->getCountVote()}">{if $oBlog->getRating() > 0}+{/if}{$oBlog->getRating()}</div>
					<a href="#" class="vote-down fa fa-chevron-down" onclick="return ls.vote.vote({$oBlog->getId()},this,-1,'blog');"></a>
				</div>
			</div>
		</div>
		</div>
	</header>
</div>

{hook run='blog_info' oBlog=$oBlog}

<div class="nav-filter-wrapper">
	<ul class="nav nav-pills">
		<li {if $sMenuSubItemSelect=='good'}class="active"{/if}><a href="{$sMenuSubBlogUrl}">{$aLang.blog_menu_collective_good}</a></li>
		<li {if $sMenuSubItemSelect=='new'}class="active"{/if}><a href="{$sMenuSubBlogUrl}newall/">{$aLang.blog_menu_collective_new}</a></li>
		<li {if $sMenuSubItemSelect=='discussed'}class="active"{/if}><a href="{$sMenuSubBlogUrl}discussed/">{$aLang.blog_menu_collective_discussed}</a></li>
		<li {if $sMenuSubItemSelect=='top'}class="active"{/if}><a href="{$sMenuSubBlogUrl}top/">{$aLang.blog_menu_collective_top}</a></li>
		{hook run='menu_blog_blog_item'}
	</ul>

	{if $sPeriodSelectCurrent}
		<ul class="nav nav-filter nav-filter-sub">
			<li {if $sPeriodSelectCurrent=='1'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=1">{$aLang.blog_menu_top_period_24h}</a></li>
			<li {if $sPeriodSelectCurrent=='7'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=7">{$aLang.blog_menu_top_period_7d}</a></li>
			<li {if $sPeriodSelectCurrent=='30'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=30">{$aLang.blog_menu_top_period_30d}</a></li>
			<li {if $sPeriodSelectCurrent=='all'}class="active"{/if}><a href="{$sPeriodSelectRoot}?period=all">{$aLang.blog_menu_top_period_all}</a></li>
		</ul>
	{/if}
</div>




{if $bCloseBlog}
	{$aLang.blog_close_show}
{else}
	{include file='topic_list.tpl'}
{/if}


{include file='footer.tpl'}
