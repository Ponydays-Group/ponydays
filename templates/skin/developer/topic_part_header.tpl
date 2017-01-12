{assign var="oBlog" value=$oTopic->getBlog()}
{assign var="oUser" value=$oTopic->getUser()}
{assign var="oVote" value=$oTopic->getVote()}


<article class="topic topic-type-{$oTopic->getType()} js-topic">
	<header class="topic-header">
		<div class="topic-header-top">
			<a href="{$oTopic->getUrl()}" class="topic-title">{$oTopic->getTitle()}</a>
			<span class="topic-author-login"><i class="fa fa-user-o"></i>{$oUser->getLogin()}</span>
			<time class="topic-time" datetime="{date_format date=$oTopic->getDateAdd() format='c'}" title="{date_format date=$oTopic->getDateAdd() format='j F Y, H:i'}">
				<i class="fa fa-clock-o"></i> {date_format date=$oTopic->getDateAdd() format="j F Y, H:i"}
			</time>
			<span><i class="fa fa-folder-o"></i><a href="{$oBlog->getUrlFull()}" class="topic-blog">{$oBlog->getTitle()|escape:'html'}</a></span>
		</div>
		<div class="topic-header-bottom">
			<img src="{$oUser->getProfileAvatarPath(64)}" class="topic-author-avatar" />



		</div>
		{*
		<h1 class="topic-title word-wrap">
			{if $oTopic->getPublish() == 0}   
				<i class="icon-tag" title="{$aLang.topic_unpublish}"></i>
			{/if}
			
			{if $oTopic->getType() == 'link'} 
				<i class="icon-share-alt" title="{$aLang.topic_link}"></i>
			{/if}
			
			{if $bTopicList}
				<a href="{$oTopic->getUrl()}">{$oTopic->getTitle()|escape:'html'}</a>
			{else}
				{$oTopic->getTitle()|escape:'html'}
			{/if}
		</h1>
		
		
		{if $oTopic->getType() == 'link'}
			<div class="topic-url">
				<a href="{router page='link'}go/{$oTopic->getId()}/" title="{$aLang.topic_link_count_jump}: {$oTopic->getLinkCountJump()}">{$oTopic->getLinkUrl()}</a>
			</div>
		{/if}
		
		
		<div class="topic-info">
			<a href="{$oBlog->getUrlFull()}" class="topic-blog">{$oBlog->getTitle()|escape:'html'}</a>
			
			<time datetime="{date_format date=$oTopic->getDateAdd() format='c'}" title="{date_format date=$oTopic->getDateAdd() format='j F Y, H:i'}">
				{date_format date=$oTopic->getDateAdd() format="j F Y, H:i"}
			</time>
			
			<ul class="actions">								   
				{if $oUserCurrent and ($oUserCurrent->getId()==$oTopic->getUserId() or $oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $oBlog->getOwnerId()==$oUserCurrent->getId())}
					<li><a href="{cfg name='path.root.web'}/{$oTopic->getType()}/edit/{$oTopic->getId()}/" title="{$aLang.topic_edit}" class="actions-edit">{$aLang.topic_edit}</a></li>
				{/if}
				
				{if $oUserCurrent and ($oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getOwnerId()==$oUserCurrent->getId())}
					<li><a href="{router page='topic'}delete/{$oTopic->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.topic_delete}" onclick="return confirm('{$aLang.topic_delete_confirm}');" class="actions-delete">{$aLang.topic_delete}</a></li>
				{/if}
			</ul>
		</div>
		*}
	</header>