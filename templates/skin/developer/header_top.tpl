<nav id="userbar" class="clearfix">
	<span class="site-title">{cfg name="view.name"}</span>
	<form action="{router page='search'}topics/" class="search">
		<input type="text" maxlength="255" name="q" class="input-text">
		<button type="submit" value="" title="{$aLang.search_submit}" class="input-submit search-icon fa fa-search"></button>
	</form>

	{hook run='userbar_nav'}

	<ul class="nav nav-userbar">
		<li title="Сменить тему">
			<a href="#" onclick="switchTheme()">
				<i class="fa fa-{cfg name="icon"}-o"></i>
			</a>
		</li>
		{if $oUserCurrent}
		<li title="{$aLang.block_create}">
        <a href="{router page='topic'}add/" class="write" id="modal_write_show">
            <i class="fa fa-plus"></i>
        </a>
    </li>
		<li title="{$aLang.user_privat_messages}{if $iUserCurrentCountTalkNew} ({$iUserCurrentCountTalkNew}){/if}">
        <a data-title="{$aLang.user_privat_messages}" href="{router page='talk'}" {if $iUserCurrentCountTalkNew}class="new-messages"{/if} id="new_messages">
            {if $iUserCurrentCountTalkNew} ({$iUserCurrentCountTalkNew}){/if}
            <i class="fa fa-envelope-o"></i>
            <i class="fa fa-envelope"></i>
        </a>
    </li>
			<li class="nav-userbar-username" title="Профиль">
				<a href="{$oUserCurrent->getUserWebPath()}" class="username">
					<span>{$oUserCurrent->getLogin()}</span>
					<img src="{$oUserCurrent->getProfileAvatarPath(24)}" alt="avatar" class="avatar" />
				</a>
			</li>
			<li title="Настройки"><a href="{router page='settings'}profile/"><i class="fa fa-cog"></i></a></li>
			{hook run='userbar_item'}
			<li title="Выйти"><a href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}"><i class="fa fa-power-off"></i></a></li>
		{else}
			{hook run='userbar_item'}
			<li><a href="{router page='login'}" class="js-login-form-show">{$aLang.user_login_submit}</a></li>
			<li><a href="{router page='registration'}" class="js-registration-form-show">{$aLang.registration_submit}</a></li>
		{/if}
	</ul>
</nav>


{include file='nav.tpl'}
