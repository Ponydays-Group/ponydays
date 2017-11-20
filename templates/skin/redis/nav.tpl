<nav class="navbar navbar-default">
	<div class="container-fluid nav nav-main">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
                <li {if $sMenuHeadItemSelect=='blog'}class="active"{/if}><a href="/">{$aLang.topic_title}</a></li>
                <li {if $sMenuHeadItemSelect=='blogs'}class="active"{/if}><a href="{router page='blogs'}">{$aLang.blogs}</a></li>
                <li {if $sMenuHeadItemSelect=='people'}class="active"{/if}><a href="{router page='people'}">{$aLang.people}</a></li>
                <li {if $sMenuHeadItemSelect=='stream'}class="active"{/if}><a href="{router page='stream'}">{$aLang.stream_menu}</a></li>
                <li {if $sMenuHeadItemSelect=='feedbacks'}class="active"{/if}><a href="{router page='feedbacks'}">{$aLang.feedbacks.header}</a></li>
                {if $oUserCurrent}
				<li {if $sMenuHeadItemSelect=='quotes'}class="active"{/if}><a href="{router page='quotes'}">{$aLang.quotes_title}</a></li>
				{/if}
                {hook run='main_menu_item'}
			</ul>
			<ul class="nav navbar-nav navbar-right">
				{if $oUserCurrent}
				{else}
					<li><a href="{router page='login'}" class="js-login-form-show">{$aLang.user_login_submit}</a></li>
					<li><a href="{router page='registration'}" class="js-registration-form-show">{$aLang.registration_submit}</a></li>
                {/if}
				<li><a href="#" class="user-wrapper">{if $oUserCurrent}{$oUserCurrent->getLogin()}{/if} <span class="avatar-wrapper"><img src="{if $oUserCurrent}{$oUserCurrent->getProfileAvatarPath(48)}{else}https://chenhan1218.github.io/img/profile.png{/if}" alt="avatar" class="avatar" /></span></a></li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
