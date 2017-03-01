<li class="userbar-item userbar-item-messages {if $sAction=='feedbacks'}active{/if}">
		<a href="{router page='feedbacks'}">
			<div class="holder">
				<i class="sideico"></i>
			</div>{$aLang.plugin.feedbacks.answers_menu}</a>
			{if $iUnreadActionsCount > 0}	
				<a href="{router page='feedbacks'}" class="userbar-item-messages-number">	+{$iUnreadActionsCount} </a>
			{/if}
</li>


			
			