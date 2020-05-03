{assign var="sidebarPosition" value='left'}
{include file='header.tpl'}

{include file='actions/ActionProfile/profile_top.tpl'}
{include file='menu.talk.tpl'}

{assign var="oUser" value=$oTalk->getUser()}


<article class="topic topic-type-talk">
	<header class="topic-header">
		<div class="topic-data-wrapper">
			<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(100)}" width="32px" alt="avatar" class="topic-author-avatar avatar" /></a>
			<div class="topic-data">
				<span class="topic-title"><a href="#">{$oTalk->getTitle()|escape:'html'}</a></span>
				<span><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>, <time datetime="{date_format date=$oTalk->getDate() format='c'}" pubdate>
                    {date_format date=$oTalk->getDate() format="j F Y, H:i"}
				</time></span>
			</div>
		</div>
		<div class="topic-more">
			<i class="material-icons">more_vert</i>
			<div class="topic-dropdown">
				<a href="{router page='talk'}delete/{$oTalk->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" onclick="return confirm('{$aLang.talk_inbox_delete_confirm}');" class="delete"><i class="material-icons">delete</i> Удалить</a>
				<a href="#" class="link-dotted" onclick="jQuery('#talk_recipients').toggle(); return false;">{$aLang.talk_speaker_edit}</a>
			</div>
		</div>
	</header>
	
	
	{include file='actions/ActionTalk/speakers.tpl'}
	
	
	<div class="topic-content text">
		{$oTalk->getText()}
	</div>

    {$aLang.talk_speaker_title}:

    {foreach from=$oTalk->getTalkUsers() item=oTalkUser name=users}
        {assign var="oUserRecipient" value=$oTalkUser->getUser()}
        {if $oUser->getId() != $oUserRecipient->getId()}
			<a class="{if $oTalkUser->getUserActive() != $TALK_USER_ACTIVE}inactive{/if}" href="{$oUserRecipient->getUserWebPath()}">{$oUserRecipient->getLogin()}</a>{if !$smarty.foreach.users.last}, {/if}
        {/if}
    {/foreach}
	{**}
	{**}
	{*<footer class="topic-footer">*}
		{*<ul class="topic-info">*}
			{*<li class="topic-info-favourite"><a href="#" onclick="return ls.favourite.toggle({$oTalk->getId()},this,'talk');" class="favourite {if $oTalk->getIsFavourite()}active{/if}"><i class="material-icons">favorite</i></a></li>*}
			{*{hook run='talk_read_info_item' talk=$oTalk}*}
		{*</ul>*}
	{*</footer>*}
</article>

{assign var="oTalkUser" value=$oTalk->getTalkUser()}

{if !$bNoComments}
{include
	file='comment_tree.tpl'
	iTargetId=$oTalk->getId()
	sTargetType='talk'
	iCountComment=$oTalk->getCountComment()
	sDateReadLast=$oTalkUser->getDateLast()
	sNoticeCommentAdd=$aLang.topic_comment_add
	bNoCommentFavourites=true
	bEnableCommentsVoteInfo=$LS->ACL_CheckSimpleAccessLevel(Engine\Config::Get('acl.vote_list.comment.ne_enable_level'), $oUserCurrent, $oTalk, 'talk')
	}
{/if}
			
			
{include file='footer.tpl'}