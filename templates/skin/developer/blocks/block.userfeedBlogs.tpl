{if $oUserCurrent}
	<section class="block block-type-activity hidden">
		<header class="block-header">
			<h3>{$aLang.userfeed_block_blogs_title}</h3>
		</header>
		
		<div class="block-content">

				<span class="checkbox">
                <span>
                  <input type="checkbox" tabindex="26" name="r4" id="r9" checked="">
                  <label for="r9">A leg</label>
                </span>
                <span>
                  <input type="checkbox" tabindex="27" name="r4" id="r10">
                  <label for="r10">B leg</label>
                </span>
              </span>

			<small class="note">{$aLang.userfeed_settings_note_follow_blogs}</small>

			{if count($aUserfeedBlogs)}
				<ul class="stream-settings-blogs">
					{foreach from=$aUserfeedBlogs item=oBlog}
						{assign var=iBlogId value=$oBlog->getId()}
						<li>
							<span class="checkbox">
    						<span>
								<input class="userfeedBlogCheckbox input-checkbox"
									type="checkbox"
									{if isset($aUserfeedSubscribedBlogs.$iBlogId)} checked="checked"{/if}
									onClick="if (jQuery(this).prop('checked')) { ls.userfeed.subscribe('blogs',{$iBlogId}) } else { ls.userfeed.unsubscribe('blogs',{$iBlogId}) } "
			   						name="subscribe_{$iBlogId}"
									/>
								<label for="subscribe_{$iBlogId}"><a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a></label>
							</span>
							</span>
						</li>
					{/foreach}
				</ul>
			{else}
				<small class="notice-empty">{$aLang.userfeed_no_blogs}</small>
			{/if}
		</div>
	</section>
{/if}