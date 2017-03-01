{include file='header.tpl'}

{if $oConfig->get('plugin.postingingroups.vk.access_token')}
	{$aLang.plugin.postingingroups.token_ok}
	<br/>
	<br/>
{/if}
<a class="button button-primary"
   href="http://api.vk.com/oauth/authorize?client_id={$oConfig->get('plugin.postingingroups.vk.app_id')}&scope={$oConfig->get('plugin.postingingroups.vk.app_scope')}&amp;redirect_uri=http://api.vk.com/blank.html&amp;response_type=token"
   target='_blank'>{$aLang.plugin.postingingroups.button.token_new}</a>
<br/><br/>

{$aLang.plugin.postingingroups.token_info}

<script type="text/javascript">
	$(function(){
		$('#accesss_token_cut').click(function(){
			var sAT = $('#accesss_token_url').val().match(/access_token=([a-z0-9]+)/);
			log(sAT);
			log(sAT == null);
			if (sAT !== null){
				prompt('Access_token:',sAT[1]);
			} else {
				alert('{$aLang.plugin.postingingroups.error.no_access_token}');
			}
		});
	});
</script>
<label style="display:block; clear: both; margin-top: 40px;">
	{$aLang.plugin.postingingroups.field.accesss_token_url.label}
	<input type="text" class="input-text input-width-full" id="accesss_token_url" />
	<small class="note">{$aLang.plugin.postingingroups.field.accesss_token_url.note}</small>
</label>
<button class="button" id="accesss_token_cut">{$aLang.plugin.postingingroups.button.token_cut}</button>

{include file='footer.tpl'}