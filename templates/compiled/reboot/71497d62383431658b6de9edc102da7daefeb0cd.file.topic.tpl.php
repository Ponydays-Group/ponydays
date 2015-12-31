<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/topic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14879344185684d6cb87b499-54873579%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '71497d62383431658b6de9edc102da7daefeb0cd' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/topic.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14879344185684d6cb87b499-54873579',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopic' => 0,
    'LS' => 0,
    'sTopicTemplateName' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb8991e7_03216400',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb8991e7_03216400')) {function content_5684d6cb8991e7_03216400($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['LS']->value->Topic_IsAllowTopicType($_smarty_tpl->tpl_vars['oTopic']->value->getType())){?>
	<?php $_smarty_tpl->tpl_vars["sTopicTemplateName"] = new Smarty_variable("topic_".($_smarty_tpl->tpl_vars['oTopic']->value->getType()).".tpl", null, 0);?>
	<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['sTopicTemplateName']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?><?php }} ?>