<section class="block">
	<header class="block-header">
		<h3>{$aLang.block_tags}</h3>
	</header>
	
	
	<div class="block-content">
		<div class="js-block-tags-content" data-type="all">
			{if $aTags}
				<ul class="tag-cloud word-wrap">
					{foreach from=$aTags item=oTag}
						<li><a class="tag-size-{$oTag->getSize()}" href="{router page='tag'}{$oTag->getText()|escape:'url'}/">{$oTag->getText()|escape:'html'}</a></li>
					{/foreach}
				</ul>
			{else}
				<div class="notice-empty">{$aLang.block_tags_empty}</div>
			{/if}
		</div>

		{if $oUserCurrent}
			<div class="js-block-tags-content" data-type="user" style="display: none;">
				{if $aTagsUser}
					<ul class="tag-cloud word-wrap">
						{foreach from=$aTagsUser item=oTag}
							<li><a class="tag-size-{$oTag->getSize()}" href="{router page='tag'}{$oTag->getText()|escape:'url'}/">{$oTag->getText()|escape:'html'}</a></li>
						{/foreach}
					</ul>
					{else}
					<div class="notice-empty">{$aLang.block_tags_empty}</div>
				{/if}
			</div>
		{/if}
	</div>
</section>