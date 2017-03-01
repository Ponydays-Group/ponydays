<nav id="nav">
	<ul class="nav nav-main">
		<li {if $sMenuHeadItemSelect=='blog'}class="active"{/if}><a href="{cfg name='path.root.web'}">{$aLang.topic_title}</a></li>
		<li {if $sMenuHeadItemSelect=='blogs'}class="active"{/if}><a href="{router page='blogs'}">{$aLang.blogs}</a></li>
		<li {if $sMenuHeadItemSelect=='people'}class="active"{/if}><a href="{router page='people'}">{$aLang.people}</a></li>
		<li {if $sMenuHeadItemSelect=='stream'}class="active"{/if}><a href="{router page='stream'}">{$aLang.stream_menu}</a></li>
		{if $oUserCurrent}
		{hook run='userbar_nav'}
			<li><a href="{router page='topic'}add/" class="write" id="modal_write_show">{$aLang.block_create}</a></li>
		{else}
			<li><a href="{router page='login'}" class="js-login-form-show">{$aLang.user_login_submit}</a></li>
			<li><a href="{router page='registration'}" class="js-registration-form-show">{$aLang.registration_submit}</a></li>
		{/if}
		{hook run='main_menu_item'}
	</ul>
	{hook run='main_menu'}
</nav>