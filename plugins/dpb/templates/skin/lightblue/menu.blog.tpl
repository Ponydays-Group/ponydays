{literal}
	<script type="text/javascript">
	$(document).ready(function(){
		$(".open-menu").click(function(){
			$(".dropdown-submenu").toggle();
		});
	});
    </script>
{/literal}
	<ul class="nav-filter">
		<li {if $sMenuSubItemSelect=='good'}class="active"{/if}><a href="{cfg name='path.root.web'}/">{$aLang.blog_menu_all_good}</a></li>
		<li {if $sMenuSubItemSelect=='new'}class="active"{/if}>
			<a href="{router page='index'}newall/" title="{$aLang.blog_menu_top_period_all}">{$aLang.topics_all}</a>
			{if $iCountTopicsNew>0}
			<a href="{router page='index'}new/" title="{$aLang.blog_menu_top_period_24h}" class="info">{$iCountTopicsNew}</a>
			{/if} 
		</li>
		<li {if $sMenuSubItemSelect=='discussed'}class="active"{/if}><a href="{router page='index'}discussed/">{$aLang.blog_menu_all_discussed}</a></li>
		<li {if $sMenuSubItemSelect=='top'}class="active"{/if}>
			<a href="#" class="open-menu">{$aLang.blog_menu_all_top}</a>

			<div class="dropdown-submenu">
				<div class="inner">
					<i class="arrow"></i>
					<ul class="submenu">
						<li {if $sPeriodSelectCurrent=='1'}class="active"{/if}>
							<a href="{if $sMenuSubItemSelect=='top'}{$sPeriodSelectRoot}?period=1{else}{router page='index'}top/{$sPeriodSelectRoot}?period=1{/if}">
								{$aLang.blog_menu_top_period_24h}
							</a>
						</li>
						<li {if $sPeriodSelectCurrent=='7'}class="active"{/if}>
							<a href="{if $sMenuSubItemSelect=='top'}{$sPeriodSelectRoot}?period=7{else}{router page='index'}top/{$sPeriodSelectRoot}?period=7{/if}">
								{$aLang.blog_menu_top_period_7d}
							</a>
						</li>
						<li {if $sPeriodSelectCurrent=='30'}class="active"{/if}>
							<a href="{if $sMenuSubItemSelect=='top'}{$sPeriodSelectRoot}?period=30{else}{router page='index'}top/{$sPeriodSelectRoot}?period=30{/if}">
								{$aLang.blog_menu_top_period_30d}
							</a>
						</li>
						<li {if $sPeriodSelectCurrent=='all'}class="active"{/if}>
							<a href="{if $sMenuSubItemSelect=='top'}{$sPeriodSelectRoot}?period=all{else}{router page='index'}top/{$sPeriodSelectRoot}?period=all{/if}">
								{$aLang.blog_menu_top_period_all}
							</a>
						</li>
					</ul>
				</div>
			</div>
		</li>
		{hook run='menu_blog_index_item'}
		{if $oUserCurrent}
			<li {if $sMenuItemSelect=='feed'}class="active"{/if}>
				<a href="{router page='feed'}">{$aLang.userfeed_title}</a>
			</li>
		{/if}
		{hook run='menu_blog'}
	</ul>
	
	