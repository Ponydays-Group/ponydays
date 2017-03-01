{hook run='people_sidebar_begin'}
<section class="block">
	<header class="block-header">
		<h3>{$aLang.user_stats}</h3>
	</header>
	
	
	<div class="block-content">
		<ul>
			<li>{$aLang.user_stats_all}: <strong>{$aStat.count_all}</strong></li>
			<li>{$aLang.user_stats_active}: 
				<ul class="people-active">
					<li>За неделю: <strong>{$aStat.count_active_week}</strong></li>
					<li>За день: <strong>{$aStat.count_active_day}</strong></li>
					<li>За час: <strong>{$aStat.count_active_hour}</strong></li>
				</ul>
			</li>
		</ul>
		
		<br />
		
		<ul>
			<li>{$aLang.user_stats_sex_man}: <strong>{$aStat.count_sex_man}</strong></li>
			<li>{$aLang.user_stats_sex_woman}: <strong>{$aStat.count_sex_woman}</strong></li>
			<li>{$aLang.user_stats_sex_other}: <strong>{$aStat.count_sex_other}</strong></li>
		</ul>
	</div>
</section>


{insert name="block" block='tagsCountry'}
{insert name="block" block='tagsCity'}

{hook run='people_sidebar_end'}
