{add_block group='toolbar' name='toolbar_comment.tpl'
	aPagingCmt=$aPagingCmt
	iTargetId=$iTargetId
	sTargetType=$sTargetType
	iMaxIdComment=$iMaxIdComment
}

{hook run='comment_tree_begin' iTargetId=$iTargetId sTargetType=$sTargetType}

<div class="comments" id="comments">
	<header class="comments-header">
		<h3><span id="count-comments">{$iCountComment}</span> {$iCountComment|declension:$aLang.comment_declension:'russian'}</h3>

		{if $bAllowSubscribe and $oUserCurrent}
			<span class="checkbox">
                <span>
                  	<input {if $oSubscribeComment and $oSubscribeComment->getStatus()}checked="checked"{/if} type="checkbox" id="comment_subscribe" class="input-checkbox" onchange="ls.subscribe.toggle('{$sTargetType}_new_comment','{$iTargetId}','',this.checked);" />
					<label for="comment_subscribe">{$aLang.comment_subscribe}</label>
                </span>
              </span>
		{/if}

		<span class="checkbox"><span>
		<input id="autoload" type="checkbox"><label for="autoload">Автообновление</label></input></span></span>


		<a name="comments"></a>
	</header>
<div id="comments-tree">
	<div class="showbox">
  <div class="loader">
    <svg class="circular" viewBox="25 25 50 50">
      <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
    </svg>
  </div>
</div>
	<script>
		{if $oUserCurrent}
		{if ($oUserCurrent->isGlobalModerator() and $oTopic->getBlog()->getType() == "open") or $oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or ($oTopic->getUserId() === $oUserCurrent->getId() and !$oTopic->isControlLocked()) or $oBlog->getOwnerId()==$oUserCurrent->getId()}
		IS_ADMIN = true;
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

		<h4 class="reply-header" id="comment_id_0" data-level=-1 >
			<a href="#" class="link-dotted" onclick="ls.comments.toggleCommentForm(0); return false;">{$sNoticeCommentAdd}</a>
		</h4>


		<div id="reply" class="reply">
			<form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
				{hook run='form_add_comment_begin'}

				<textarea name="comment_text" id="form_comment_text" class="mce-editor markitup-editor input-width-full"></textarea>

				{hook run='form_add_comment_end'}

				<button type="submit" name="submit_comment"
						id="comment-button-submit"
						onclick="ls.comments.add('form_comment',{$iTargetId},'{$sTargetType}'); return false;"
						class="button button-primary">{$aLang.comment_add}</button>
				<button type="button" onclick="ls.comments.preview();" class="button">{$aLang.comment_preview}</button>

				<input type="hidden" name="reply" value="0" id="form_comment_reply" />
				<input type="hidden" name="cmt_target_id" value="{$iTargetId}" />
			</form>
		</div>
	{else}
		{$aLang.comment_unregistered}
	{/if}
{/if}
