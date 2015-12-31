<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker/plugins/editcomment/templates/skin/default/window_history.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2878208885684d6cc6b0911-06905001%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f00e98085e3666b077c12540f901a3eeacf6d2d9' => 
    array (
      0 => '/var/www/bunker/plugins/editcomment/templates/skin/default/window_history.tpl',
      1 => 1363757570,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2878208885684d6cc6b0911-06905001',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cc6bd0e0_62743988',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cc6bd0e0_62743988')) {function content_5684d6cc6bd0e0_62743988($_smarty_tpl) {?><div class="modal editcomment-history" id="modal-editcomment-history">
	<header class="modal-header">
	    <h3><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['editcomment']['history_window_title'];?>
</h3>
		<a href="#" class="close jqmClose"></a>
	</header>
	
	<div class="modal-content history-content"><div id='editcomment-history-content'></div></div>
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

<?php }} ?>