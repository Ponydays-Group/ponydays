<div class="modal editcomment-history" id="modal-editcomment-history">
	<header class="modal-header">
	    <h3>{$aLang.plugin.editcomment.history_window_title}</h3>
		<a href="#" class="close jqmClose"></a>
	</header>
	
	{strip}
	<div class="modal-content history-content">
	    <div id='editcomment-history-content'>
	    </div>
	</div>
	{/strip}
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#modal-editcomment-history').jqm();
    $(document).keydown( function( e ) {
   if( e.which == 27) {  // escape, close box
     $('#modal-editcomment-history').jqmHide();
   }
 });
});
</script>

