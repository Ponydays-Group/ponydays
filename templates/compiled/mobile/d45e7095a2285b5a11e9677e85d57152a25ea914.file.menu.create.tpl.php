<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:59
         compiled from "/var/www/bunker//templates/skin/mobile/menu.create.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4061695085684d6e3eaa1c3-90495235%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd45e7095a2285b5a11e9677e85d57152a25ea914' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/menu.create.tpl',
      1 => 1449125145,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4061695085684d6e3eaa1c3-90495235',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'sMenuItemSelect' => 0,
    'aLang' => 0,
    'sMenuSubItemSelect' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e3f27631_29807133',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e3f27631_29807133')) {function content_5684d6e3f27631_29807133($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><ul class="nav-foldable">
	<li <?php if ($_smarty_tpl->tpl_vars['sMenuItemSelect']->value=='topic'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
add/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create'];?>
  <?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_menu_add'];?>
</a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['sMenuItemSelect']->value=='blog'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'blog'),$_smarty_tpl);?>
add/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create'];?>
 <?php echo $_smarty_tpl->tpl_vars['aLang']->value['blog_menu_create'];?>
</a></li>
	<?php echo smarty_function_hook(array('run'=>'menu_create_item','sMenuItemSelect'=>$_smarty_tpl->tpl_vars['sMenuItemSelect']->value),$_smarty_tpl);?>

</ul>


<?php if ($_smarty_tpl->tpl_vars['sMenuItemSelect']->value=='topic'){?>
	<ul class="nav-foldable">
		<li <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='topic'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
add/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_menu_add_topic'];?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='question'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'question'),$_smarty_tpl);?>
add/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_menu_add_question'];?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='link'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'link'),$_smarty_tpl);?>
add/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_menu_add_link'];?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='photoset'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'photoset'),$_smarty_tpl);?>
add/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_menu_add_photoset'];?>
</a></li>
		<?php echo smarty_function_hook(array('run'=>'menu_create_topic_item'),$_smarty_tpl);?>

	</ul>
<?php }?>


<?php echo smarty_function_hook(array('run'=>'menu_create','sMenuItemSelect'=>$_smarty_tpl->tpl_vars['sMenuItemSelect']->value,'sMenuSubItemSelect'=>$_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value),$_smarty_tpl);?>
<?php }} ?>