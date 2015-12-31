<li class="item-messages">
	<a href="{router page='feedbacks'}">
		<i class="ion-speakerphone"></i> 
		<span>{$aLang.plugin.feedbacks.answers_menu}</span>
		{if $iUnreadActionsCount > 0} 
		<div class="new">+{$iUnreadActionsCount}</div>
		{/if}		
	</a>
</li>
