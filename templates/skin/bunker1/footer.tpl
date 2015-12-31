			{hook run='content_end'}
		</div> <!-- /content -->

		
		{if !$noSidebar && $sidebarPosition != 'left'}
			{include file='sidebar.tpl'}
		{/if}
	</div> <!-- /wrapper -->
	<div>
</div> <!-- /container -->
{include file='toolbar.tpl'}
<script>
NGG();
</script>
<a name="footer">
<span onclick=bunkerStyle() style="cursor: pointer; color: white;">Светлый режим/темный режим</span>

{hook run='body_end'}
		<style>
</body>
</html>