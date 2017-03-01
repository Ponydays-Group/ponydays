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
{/literal}
</script>

<span onclick=bunkerStyle() style="cursor: pointer; color: white;">Светлый режим/темный режим</span><br>
<span style="cursor: pointer;" onclick="NewOldBunker()"><a style="cursor: pointer;" onclick="NewOldBunker()">Что-то старое, что-то новое... Что-то синее.</a></span>
{hook run='body_end'}
		<style>
</body>
</html>
