			{hook run='content_end'}
		</div> <!-- /content -->
	</div> <!-- /wrapper -->


	<footer id="footer">
		<div style="text-align: center; font-size: 16pt;">
			<span style="display: block; clear: both;">Светлая\темная тема</span>
			<div style="height: 80px; margin: 10px auto 0px; width: 80px;">
				<img src="{cfg name="path.static.skin"}/images/dark-to-day.png" onclick="bunkerStyle()" title="Светлый режим/темный режим" class="switch-theme" />
			</div>
                <p style="margin: 10px; font-size: 12pt;"><a href="/?force-mobile=on">Мобильная версия</a></p>
		</div>
		{hook run='body_end'}
	</footer>
</div> <!-- /container -->



			{include file='toolbar.tpl'}
			<script>
				{literal}


				function bunkerStyle() {
        var date = new Date;
        date.setDate(date.getDate() + 100);
        if(getCookie("SiteStyle") == "Dark") {
                document.cookie = "SiteStyle=Light; path=/; expires=" + date.toUTCString();
                location.reload();
        } else {
                if(getCookie("SiteStyle") == "Light") {
                        document.cookie = "SiteStyle=Dark; path=/; expires=" + date.toUTCString();
                        location.reload();
                } else {
                        document.cookie = "SiteStyle=Dark; path=/; expires=" + date.toUTCString();
                        location.reload();
                }
        }
}
var allNew = document.querySelectorAll('.spoiler-title');
console.log(allNew)
idx=0
for(idx=0;idx<allNew.length;idx++){	
	allNew[idx].className="spoiler-title spoiler-close" 
}

var despoil = function() {
	var allBody = document.querySelectorAll('.spoiler-body');
	idx=0
	for(idx=0;idx<allBody.length;idx++){	
		allBody[idx].style.display="inline" 
	}
	var allNew = document.querySelectorAll('.spoiler-title');
	idx=0
	for(idx=0;idx<allNew.length;idx++){	
		allNew[idx].className="spoiler-title spoiler-open" 
	}
	var el = document.getElementById("spoil");
    el.innerHTML = 'Закрыть спойлеры<i class="fa fa-eye-slash">';
    el.parentNode.title = 'Закрыть спойлеры'
    el.onclick=function(){spoil(); return false;};
}

var spoil = function() {
	var allBody = document.querySelectorAll('.spoiler-body');
	idx=0
	for(idx=0;idx<allBody.length;idx++){	
		allBody[idx].style.display="none" 
	}
	var allNew = document.querySelectorAll('.spoiler-title');
	idx=0
	for(idx=0;idx<allNew.length;idx++){	
		allNew[idx].className="spoiler-title spoiler-close" 
	}
	var el = document.getElementById("spoil");
    el.innerHTML = 'Открыть спойлеры<i class="fa fa-eye-slash">';
    el.parentNode.title = 'Открыть спойлеры'
    el.onclick=function(){despoil(); return false;};
}
$('.btn-menu').click(function(){panel()})
{/literal}
pc = 0;
var panel = function(){
	pc++;
	if (pc == 16) {
		woona()
	}
}
</script>
</body>
</html>
