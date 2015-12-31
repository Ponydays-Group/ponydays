<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:26:43
         compiled from "/var/www/bunker//templates/skin/mobile/topic_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9472395175684d8b3b3a881-86569768%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '139c002754f90fe679368cbe4c42ba648f6d0bf5' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/topic_list.tpl',
      1 => 1449125146,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9472395175684d8b3b3a881-86569768',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aTopics' => 0,
    'oTopic' => 0,
    'LS' => 0,
    'sTopicTemplateName' => 0,
    'aPaging' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d8b3b77112_90777728',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d8b3b77112_90777728')) {function content_5684d8b3b77112_90777728($_smarty_tpl) {?><?php if (count($_smarty_tpl->tpl_vars['aTopics']->value)>0){?>
	<?php  $_smarty_tpl->tpl_vars['oTopic'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oTopic']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aTopics']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oTopic']->key => $_smarty_tpl->tpl_vars['oTopic']->value){
$_smarty_tpl->tpl_vars['oTopic']->_loop = true;
?>
		<?php if ($_smarty_tpl->tpl_vars['LS']->value->Topic_IsAllowTopicType($_smarty_tpl->tpl_vars['oTopic']->value->getType())){?>
			<?php $_smarty_tpl->tpl_vars["sTopicTemplateName"] = new Smarty_variable("topic_".($_smarty_tpl->tpl_vars['oTopic']->value->getType()).".tpl", null, 0);?>
			<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['sTopicTemplateName']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('bTopicList'=>true), 0);?>

		<?php }?>
	<?php } ?>

	<?php echo $_smarty_tpl->getSubTemplate ('paging.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('aPaging'=>$_smarty_tpl->tpl_vars['aPaging']->value), 0);?>

<?php }else{ ?>
	<?php echo $_smarty_tpl->tpl_vars['aLang']->value['blog_no_topic'];?>

<?php }?><?php }} ?>