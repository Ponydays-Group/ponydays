{assign var="sidebarPosition" value='left'}
{include file='header.tpl'}


{include file='actions/ActionProfile/profile_top.tpl'}
{include file='menu.settings.tpl'}


{hook run='settings_behavior_begin'}

<form action="{router page='settings'}behavior/" method="POST" enctype="multipart/form-data" id="behavior-form">
	{hook run='form_settings_behavior_begin'}

	<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	
	<label for="min_comment_width">{$aLang.min_comment_width_behavior_label}</label>
	<input type="number" min=100 max=700 value=250 name="min_comment_width" class="input-width-300" data-save=1 /><br/>

	<label for="float_window_wait">Ожидание до отображения всплывающего окна (мс)</label>
	<input type="number" min=100 max=99999 value=1000 name="float_window_wait" class="input-width-300" data-save=1 /><br/>
	
	<fieldset>
<span class="checkbox">
	<span>
		<input type="checkbox" id="square_avatars" name="square_avatars" data-default=0 data-save=1 class="input-checkbox"/> <label
				for="square_avatars">Включить квадратные аватарки</label></span></span></fieldset>
	<fieldset>
		<span class="checkbox"><span>
			<input type="checkbox" id="notice_talk_reply" name="notice_talk_reply" data-default=1 data-save=1 class="input-checkbox" />
			<label for="notice_talk_reply">Уведомления о новых комментариях в ЛС</label>
		</span></span>
		<span class="checkbox"><span>
			<input type="checkbox" id="notice_reply" name="notice_reply" data-default=1 data-save=1 class="input-checkbox" />
			<label for="notice_reply">Уведомления об ответах</label>
		</span></span>
		<span class="checkbox"><span>
			<input type="checkbox" id="notice_comment_edit" name="notice_comment_edit" data-default=1 data-save=1 class="input-checkbox" />
			<label for="notice_comment_edit">Уведомления о редактировании ваших комментариев</label>
		</span></span>
		<span class="checkbox"><span>
			<input type="checkbox" id="notice_comment_delete" name="notice_comment_delete" data-default=1 data-save=1 class="input-checkbox" />
			<label for="notice_comment_delete">Уведомления об удалении/восстановлении ваших комментариев</label>
		</span></span>
		<span class="checkbox"><span>
			<input type="checkbox" id="notice_vote" name="notice_vote" data-default=1 data-save=1 class="input-checkbox" />
			<label for="notice_vote">Уведомления о голосовании за ваши посты/комментарии</label>
		</span></span>
	</fieldset>
	<fieldset>
		<span class="checkbox"><span>
			<input type="checkbox" id="sound_notice" name="sound_notice" data-default=1 data-save=1 class="input-checkbox" />
			<label for="sound_notice">Звук в уведомлениях</label>
		</span></span>
		<label for="notice_sound_url">URL звука в уведомлениях</label>
		<input name="notice_sound_url" id="notice_sound_url" class="input-width-300" data-default="http://freesound.org/data/previews/245/245645_1038806-lq.mp3" data-save=1 /><br/>
	</fieldset>
	
    {literal}
	<script>
	    $('#behavior-form  [data-save=1]').each(function(k,v){
	    	if (v.type=="checkbox") {
	    		console.log("Chуckbox:", localStorage.getItem(v.name))
	    		v.checked = parseInt(localStorage.getItem(v.name))
				if (localStorage.getItem(v.name)==null) {
	    		    v.checked = parseInt(v.dataset.default)
                }
	    		return
	    	}
	    	v.value = localStorage.getItem(v.name) || v.dataset.default
	    });
	    
	    function saveBehaviorSettings(e) {
	        $('#behavior-form  [data-save=1]').each(function(k,v){
	        	if (v.type=="checkbox") {
	        		console.log("Chackbox:", v.checked)
	    			localStorage.setItem(v.name, v.checked?1:0)
	    			return
	    		} else {
                    localStorage.setItem(v.name, v.value || v.dataset.default)
                    console.log(v.name, v.value, v.dataset.default)
                }
	        })
	        ls.msg.notice('','Настройки сохранены')
	        return false;
	    }
	</script>
	{/literal}
	
	<button type="submit" name="submit_settings_behavior" onclick="saveBehaviorSettings(); return false;" class="button button-primary">{$aLang.settings_profile_submit}</button>
</form>

{hook run='settings_behavior_end'}

{include file='footer.tpl'}