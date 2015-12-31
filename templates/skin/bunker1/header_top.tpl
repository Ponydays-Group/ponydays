		<style>
			#container {
				width: 1250px;
			}
		</style>

<header id="header" role="banner">
	{hook run='header_banner_begin'}
	<hgroup class="site-info">
		<a href="/">
			<img src="/images/logo.png" width=100% class="logo" />
		</a>
	</hgroup>
	{hook run='header_banner_end'}
</header>
<div class="primer" id="primer">
<ul class="nav nav-main">
	<li>
	<input type="button" value="Широкий режим" class="spoiler_button2" onclick=wide()>
	</li>
	<li>
	<input type="button" value="Обычный режим" class="spoiler_button2" onclick=dewide()>
	</li>
	<li>
	<input type="button" value="Закрыть все" class="spoiler_button2" onclick=$("div[class^='spoiler_body']").hide('normal')>
	</li>
	<li>
	<input type="button" value="Открыть все" class="spoiler_button2" onclick=$("div[class^='spoiler_body']").show('normal')>
	</li>
	<li><div class="spoiler_button2"><a href="#top">&uarr;</a></div></li>
	<li><div class="spoiler_button2"><a href="#footer">&darr;</a></div></li>
	</ul>
</div>
<script>
if (document.body.clientWidth <= '800') {
   var element = document.getElementById('userbar');
   var element1 = document.getElementById('primer');
   element.style.position = "static"
   element1.style.display = "none"
}
if (screen.width <= '800') {
   var element = document.getElementById('userbar');
   var element1 = document.getElementById('primer');
   element.style.position = "static"
   element1.style.display = "none"
}
</script>