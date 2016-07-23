<nav id="nav">
	<ul class="nav nav-main">
		{if !$oUserCurrent}
			<li {if $sMenuHeadItemSelect=='blog'}class="active"{/if}><a href="{cfg name='path.root.web'}">{$aLang.topic_title}</a></li>
		{else}
			<li {if $sMenuHeadItemSelect=='blog'}class="active"{/if}><a href="{cfg name='path.root.web'}">Топики с фильтром</a></li>
			<li {if $sMenuHeadItemSelect=='newall'}class="active"{/if}><a href="{cfg name='path.root.web'}/index/newall/">Все топики</a></li>
		{/if}
		<li {if $sMenuHeadItemSelect=='blogs'}class="active"{/if}><a href="{router page='blogs'}">{$aLang.blogs}</a></li>
		<li {if $sMenuHeadItemSelect=='people'}class="active"{/if}><a href="{router page='people'}">{$aLang.people}</a></li>
		<li {if $sMenuHeadItemSelect=='stream'}class="active"{/if}><a href="{router page='stream'}">{$aLang.stream_menu}</a></li>
		<li><a href="http://freepony.ru/">{$aLang.freepony}</a></li>

		<li class="quote" style="float: left;">
		<a href="#" style="padding-top: 3px; padding-bottom" 0px;>
<img src="{cfg name="path.static.skin"}/images/woona.gif">
</a>
</li>	
	<li class="quote" style="float: left;">
		<a href="#">
		<h1>
		<em>
{include file="quote.php"}
</em>
</h1>
</a>
</li>
	</ul>
	{hook run='main_menu'}
</nav>
