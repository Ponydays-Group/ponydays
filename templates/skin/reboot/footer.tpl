			{hook run='content_end'}
		</div> <!-- /content -->
	</div> <!-- /wrapper -->
{if !$oUserCurrent}
	        <script src="https://smiles.everypony.ru/smilepack/jrayjn.compat.user.js"></script>
{/if}

<script>
{literal}
function NewOldBunker(){
	date = new Date();
        date.setDate(date.getDate() + 100);
        if(getCookie("UseOld") == "1") {
                document.cookie = "UseOld=0; path=/; expires=" + date.toUTCString();
                location.reload();
        } else {
                if(getCookie("UseOld") == "0") {
                        document.cookie = "UseOld=1; path=/; expires=" + date.toUTCString();
                        location.reload();
                } else {
                        document.cookie = "UseOld=1; path=/; expires=" + date.toUTCString();
                        location.reload();
                }
	}
}
</script>
<!-- Yandex.Metrika counter --> <script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter34742160 = new Ya.Metrika({ id:34742160, clickmap:true, trackLinks:true, accurateTrackBounce:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/34742160" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
{/literal}


	<footer id="footer">
		<div style="text-align: center; font-size: 16pt;">
			<span style="display: block; clear: both;">Светлая\темная тема</span>
			<div style="height: 80px; margin: 10px auto 0px; width: 80px;">
				<img src="{cfg name="path.static.skin"}/images/dark-to-day.png" onclick="bunkerStyle()" title="Светлый режим/темный режим" class="switch-theme" />
			</div>
		
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
	var allNew = document.querySelectorAll('.spoiler-title');
	idx=0
	for(idx=0;idx<allNew.length;idx++){	
		allNew[idx].className="spoiler-title spoiler-open" 
                b = allNew[idx].parentNode.querySelector(".spoiler-body");
                jQuery(b).show(300);
                b.style.display = "block";

	}
	var el = document.getElementById("spoil");
    el.innerHTML = 'Закрыть спойлеры<i class="fa fa-eye-slash">';
    el.parentNode.title = 'Закрыть спойлеры'
    el.onclick=function(){spoil(); return false;};
}

var spoil = function() {
	var allBody = document.querySelectorAll('.spoiler-body');
	idx=0
	var allNew = document.querySelectorAll('.spoiler-title');
	idx=0
	for(idx=0;idx<allNew.length;idx++){	
		allNew[idx].className="spoiler-title spoiler-close" 
                b = allNew[idx].parentNode.querySelector(".spoiler-body");
                jQuery(b).hide(300);

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
<!-- right sidebar -->
	<script src="{cfg name="path.static.skin"}/js/scroll.js"></script>
</body>
</html>
