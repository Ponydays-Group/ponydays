{assign var="oBlog" value=$oTopic->getBlog()}
{assign var="oUser" value=$oTopic->getUser()}
{assign var="oVote" value=$oTopic->getVote()}
{assign var="bAllowLockControl" value=$oTopic->testAllowLockControl($oUserCurrent)}
{assign var="bVoteInfoEnabled" value=$LS->ACL_CheckSimpleAccessLevel(Config::Get('acl.vote_state.comment.ne_enable_level'), $oUserCurrent, $oTopic, 'topic')}


<article class="topic topic-type-{$oTopic->getType()} js-topic">
    <header class="topic-header">
        <div class="topic-data-wrapper">
            <a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(64)}"
                                                      class="topic-author-avatar"/></a>
            <div class="topic-data">
                <span class="topic-title"><a href="{$oTopic->getUrl()}">{$oTopic->getTitle()}</a></span>
                {if $oTopic->getPublish() == 0}
                    <i class="fa fa-tag" title="{$aLang.topic_unpublish}"></i>
                {/if}
                <span><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>,
                    <a href="{$oBlog->getUrlFull()}" class="topic-blog">{$oBlog->getTitle()|escape:'html'}</a>
			<time class="topic-time" datetime="{date_format date=$oTopic->getDateAdd() format='c'}"
                  title="{date_format date=$oTopic->getDateAdd() format='j F Y, H:i'}">
				{date_format date=$oTopic->getDateAdd() format="j F Y, H:i"}
			</time></span>
            </div>
        </div>
        <div class="topic-more">
            <i class="material-icons">more_vert</i>
            <div class="topic-dropdown">
                {if $oUserCurrent and (($oUserCurrent->isGlobalModerator() and $oTopic->getBlog()->getType() == "open") or $oUserCurrent->getId()==$oTopic->getUserId() or $oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $oBlog->getOwnerId()==$oUserCurrent->getId())}
                    <a href="/{$oTopic->getType()}/edit/{$oTopic->getId()}/" title="{$aLang.topic_edit}">
                        <i class="material-icons">mode_edit</i> Редактировать
                    </a>
                {/if}
                {if $oUserCurrent and (($oUserCurrent->isGlobalModerator() and $oTopic->getBlog()->getType() == "open") or $oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or ($oTopic->getUserId() === $oUserCurrent->getId() and !$oTopic->isControlLocked()) or $oBlog->getOwnerId()==$oUserCurrent->getId())}
                    <a href="{router page='topic'}delete/{$oTopic->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.topic_delete}">
                        <i class="material-icons">delete</i> Удалить
                    </a>
                {/if}
                {if $bAllowLockControl}
                    <a href="#">
                        <span class="checkbox" style="margin: 0;"><span style="padding: 0;"><input name="topic_{$oTopic->getId()}_lock"
                                                                                id="topic_{$oTopic->getId()}_lock"
                                                                                type="checkbox"
                                                                                onclick="if(!!this.checked || confirm('{$aLang.topic_lock_control_un}')) return ls.topic.lockControl({$oTopic->getId()},this); else return false;"
                                                                                {if $oTopic->isControlLocked()}checked="checked"{/if} /><label
                                        for="topic_{$oTopic->getId()}_lock" title="{$aLang.topic_lock_control_title}"
                                        class="actions-topic_lock_control">{$aLang.topic_lock_control}</label></span></span>
                    </a>
                {/if}
            </div>
        </div>
    </header>
