<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:40
         compiled from "/var/www/bunker//templates/skin/mobile/topic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20680230785684d70d000c74-43176656%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '965224bff633316a4b7cbb9685592bbec4456dc5' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/topic.tpl',
      1 => 1449125146,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20680230785684d70d000c74-43176656',
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
  'unifunc' => 'content_5684d70d027e66_60366308',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70d027e66_60366308')) {function content_5684d70d027e66_60366308($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['LS']->value->Topic_IsAllowTopicType($_smarty_tpl->tpl_vars['oTopic']->value->getType())){?>
	<?php $_smarty_tpl->tpl_vars["sTopicTemplateName"] = new Smarty_variable("topic_".($_smarty_tpl->tpl_vars['oTopic']->value->getType()).".tpl", null, 0);?>
	<?php echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['sTopicTemplateName']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?><?php }} ?>