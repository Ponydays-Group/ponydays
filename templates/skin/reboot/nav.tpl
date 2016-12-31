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
<img src="{cfg name="path.static.skin"}/images/woona_ny.gif">
</a>
</li>	
	<li class="quote" style="float: left;">
		<a href="#">
		<h1>
		<em>
{literal}
<script type="text/javascript">
function fulltime ()
{
var time=new Date();
var newYear=new Date("January,1,2017,00:00:00");
var totalRemains=(newYear.getTime()-time.getTime());

if (totalRemains>1){

var RemainsSec = (parseInt(totalRemains/1000));//сколько всего осталось секунд
var RemainsFullDays=(parseInt(RemainsSec/(24*60*60)));//осталось дней
var secInLastDay=RemainsSec-RemainsFullDays*24*3600; //осталось секунд в неполном дне
var RemainsFullHours=(parseInt(secInLastDay/3600));//осталось часов в неполном дне
if (RemainsFullHours<10){RemainsFullHours="0"+RemainsFullHours};
var secInLastHour=secInLastDay-RemainsFullHours*3600;//осталось секунд в неполном часе
var RemainsMinutes=(parseInt(secInLastHour/60));//осталось минут в неполном часе
if (RemainsMinutes<10){RemainsMinutes="0"+RemainsMinutes};
var lastSec=secInLastHour-RemainsMinutes*60;//осталось секунд
if (lastSec<10){lastSec="0"+lastSec};

document.getElementById("RemainsFullHours").innerHTML=RemainsFullHours+":";
document.getElementById("RemainsMinutes").innerHTML=RemainsMinutes+":";
document.getElementById("lastSec").innerHTML=lastSec;
setTimeout('fulltime()',10)
}

else{
document.getElementById("clock").innerHTML="C НОВЫМ ГОДОМ!";
}
}
</script>
<span id="clock">До нового года осталось:
<b><span id="RemainsFullHours"></span><span id="RemainsMinutes"></span><span id="lastSec"></span></b>
</span>
<script type="text/javascript">fulltime();</script>{/literal}
</em>
</h1>
</a>
</li>
	</ul>
	{hook run='main_menu'}
</nav>
