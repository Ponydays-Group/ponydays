<script type="text/javascript">
	$(function(){
		$('#submit_topic_publish').click(function(){
			var iVkPostId = {if !$sVkPostId}null{else}{$sVkPostId}{/if};
			if (iVkPostId && $('#publish_in_vk').attr('checked') == 'checked'){
				return confirm("Этот топик уже публиковался в группу\r\nОпубликовать повторно?");
			}
		});
	});
</script>
<p>
	<label>
		<input type="checkbox" id="publish_in_vk" name="publish_in_vk" class="input-checkbox" value="1" {if $oConfig->get('plugin.postingingroups.vk.published_default') == 1 && !$sVkPostId} checked="checked"{/if}>
		{$aLang.plugin.postingingroups.field.publish_in_vk.label}
	</label>
	<small class="note">{$aLang.plugin.postingingroups.field.publish_in_vk.note}</small>
</p>