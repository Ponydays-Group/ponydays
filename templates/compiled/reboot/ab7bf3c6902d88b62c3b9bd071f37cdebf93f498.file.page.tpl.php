<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:29
         compiled from "/var/www/bunker/plugins/page/templates/skin/default/actions/ActionPage/page.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1324295025684d701bc6542-23106426%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ab7bf3c6902d88b62c3b9bd071f37cdebf93f498' => 
    array (
      0 => '/var/www/bunker/plugins/page/templates/skin/default/actions/ActionPage/page.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1324295025684d701bc6542-23106426',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oConfig' => 0,
    'oPage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d701c3e196_99103353',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d701c3e196_99103353')) {function content_5684d701c3e196_99103353($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars["noSidebar"] = new Smarty_variable(true, null, 0);?>
<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<div class="topic">
	<div class="topic-content text">
		<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('view.tinymce')){?>
			<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getText();?>

		<?php }else{ ?>
			<?php if ($_smarty_tpl->tpl_vars['oPage']->value->getAutoBr()){?>
				<?php echo nl2br($_smarty_tpl->tpl_vars['oPage']->value->getText());?>

			<?php }else{ ?>
				<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getText();?>

			<?php }?>
		<?php }?>
	</div>
</div>

<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>