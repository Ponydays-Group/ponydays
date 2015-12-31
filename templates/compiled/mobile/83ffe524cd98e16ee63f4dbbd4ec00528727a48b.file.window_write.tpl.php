<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:59
         compiled from "/var/www/bunker//templates/skin/mobile/window_write.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11126521605684d6e3deb1e7-82851391%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '83ffe524cd98e16ee63f4dbbd4ec00528727a48b' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/window_write.tpl',
      1 => 1449125147,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11126521605684d6e3deb1e7-82851391',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'iUserCurrentCountTopicDraft' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e3e18e68_69804359',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e3e18e68_69804359')) {function content_5684d6e3e18e68_69804359($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><ul class="slide slide-write" id="write">
	<?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTopicDraft']->value){?>
		<li class="write-item-type-draft">
			<i class="icon-submit-draft"></i>
			<a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
saved/" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_menu_saved'];?>
 (<?php echo $_smarty_tpl->tpl_vars['iUserCurrentCountTopicDraft']->value;?>
)</a>
		</li>
	<?php }?>
	<li class="write-item-type-topic">
		<i class="icon-submit-topic"></i>
		<a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_topic_topic'];?>
</a>
	</li>
	<li class="write-item-type-blog">
		<i class="icon-submit-blog"></i>
		<a href="<?php echo smarty_function_router(array('page'=>'blog'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_blog'];?>
</a>
	</li>
	<li class="write-item-type-message">
		<i class="icon-submit-message"></i>
		<a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_talk'];?>
</a>
	</li>
	<?php echo smarty_function_hook(array('run'=>'write_item','isPopup'=>true),$_smarty_tpl);?>

</ul>
	<?php }} ?>