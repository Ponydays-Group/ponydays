{assign var="oSession" value=$oUserProfile->getSession()}
{assign var="oVote" value=$oUserProfile->getVote()}
{assign var="oGeoTarget" value=$oUserProfile->getGeoTarget()}
{if $oUserCurrent && $oUserCurrent->isAdministrator()}
    <section class="block block-profile-admin">
        <header class="block-header">
            <h3>Инфа для админов</h3>
        </header>
        <form id="user_admin">
            <label for="admin_id">User ID: {$oUserProfile->getId()}</label>
            <input type="hidden" value="{$oUserProfile->getId()}" id="user_id" name="user_id" />
            <label for="admin_id">User is admin: {if $oUserProfile->isAdministrator()}<span style="color: #4CAF50;">true</span>{else}<span style="color: #F44336;">false</span>{/if}</label>
            <label for="admin_id">User is moder: {if $oUserProfile->isGlobalModerator()}<span style="color: #4CAF50;">true</span>{else}<span style="color: #F44336;">false</span>{/if}</label>
            <label for="admin_mail">User email:</label>
            <input id="user_mail" style="margin-bottom: 10px;" name="user_mail" value="{$oUserProfile->getMail()}" />
            <label for="user_rank">User rank:</label>
            <input id="user_rank" style="margin-bottom: 10px;" name="user_rank" value="{$oUserProfile->getRank()}" />
            <label for="user_login">User login:</label>
            <input id="user_login" style="margin-bottom: 10px;" name="user_login" value="{$oUserProfile->getLogin()}" />
            <button class="button button-primary" type="submit" onclick="ls.ajax.saveUserAdmin(); return false;">Save</button>
        </form>
    </section>
{/if}

{if $oUserProfile->getProfileAbout()}
    <section class="block block-profile-about">
        <header class="block-header">
            <h3>О себе</h3>
        </header>
        <div class="block-content colorful-links content-big text">
            {$oUserProfile->getProfileAbout()}
        </div>
    </section>
{/if}

{if $oUserCurrent && $oUserCurrent->getId()!=$oUserProfile->getId()}
    <script type="text/javascript">
        jQuery(function ($) {
            ls.lang.load({lang_load name="profile_user_unfollow,profile_user_follow"});
        });
    </script>
    <section class="block block-type-profile-actions">
        <div class="block-content">
            <ul class="profile-actions" id="profile_actions">
                {include file='actions/ActionProfile/friend_item.tpl' oUserFriend=$oUserProfile->getUserFriend()}
                <li>
                    <a href="{router page='talk'}add/?talk_users={$oUserProfile->getLogin()}">{$aLang.user_write_prvmsg}</a>
                </li>
                <li>
                    <a href="#" onclick="ls.user.followToggle(this, {$oUserProfile->getId()}); return false;"
                       class="{if $oUserProfile->isFollow()}followed{/if}">
                        {if $oUserProfile->isFollow()}{$aLang.profile_user_unfollow}{else}{$aLang.profile_user_follow}{/if}
                    </a>
                </li>
                {if $oUserCurrent->isAdministrator()}
                    <a href="#"
                       onclick="forbidIgnoreUser({$oUserProfile->getId()}, this); return false;">{if $bForbidIgnore}{$aLang.allow_ignore_user}{else}{$aLang.forbid_ignore_user}{/if}</a>
                    <br/>
                {/if}

                {if $oUserCurrent->getId() != $oUserProfile->getId()}
                    {if !$bForbidIgnore}
                        <a href="#"
                           onclick="ignoreUser({$oUserProfile->getId()}, 'topics',this); return false;">{if $bIgnoredTopics}{$aLang.disignore_user_topics}{else}{$aLang.ignore_user_topics}{/if}</a>
                        <br/>
                        <a href="#"
                           onclick="ignoreUser({$oUserProfile->getId()}, 'comments',this); return false;">{if $bIgnoredComments}{$aLang.disignore_user_comments}{else}{$aLang.ignore_user_comments}{/if}</a>
                        <br/>
                    {/if}
                    <a href="#"
                       onclick="ignoreTalkUser('{$oUserProfile->getLogin()}', {$oUserProfile->getId()},this); return false;">{if $bIgnoredTalks}{$aLang.disignore_user_talks}{else}{$aLange.ignore_user_talks}{/if}</a>
                    <br/>
                {/if}

                <script>
                    var bIgnoreState = {$bIgnoredTalks};
                    {literal}
                    function forbidIgnoreUser(idUser, a) {
                        ls.ajax.ajax(aRouter['ajax'] + 'forbid-ignore', {idUser: idUser}, function (result) {
                            if (!result) {
                                ls.msg.error('Error', 'Please try again later');
                            }
                            if (result.bStateError) {
                                ls.msg.error(result.sMsgTitle, result.sMsg);
                            } else {
                                jQuery(a).html(result.sText);
                                ls.msg.notice(result.sMsgTitle, result.sMsg);
                            }
                        });
                    }

                    function ignoreUser(idUser, type, a) {
                        ls.ajax.ajax(aRouter['ajax'] + 'ignore', {idUser: idUser, type: type}, function (result) {
                            if (!result) {
                                ls.msg.error('Error', 'Please try again later');
                            }
                            if (result.bStateError) {
                                ls.msg.error(result.sMsgTitle, result.sMsg);
                            } else {
                                jQuery(a).html(result.sText);
                                ls.msg.notice(result.sMsgTitle, result.sMsg);
                            }
                        });
                    }

                    function ignoreTalkUser(loginUser, idUser, a) {
                        var error = false;
                        if (bIgnoreState) {
                            ls.ajax.ajax(aRouter['talk'] + 'ajaxdeletefromblacklist/', {idTarget: idUser}, function (result) {
                                if (!result) {
                                    ls.msg.error('Error', 'Please try again later');
                                    var error = true;
                                }
                                if (result.bStateError) {
                                    ls.msg.error(null, result.sMsg);
                                    var error = true;
                                }

                                if (!error) {
                                    jQuery(a).html(ls.lang.get('plugin.ignore.ignore_user_talks'));
                                    ls.msg.notice(null, ls.lang.get('plugin.ignore.disignore_user_ok_talk'));
                                    bIgnoreState = 0;
                                }
                            });
                        } else {
                            ls.ajax.ajax(aRouter['talk'] + 'ajaxaddtoblacklist/', {users: loginUser}, function (result) {
                                if (result.bStateError) {
                                    ls.msg.error(null, result.sMsg);
                                    error = true;
                                } else {
                                    $.each(result.aUsers, function (index, item) {
                                        if (item.bStateError) {
                                            ls.msg.notice(null, item.sMsg);
                                            error = true;
                                        }
                                    });
                                }
                                if (!error) {
                                    jQuery(a).html(ls.lang.get('plugin.ignore.disignore_user_talks'));
                                    ls.msg.notice(null, ls.lang.get('plugin.ignore.ignore_user_ok_talk'));
                                    bIgnoreState = 1;
                                }
                            });
                        }

                    }
                </script>
                {/literal}

            </ul>
        </div>
    </section>
{/if}
<section class="block">
    <header class="block-header">
        <h3>Личное</h3>
    </header>
    <div class="block-content colorful-links">
        <table class="table table-profile-info">
            {if $oUserProfile->getProfileSex()!='other'}
                <tr>
                    <td class="cell-label">{$aLang.profile_sex}:</td>
                    <td>
                        {if $oUserProfile->getProfileSex()=='man'}
                            {$aLang.profile_sex_man}
                        {else}
                            {$aLang.profile_sex_woman}
                        {/if}
                    </td>
                </tr>
            {/if}


            {if $oUserProfile->getProfileBirthday()}
                <tr>
                    <td class="cell-label">{$aLang.profile_birthday}:</td>
                    <td>{date_format date=$oUserProfile->getProfileBirthday() format="j F Y"}</td>
                </tr>
            {/if}


            {if $oGeoTarget}
                <tr>
                    <td class="cell-label">{$aLang.profile_place}:</td>
                    <td itemprop="address" itemscope itemtype="http://data-vocabulary.org/Address">
                        {if $oGeoTarget->getCountryId()}
                            <a href="{router page='people'}country/{$oGeoTarget->getCountryId()}/"
                               itemprop="country-name">{$oUserProfile->getProfileCountry()|escape:'html'}</a>{if $oGeoTarget->getCityId()},{/if}
                        {/if}

                        {if $oGeoTarget->getCityId()}
                            <a href="{router page='people'}city/{$oGeoTarget->getCityId()}/"
                               itemprop="locality">{$oUserProfile->getProfileCity()|escape:'html'}</a>
                        {/if}
                    </td>
                </tr>
            {/if}

            {if $aUserFieldValues}
                {foreach from=$aUserFieldValues item=oField}
                    <tr>
                        <td class="cell-label"><i
                                    class="icon-contact icon-contact-{$oField->getName()}"></i> {$oField->getTitle()|escape:'html'}
                            :
                        </td>
                        <td>{$oField->getValue(true,true)}</td>
                    </tr>
                {/foreach}
            {/if}

            {hook run='profile_whois_privat_item' oUserProfile=$oUserProfile}
        </table>
    </div>
</section>

{assign var="aUserFieldContactValues" value=$oUserProfile->getUserFieldValues(true,array('social'))}
{if $aUserFieldContactValues}
    <section class="block">
        <header class="block-header">
            <h2>{$aLang.profile_contacts}</h2>
        </header>
        <div class="block-content colorful-links">
            <table class="table table-profile-info">
                {foreach from=$aUserFieldContactValues item=oField}
                    <tr>
                        <td class="cell-label"><i
                                    class="icon-contact icon-contact-{$oField->getName()}"></i> {$oField->getTitle()|escape:'html'}
                            :
                        </td>
                        <td>{$oField->getValue(true,true)}</td>
                    </tr>
                {/foreach}
            </table>
        </div>
    </section>
{/if}

<section class="block">
    <header class="block-header">
        <h3>{$aLang.profile_activity}</h3>
    </header>
    <div class="block-content colorful-links">
        <table class="table table-profile-info">

            {if $oConfig->GetValue('general.reg.invite') and $oUserInviteFrom}
                <tr>
                    <td class="cell-label">{$aLang.profile_invite_from}:</td>
                    <td>
                        <a href="{$oUserInviteFrom->getUserWebPath()}">{$oUserInviteFrom->getLogin()}</a>&nbsp;
                    </td>
                </tr>
            {/if}


            {if $oConfig->GetValue('general.reg.invite') and $aUsersInvite}
                <tr>
                    <td class="cell-label">{$aLang.profile_invite_to}:</td>
                    <td>
                        {foreach from=$aUsersInvite item=oUserInvite}
                            <a href="{$oUserInvite->getUserWebPath()}">{$oUserInvite->getLogin()}</a>
                            &nbsp;
                        {/foreach}
                    </td>
                </tr>
            {/if}


            {if $aBlogsOwner}
                <tr>
                    <td class="cell-label">{$aLang.profile_blogs_self}:</td>
                    <td>
                        {foreach from=$aBlogsOwner item=oBlog name=blog_owner}
                            <a
                            href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_owner.last}, {/if}
                        {/foreach}
                    </td>
                </tr>
            {/if}


            {if $aBlogAdministrators}
                <tr>
                    <td class="cell-label">{$aLang.profile_blogs_administration}:</td>
                    <td>
                        {foreach from=$aBlogAdministrators item=oBlogUser name=blog_user}
                            {assign var="oBlog" value=$oBlogUser->getBlog()}
                            <a
                            href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
                        {/foreach}
                    </td>
                </tr>
            {/if}


            {if $aBlogModerators}
                <tr>
                    <td class="cell-label">{$aLang.profile_blogs_moderation}:</td>
                    <td>
                        {foreach from=$aBlogModerators item=oBlogUser name=blog_user}
                            {assign var="oBlog" value=$oBlogUser->getBlog()}
                            <a
                            href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
                        {/foreach}
                    </td>
                </tr>
            {/if}


            {if $aBlogUsers}
                <tr>
                    <td class="cell-label">{$aLang.profile_blogs_join}:</td>
                    <td>
                        {foreach from=$aBlogUsers item=oBlogUser name=blog_user}
                            {assign var="oBlog" value=$oBlogUser->getBlog()}
                            <a
                            href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
                        {/foreach}
                    </td>
                </tr>
            {/if}


            {hook run='profile_whois_activity_item' oUserProfile=$oUserProfile}


            <tr>
                <td class="cell-label">{$aLang.profile_date_registration}:</td>
                <td>{date_format date=$oUserProfile->getDateRegister()}{if $oUserCurrent && ($oUserCurrent->isAdministrator() || $oUserCurrent->isGlobalModerator())}
                        ({$oUserProfile->getIpRegister()})
                    {/if}</td>
            </tr>


            {if $oSession}
                <tr>
                    <td class="cell-label">{$aLang.profile_date_last}:</td>
                    <td>{date_format date=$oSession->getDateLast()}{if $oUserCurrent && ($oUserCurrent->isAdministrator() || $oUserCurrent->isGlobalModerator())}
                            ({$oSession->getIpLast()})
                        {/if}</td>
                </tr>
            {/if}
        </table>
    </div>
</section>
{if $oUserCurrent && $oUserCurrent->getId()!=$oUserProfile->getId() && ($oUserCurrent->isAdministrator() || $oUserCurrent->isGlobalModerator()) && !$oUserProfile->isBanned()}
    <section class="block">
        <header class="block-header">
            <h3>Забанить</h3>
        </header>
        <div class="block-content">
            <form method="post" action="/ajax/ban/" onsubmit="ls.user.banUser(this);return false;">
                <input name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" type="hidden">

                <input name="iUserId" value="{$oUserProfile->getId()}" type="hidden">
                <input name="iUnban" value="0" type="hidden">
                <input name="iBanUnlim" value="1" type="hidden">

                <label for="iBanHours">Часы бана:</label>
                <input type="numeric" name="iBanHours">

                <label for="ban_comment">Комментарий</label>
                <input name="sBanComment" id="ban_comment" maxlength="255" type="text"><br/>
                <button type="submit" name="adm_action_submit" class="button button-primary">Забанить</button>
            </form>
        </div>
    </section>
{elseif $oUserCurrent && $oUserCurrent->getId()!=$oUserProfile->getId() && ($oUserCurrent->isAdministrator() || $oUserCurrent->isGlobalModerator()) && $oUserProfile->isBanned()}
    <section class="block">
        <header class="block-header">
            <h3>Разбанить</h3>
        </header>
        <div class="block-content">
            <form method="post" action="/ajax/ban/" onsubmit="ls.user.banUser(this);return false;">
                <input name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" type="hidden">

                <input name="iUnban" value="1" type="hidden">
                <input name="iUserId" value="{$oUserProfile->getId()}" type="hidden">
                Забанен {if $oUserProfile->getBanUnlim()}бессрочно{else}до
                <time>{$oUserProfile->getBanLine()}</time>{/if}<br/><br/>
                {assign var="oBanModer" value=$oUserProfile->getBanModer()}
                Забанивший: <a href="{$oBanModer->getUserWebPath()}">{$oBanModer->getLogin()}</a><br/><br/>
                Комментарий: {$oUserProfile->getBanComment()}<br/><br/>
                <button type="submit" name="adm_action_submit" class="button button-primary">Разбанить</button>
            </form>
        </div>
    </section>
{/if}