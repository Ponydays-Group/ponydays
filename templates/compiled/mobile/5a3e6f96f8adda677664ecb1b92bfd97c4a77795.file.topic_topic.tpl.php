<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:41
         compiled from "/var/www/bunker//templates/skin/mobile/topic_topic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14180744985684d70d02d4f6-92436843%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5a3e6f96f8adda677664ecb1b92bfd97c4a77795' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/topic_topic.tpl',
      1 => 1449125147,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14180744985684d70d02d4f6-92436843',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopic' => 0,
    'bTopicList' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70d08b5f5_43639404',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70d08b5f5_43639404')) {function content_5684d70d08b5f5_43639404($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><?php echo $_smarty_tpl->getSubTemplate ('topic_part_header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

   
   
<div class="topic-content text">
	<?php echo smarty_function_hook(array('run'=>'topic_content_begin','topic'=>$_smarty_tpl->tpl_vars['oTopic']->value,'bTopicList'=>$_smarty_tpl->tpl_vars['bTopicList']->value),$_smarty_tpl);?>

	
	<?php if ($_smarty_tpl->tpl_vars['bTopicList']->value){?>
		<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getTextShort();?>

	<?php }else{ ?>
		<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getText();?>

	<?php }?>
	
	<?php echo smarty_function_hook(array('run'=>'topic_content_end','topic'=>$_smarty_tpl->tpl_vars['oTopic']->value,'bTopicList'=>$_smarty_tpl->tpl_vars['bTopicList']->value),$_smarty_tpl);?>

</div> 


<?php if ($_smarty_tpl->tpl_vars['bTopicList']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getTextShort()!=$_smarty_tpl->tpl_vars['oTopic']->value->getText()){?>
	<div class="topic-more">
		<a href="<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getUrl();?>
#cut" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_read_more'];?>
" class="button button-primary">
			<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getCutText()){?>
				<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCutText();?>

			<?php }else{ ?>
				<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_read_more'];?>

			<?php }?>
		</a>
	</div>
<?php }?>


<?php echo $_smarty_tpl->getSubTemplate ('topic_part_footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }} ?>