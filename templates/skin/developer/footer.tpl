			{hook run='content_end'}
		</div> <!-- /content -->


		{if !$noSidebar && $sidebarPosition != 'left'}
			{include file='sidebar.tpl'}
		{/if}
	</div> <!-- /wrapper -->

<div id="image-modal"><img src="#" id="image-modal-img" /></div>

	<footer id="footer">
		<div class="copyright">
			{hook run='copyright'}
		</div>

		Версия фронтэнда: {cfg name='frontend.version'}<br>
		<a target="_blank" href="https://github.com/silvman/ponydays/issues">Сообщить об ошибке / Отправить предложение</a>

		{hook run='footer_end'}
	</footer>

</div> <!-- /container -->

{include file='toolbar.tpl'}

{hook run='body_end'}

</body>
</html>
