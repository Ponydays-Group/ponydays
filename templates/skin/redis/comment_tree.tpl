{add_block group='toolbar' name='toolbar_comment.tpl'
aPagingCmt=$aPagingCmt
iTargetId=$iTargetId
sTargetType=$sTargetType
iMaxIdComment=$iMaxIdComment
}
{hook run='comment_tree_begin' iTargetId=$iTargetId sTargetType=$sTargetType}

<div class="comments {if $bEnableCommentsVoteInfo}vote-info-enable{/if}" id="comments">
    <header class="comments-header">
        <h3>
            <span id="count-comments">{$iCountComment}</span> {$iCountComment|declension:$aLang.comment_declension:'russian'}
        </h3>

        {if $bAllowSubscribe and $oUserCurrent}
            <span class="checkbox">
                <span>
                  	<input {if $oSubscribeComment and $oSubscribeComment->getStatus()}checked="checked"{/if}
                           type="checkbox" id="comment_subscribe" class="input-checkbox"
                           onchange="ls.subscribe.toggle('{$sTargetType}_new_comment','{$iTargetId}','',this.checked);"/>
					<label for="comment_subscribe">{$aLang.comment_subscribe}</label>
                </span>
              </span>
        {/if}

        <span class="checkbox"><span>
		<input id="autoload" type="checkbox"><label for="autoload">Автообновление</label></input></span></span>


        <a name="comments"></a>
    </header>
    <div id="comments-tree">
        {foreach from=$aComments item=oComment name=rublist}
            {assign var="cmtlevel" value=$oComment->getLevel()}
            {if $sTargetType!="talk"}
                {assign var="oBlog" value=$oTopic->getBlog()}
                {assign var="bAdmin" value=($oUserCurrent&&(($oUserCurrent->isGlobalModerator() and $oBlog->getType() == "open") or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $oBlog->getOwnerId()==$oUserCurrent->getId() or $oUserCurrent->isAdministrator()))}
            {/if}

            {if $cmtlevel>$oConfig->GetValue('module.comment.max_tree')}
                {assign var="cmtlevel" value=$oConfig->GetValue('module.comment.max_tree')}
            {/if}

            {if $nesting < $cmtlevel}
            {elseif $nesting > $cmtlevel}
                {section name=closelist1  loop=$nesting-$cmtlevel+1}{*</div>*}{/section}
            {elseif not $smarty.foreach.rublist.first}
                {*</div>*}
            {/if}

            {*<div class="comment-wrapper" id="comment_wrapper_id_{$oComment->getId()}">*}

            {include file='comment.tpl'}
            {assign var="nesting" value=$cmtlevel}
            {if $smarty.foreach.rublist.last}
                {section name=closelist2 loop=$nesting+1}{/section}
            {/if}
        {/foreach}
        <script>
            var targetType = "{$sTargetType}";
            var targetId = {$iTargetId};
            {if $sTargetType!="talk"}
            {assign var="oBlog" value=$oTopic->getBlog()}
            {if $oUserCurrent}
            {if (($oUserCurrent->isGlobalModerator() and $oBlog->getType() == "open") or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $oBlog->getOwnerId()==$oUserCurrent->getId()) }
            IS_ADMIN = 1;
            {/if}
            {/if}
            {/if}
            {literal}
            $(document).ready(ls.comments.renderComments)
            {/literal}
        </script>

    </div>
</div>

{include file='comment_paging.tpl' aPagingCmt=$aPagingCmt}

{hook run='comment_tree_end' iTargetId=$iTargetId sTargetType=$sTargetType}

{if $bAllowNewComment}
    {$sNoticeNotAllow}
{else}
    {if $oUserCurrent}
        {include file='editor.tpl' sImgToLoad='form_comment_text' sSettingsTinymce='ls.settings.getTinymceComment()' sSettingsMarkitup='ls.settings.getMarkitupComment()'}
        <h4 class="reply-header" id="comment_id_0" data-level=-1>
            <a href="#" class="link-dotted"
               onclick="ls.comments.toggleCommentForm(0); return false;">{$sNoticeCommentAdd}</a>
        </h4>
        <div id="reply" class="reply">
            <form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
                {hook run='form_add_comment_begin'}

                <textarea name="comment_text" id="form_comment_text"
                          class="mce-editor markitup-editor input-width-full"></textarea>
                <div id="form_comment_mark_wrapper">
                    <span class="checkbox">
                        <span>
		                    <input id="form_comment_mark" name="form_comment_mark" type="checkbox" />
                            <label for="form_comment_mark">Экспериментальная разметка</label>
                        </span>
                    </span>
                </div>

                {hook run='form_add_comment_end'}

                <button type="submit" name="submit_comment"
                        id="comment-button-submit"
                        onclick="ls.comments.add('form_comment',{$iTargetId},'{$sTargetType}'); return false;"
                        class="button button-primary">{$aLang.comment_add}</button>
                <button type="button" onclick="ls.comments.preview();" class="button">{$aLang.comment_preview}</button>

                <input type="hidden" name="reply" value="0" id="form_comment_reply"/>
                <input type="hidden" name="cmt_target_id" value="{$iTargetId}"/>
            </form>
        </div>
    {else}
        {$aLang.comment_unregistered}
    {/if}
{/if}
