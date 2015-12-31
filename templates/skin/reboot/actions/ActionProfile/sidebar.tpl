{hook run='profile_sidebar_begin' oUserProfile=$oUserProfile}

<section class="block block-type-profile">
	<div class="profile-photo-wrapper">
		<div class="status {if $oUserProfile->isOnline()}status-online{else}status-offline{/if}">{if $oUserProfile->isOnline()}{$aLang.user_status_online}{else}{$aLang.user_status_offline}{/if}</div>
		<a href="{$oUserProfile->getUserWebPath()}"><img src="{$oUserProfile->getProfileFotoPath()}" alt="photo" class="profile-photo" id="foto-img" /></a>
	</div>
	
	{if $sAction=='settings' and $oUserCurrent and $oUserCurrent->getId() == $oUserProfile->getId()}
		<script type="text/javascript">
			jQuery(function($){
				$('#foto-upload').file({ name:'foto' }).choose(function(e, input) {
					ls.user.uploadFoto(null,input);
				});
			});
		</script>
		
		<p class="upload-photo">
			<a href="#" id="foto-upload" class="link-dotted">{if $oUserCurrent->getProfileFoto()}{$aLang.settings_profile_photo_change}{else}{$aLang.settings_profile_photo_upload}{/if}</a>&nbsp;&nbsp;&nbsp;
			<a href="#" id="foto-remove" class="link-dotted" onclick="return ls.user.removeFoto();" style="{if !$oUserCurrent->getProfileFoto()}display:none;{/if}">{$aLang.settings_profile_foto_delete}</a>
		</p>

		<div class="modal" id="foto-resize">
			<header class="modal-header">
				<h3>{$aLang.uploadimg}</h3>
			</header>
			
			<div class="modal-content">
				<img src="" alt="" id="foto-resize-original-img"><br />
				<button type="submit" class="button button-primary" onclick="return ls.user.resizeFoto();">{$aLang.settings_profile_avatar_resize_apply}</button>
				<button type="submit" class="button" onclick="return ls.user.cancelFoto();">{$aLang.settings_profile_avatar_resize_cancel}</button>
			</div>
		</div>
	{/if}
</section>



{if $oUserCurrent && $oUserCurrent->getId()!=$oUserProfile->getId()}
	<script type="text/javascript">
		jQuery(function($){
			ls.lang.load({lang_load name="profile_user_unfollow,profile_user_follow"});
		});
	</script>

	<section class="block block-type-profile-actions">
		<div class="block-content">
			<ul class="profile-actions" id="profile_actions">
				{include file='actions/ActionProfile/friend_item.tpl' oUserFriend=$oUserProfile->getUserFriend()}
				<li><a href="{router page='talk'}add/?talk_users={$oUserProfile->getLogin()}">{$aLang.user_write_prvmsg}</a></li>						
				<li>
					<a href="#" onclick="ls.user.followToggle(this, {$oUserProfile->getId()}); return false;" class="{if $oUserProfile->isFollow()}followed{/if}">
						{if $oUserProfile->isFollow()}{$aLang.profile_user_unfollow}{else}{$aLang.profile_user_follow}{/if}
					</a>
				</li>
				{hook run='profile_sidebar_show' oUserProfile=$oUserProfile}
			</ul>
		</div>
	</section>
{/if}	



{if $oUserCurrent && $oUserCurrent->getId() != $oUserProfile->getId()}
	<section class="block block-type-profile-note">
		{if $oUserNote}
			<script type="text/javascript">
				ls.usernote.sText = {json var = $oUserNote->getText()};
			</script>
		{/if}

		<div id="usernote-note" class="profile-note" {if !$oUserNote}style="display: none;"{/if}>
			<p id="usernote-note-text">
				{if $oUserNote}
					{$oUserNote->getText()}
				{/if}
			</p>
			
			<ul class="actions">
				<li><a href="#" onclick="return ls.usernote.showForm();" class="link-dotted">{$aLang.user_note_form_edit}</a></li>
				<li><a href="#" onclick="return ls.usernote.remove({$oUserProfile->getId()});" class="link-dotted">{$aLang.user_note_form_delete}</a></li>
			</ul>
		</div>
		
		<div id="usernote-form" style="display: none;">
			<p><textarea rows="4" cols="20" id="usernote-form-text" class="input-text input-width-full"></textarea></p>
			<button type="submit" onclick="return ls.usernote.save({$oUserProfile->getId()});" class="button button-primary">{$aLang.user_note_form_save}</button>
			<button type="submit" onclick="return ls.usernote.hideForm();" class="button">{$aLang.user_note_form_cancel}</button>
		</div>
		
		<a href="#" onclick="return ls.usernote.showForm();" id="usernote-button-add" class="link-dotted" {if $oUserNote}style="display:none;"{/if}>{$aLang.user_note_add}</a>
	</section>
{/if}

{hook run='profile_sidebar_menu_before' oUserProfile=$oUserProfile}

<section class="block block-type-profile-nav">
	<ul class="nav nav-pills nav-profile">
		{hook run='profile_sidebar_menu_item_first' oUserProfile=$oUserProfile}
		<li {if $sAction=='profile' && ($aParams[0]=='whois' or $aParams[0]=='')}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}">{$aLang.user_menu_profile_whois}</a></li>
		<li {if $sAction=='profile' && $aParams[0]=='wall'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}wall/">{$aLang.user_menu_profile_wall}{if ($iCountWallUser)>0} ({$iCountWallUser}){/if}</a></li>
		<li {if $sAction=='profile' && $aParams[0]=='created'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}created/topics/">{$aLang.user_menu_publication}{if ($iCountCreated)>0} ({$iCountCreated}){/if}</a></li>
		<li {if $sAction=='profile' && $aParams[0]=='favourites'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}favourites/topics/">{$aLang.user_menu_profile_favourites}{if ($iCountFavourite)>0} ({$iCountFavourite}){/if}</a></li>
		<li {if $sAction=='profile' && $aParams[0]=='friends'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}friends/">{$aLang.user_menu_profile_friends}{if ($iCountFriendsUser)>0} ({$iCountFriendsUser}){/if}</a></li>
		<li {if $sAction=='profile' && $aParams[0]=='stream'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}stream/">{$aLang.user_menu_profile_stream}</a></li>
		
		{if $oUserCurrent and $oUserCurrent->getId() == $oUserProfile->getId()}
			<li {if $sAction=='talk'}class="active"{/if}><a href="{router page='talk'}">{$aLang.talk_menu_inbox}{if $iUserCurrentCountTalkNew} ({$iUserCurrentCountTalkNew}){/if}</a></li>
			<li {if $sAction=='settings'}class="active"{/if}><a href="{router page='settings'}">{$aLang.settings_menu}</a></li>
			{hook run='athead'}
			{hook run='atmenu'}
		{/if}
		{hook run='profile_sidebar_menu_item_last' oUserProfile=$oUserProfile}
	</ul>
</section>
{if $oUserCurrent and $oUserCurrent->getId() != $oAceUserProfile->getId() and ($oUserCurrent->isAdministrator() or $oUserCurrent->isGlobalModerator())}
<section class="block">
<header class="block-header"><h3>Бан</h3></header>
<div class="block-content">
{if !$oAceUserProfile->IsBannedByLogin()}
                    <form method="post" action="https://reboot.lunavod.ru/api/ban/" class="well well-small">
                        <br>
                        <input name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" type="hidden">

                        <input name="ban_login" value="{$oUserProfile->getLogin()}" type="hidden">

                        <label class="radio">
                            <input name="ban_period" value="days" checked="" type="radio">
                            Бан на
                            <input name="ban_days" id="ban_days" class="num1" type="text"> дней
                        </label>

                        <label class="radio">
                            <input name="ban_period" value="unlim" type="radio">
                            Бан бессрочный
                        </label>

                        <label for="ban_comment">Комментарий</label>
                        <input name="ban_comment" id="ban_comment" maxlength="255" type="text">
			<br>
                        <input name="adm_user_ref" value="https://reboot.lunavod.ru/admin/users/" type="hidden">
                        <input name="adm_user_action" value="adm_ban_user" type="hidden">
                        <button type="submit" name="adm_action_submit" class="btn btn-primary">Забанить</button>
                    </form>
                </div>
</section>
{else}
<div class="alert alert-block">
    {$oLang->adm_ban_upto}
    : {if $oAceUserProfile->getBanLine()}{$oAceUserProfile->getBanLine()}{else}{$oLang->adm_ban_unlim}{/if}
    <br/>
    <strong>{$oUserProfile->getBanComment()}</strong>
</div>
<form method="post" action="https://reboot.lunavod.ru/api/ban/" class="well well-small">
                        <input name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" type="hidden">

                        <input name="ban_login" value="{$oUserProfile->getLogin()}" type="hidden">
<input name="clear" type="hidden" value="true">
<button type="submit" name="adm_action_submit" class="btn btn-primary">Разбанить</button>
</form>
</div>
</section>
{/if}
{/if}
{hook run='profile_sidebar_end' oUserProfile=$oUserProfile}
