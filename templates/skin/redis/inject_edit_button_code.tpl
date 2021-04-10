<script type="text/javascript">
    jQuery(document).ready(function ($) {
        ls.comments.options.edit_button_code = '<button type="submit" class="button button-primary" name="submit_edit" id="comment-button-submit-edit" onclick="ls.comments.edit(\'form_comment\',{$iTargetId},\'{$sTargetType}\'); return false;">{$aLang.editcomment.edit_button_title}</button>';
    {assign var=cv value=$oConfig->Get('module.editcomment.show_history_button')}
    {if ($cv!=0 && $oUserCurrent->isAdministrator()) || ($cv==1) || ($cv==2 && (in_array($oUserCurrent->getId(),$oConfig->Get('module.editcomment.comment_editors'))))}
        ls.comments.options.history_button_code = '<button type="button" class="button" name="submit_history" id="comment-button-history" onclick="ls.comments.showHistory(); return false;">{$aLang.editcomment.history_button_title}</button>';
        {else}
        ls.comments.options.history_button_code = '';
    {/if}
    {if $oConfig->Get('module.editcomment.show_cancel_button')}
        ls.comments.options.cancel_button_code = '<button type="button" class="button" name="submit_cancel" id="comment-button-cancel" onclick=ls.comments.cancelEditComment(); return false;">{$aLang.editcomment.cancel_button_title}</button>';
        {else}
        ls.comments.options.cancel_button_code = '';
    {/if}
    });
</script>

