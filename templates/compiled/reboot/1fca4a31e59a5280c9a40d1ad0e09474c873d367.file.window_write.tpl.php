<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/window_write.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18997042655684d6cb0746f8-81905026%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1fca4a31e59a5280c9a40d1ad0e09474c873d367' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/window_write.tpl',
      1 => 1445000008,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18997042655684d6cb0746f8-81905026',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aLang' => 0,
    'iUserCurrentCountTopicDraft' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb0c81f0_52033633',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb0c81f0_52033633')) {function content_5684d6cb0c81f0_52033633($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><div class="modal modal-write" id="modal_write">
	<header class="modal-header">
		<h3><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create'];?>
</h3>
		<a href="#" class="close jqmClose"></a>
	</header>
	
	<div class="modal-content"><ul class="write-list"><li class="write-item-type-topic"><a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
add" class="write-item-image"></a><a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_topic_topic'];?>
</a></li><li class="write-item-type-poll"><a href="<?php echo smarty_function_router(array('page'=>'question'),$_smarty_tpl);?>
add" class="write-item-image"></a><a href="<?php echo smarty_function_router(array('page'=>'question'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_topic_question'];?>
</a></li><li class="write-item-type-link"><a href="<?php echo smarty_function_router(array('page'=>'link'),$_smarty_tpl);?>
add" class="write-item-image"></a><a href="<?php echo smarty_function_router(array('page'=>'link'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_topic_link'];?>
</a></li><li class="write-item-type-blog"><a href="<?php echo smarty_function_router(array('page'=>'blog'),$_smarty_tpl);?>
add" class="write-item-image"></a><a href="<?php echo smarty_function_router(array('page'=>'blog'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_blog'];?>
</a></li><li class="write-item-type-draft"><a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
saved/" class="write-item-image"></a><a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
saved/" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_menu_saved'];?>
 <?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTopicDraft']->value){?>(<?php echo $_smarty_tpl->tpl_vars['iUserCurrentCountTopicDraft']->value;?>
)<?php }?></a></li><?php echo smarty_function_hook(array('run'=>'write_item','isPopup'=>true),$_smarty_tpl);?>
</ul></div>
</div>
	<?php }} ?>