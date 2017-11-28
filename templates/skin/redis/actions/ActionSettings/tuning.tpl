{assign var="sidebarPosition" value='left'}
{include file='header.tpl'}


{include file='actions/ActionProfile/profile_top.tpl'}
{include file='menu.settings.tpl'}


{hook run='settings_tuning_begin'}

<form action="{router page='settings'}tuning/" method="POST" enctype="multipart/form-data">
	{hook run='form_settings_tuning_begin'}

	<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	
	<fieldset>
		<legend>{$aLang.settings_tuning_notice}</legend>

		<span class="checkbox"><span><input {if $oUserCurrent->getSettingsNoticeNewTopic()}checked{/if} type="checkbox" id="settings_notice_new_topic" name="settings_notice_new_topic" value="1" class="input-checkbox" /> <label for="settings_notice_new_topic">{$aLang.settings_tuning_notice_new_topic}</label></span></span>
		<span class="checkbox"><span><input {if $oUserCurrent->getSettingsNoticeNewComment()}checked{/if} type="checkbox" id="settings_notice_new_comment" name="settings_notice_new_comment" value="1" class="input-checkbox" /> <label for="settings_notice_new_comment">{$aLang.settings_tuning_notice_new_comment}</label></span></span>
		<span class="checkbox"><span><input {if $oUserCurrent->getSettingsNoticeNewTalk()}checked{/if} type="checkbox" id="settings_notice_new_talk" name="settings_notice_new_talk" value="1" class="input-checkbox" /> <label for="settings_notice_new_talk">{$aLang.settings_tuning_notice_new_talk}</label></span></span>
		<span class="checkbox"><span><input {if $oUserCurrent->getSettingsNoticeReplyComment()}checked{/if} type="checkbox" id="settings_notice_reply_comment" name="settings_notice_reply_comment" value="1" class="input-checkbox" /> <label for="settings_notice_reply_comment">{$aLang.settings_tuning_notice_reply_comment}</label></span></span>
		<span class="checkbox"><span><input {if $oUserCurrent->getSettingsNoticeNewFriend()}checked{/if} type="checkbox" id="settings_notice_new_friend" name="settings_notice_new_friend" value="1" class="input-checkbox" /> <label for="settings_notice_new_friend">{$aLang.settings_tuning_notice_new_friend}</label></span></span>
	</fieldset>

	<fieldset>
		<legend>{$aLang.settings_tuning_general}</legend>

		<label>{$aLang.settings_tuning_general_timezone}:
			<select name="settings_general_timezone" class="input-width-400">
			{foreach from=$aTimezoneList item=sTimezone}
				<option value="{$sTimezone}" {if $_aRequest.settings_general_timezone==$sTimezone}selected="selected"{/if}>{$aLang.timezone_list[$sTimezone]}</option>
			{/foreach}
			</select>
		</label>
	</fieldset>
	
	{hook run='form_settings_tuning_end'}
	
	<button type="submit" name="submit_settings_tuning" class="button button-primary">{$aLang.settings_profile_submit}</button>
</form>

{hook run='settings_tuning_end'}

{include file='footer.tpl'}