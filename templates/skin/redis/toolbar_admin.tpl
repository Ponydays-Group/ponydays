{if $oUserCurrent and $oUserCurrent->isAdministrator()}
<div class="rightbar-item">
	<a href="{router page='admin'}" title="{$aLang.admin_title}">
		<i class="material-icons">settings</i>
	</a>
</div>
{/if}
