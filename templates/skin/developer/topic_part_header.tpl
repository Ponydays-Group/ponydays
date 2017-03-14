{assign var="oBlog" value=$oTopic->getBlog()}
{assign var="oUser" value=$oTopic->getUser()}
{assign var="oVote" value=$oTopic->getVote()}
{assign var="bAllowLockControl" value=$oTopic->testAllowLockControl($oUserCurrent)}
{assign var="bVoteInfoEnabled" value=$LS->ACL_CheckSimpleAccessLevel(Config::Get('acl.vote_state.comment.ne_enable_level'), $oUserCurrent, $oTopic, 'topic')}


<article class="topic topic-type-{$oTopic->getType()} js-topic">
	<header class="topic-header">
		<div class="topic-header-top">
			<span class="topic-title"><a href="{$oTopic->getUrl()}">{$oTopic->getTitle()}</a></span>
			<ul class="actions" style="float: right; display: inline-block;">
				{if $oUserCurrent and (($oUserCurrent->isGlobalModerator() and $oTopic->getBlog()->getType() == "open") or $oUserCurrent->getId()==$oTopic->getUserId() or $oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $oBlog->getOwnerId()==$oUserCurrent->getId())}
					<li><a href="{cfg name='path.root.web'}/{$oTopic->getType()}/edit/{$oTopic->getId()}/" title="{$aLang.topic_edit}" class="actions-edit">{$aLang.topic_edit}</a></li>
				{/if}

				{if $oUserCurrent and (($oUserCurrent->isGlobalModerator() and $oTopic->getBlog()->getType() == "open") or $oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or ($oTopic->getUserId() === $oUserCurrent->getId() and !$oTopic->isControlLocked()) or $oBlog->getOwnerId()==$oUserCurrent->getId())}
					<li><a href="#" title="{$aLang.topic_delete}" onclick="onDelete();" class="actions-delete">{$aLang.topic_delete}</a></li>
				{/if}

				{if $bAllowLockControl}
					<li><span class="checkbox"><span style="padding: 0;"><input name="topic_{$oTopic->getId()}_lock" id="topic_{$oTopic->getId()}_lock" type="checkbox" onclick="if(!!this.checked || confirm('{$aLang.topic_lock_control_un}')) return ls.topic.lockControl({$oTopic->getId()},this); else return false;" {if $oTopic->isControlLocked()}checked="checked"{/if} /><label for="topic_{$oTopic->getId()}_lock" title="{$aLang.topic_lock_control_title}" class="actions-topic_lock_control" style="padding: 0px 0px 0px 25px;">{$aLang.topic_lock_control}</label></span></span></li>
				{/if}
			</ul>
			<a href="{$oUser->getUserWebPath()}" class="topic-author-login"><i class="fa fa-user-o"></i>{$oUser->getLogin()}</a>
			<time class="topic-time" datetime="{date_format date=$oTopic->getDateAdd() format='c'}" title="{date_format date=$oTopic->getDateAdd() format='j F Y, H:i'}">
				<i class="fa fa-clock-o"></i> {date_format date=$oTopic->getDateAdd() format="j F Y, H:i"}
			</time>
			<span><i class="fa fa-folder-o"></i><a href="{$oBlog->getUrlFull()}" class="topic-blog">{$oBlog->getTitle()|escape:'html'}</a></span>
		</div>
		<div class="topic-header-bottom">
			<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(64)}" class="topic-author-avatar" /></a>
		</div>
	</header>
