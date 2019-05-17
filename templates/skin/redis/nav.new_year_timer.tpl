<li id="NYtimer">
    <a href="#">
        {literal}
            <script type="text/javascript">
				function declOfNum(number, titles) {
					cases = [2, 0, 1, 1, 1, 2];
					return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
				}

				function fulltime () {
					var time=new Date();
					var newYear=new Date("January,1,2020,00:00:00");
					var totalRemains=(newYear.getTime()-time.getTime());
					if (totalRemains>1){
						var RemainsSec = (parseInt(totalRemains/1000));//сколько всего осталось секунд
						var RemainsFullDays=(parseInt(RemainsSec/(24*60*60)));//осталось дней
						var secInLastDay=RemainsSec-RemainsFullDays*24*3600; //осталось секунд в неполном дне
						var RemainsFullHours=(parseInt(secInLastDay/3600));//осталось часов в неполном дне
						if (RemainsFullHours<10){RemainsFullHours="0"+RemainsFullHours}
						var secInLastHour=secInLastDay-RemainsFullHours*3600;//осталось секунд в неполном часе
						var RemainsMinutes=(parseInt(secInLastHour/60));//осталось минут в неполном часе
						if (RemainsMinutes<10){RemainsMinutes="0"+RemainsMinutes}
						var lastSec=secInLastHour-RemainsMinutes*60;//осталось секунд
						if (lastSec<10){lastSec="0"+lastSec}

						document.getElementById("RemainsFullDays").innerHTML=RemainsFullDays+" "+declOfNum(RemainsFullDays, ['день', 'дня', 'дней']);
						document.getElementById("RemainsFullHours").innerHTML=RemainsFullHours+" "+declOfNum(RemainsFullHours, ['час', 'часа', 'часов']);
						document.getElementById("RemainsMinutes").innerHTML=RemainsMinutes+" "+declOfNum(RemainsMinutes, ['минута', 'минуты', 'минут']);
						document.getElementById("lastSec").innerHTML=lastSec+" "+declOfNum(lastSec, ['секунда', 'секунды', 'секунд']);
						setTimeout('fulltime()',10)
					}

					else{
						document.getElementById("clock").innerHTML="C НОВЫМ ГОДОМ!";
					}
				}
            </script>
            <span id="clock">До нового года осталось:
                <b><span id="RemainsFullDays"></span>, <span id="RemainsFullHours"></span>, <span id="RemainsMinutes"></span>, <span id="lastSec"></span></b>
            </span>
            <script type="text/javascript">fulltime();</script>
        {/literal}
    </a>
</li>
