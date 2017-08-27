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
				<li><a href="#">Link</a></li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
