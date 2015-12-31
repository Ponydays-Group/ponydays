{literal}
<script>
jQuery(document).ready(function ($) {
    $('#settings_talk_bell').click(function(){
        ls.ajax(aRouter['talkbell']+'ajaxedittuning/', {security_ls_key:LIVESTREET_SECURITY_KEY}, function (result) {
            if (result.bStateError) {
                ls.msg.error(null, result.sMsg);
            } else {

            }
        });
    });
});          
</script>
{/literal}
<br />
<h3>{$aLang.plugin.talkbell.tuning_title}</h3>
<label for="settings_talk_bell">
    <input {if $oUserCurrent->getUserSettingsTalkBell()==1}checked{/if} type="checkbox" id="settings_talk_bell" name="settings_talk_bell" value="1" class="checkbox" />
    {$aLang.plugin.talkbell.setting}</label>
<br />