<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/nav_content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2208554135684d6cb819519-12710780%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1f83cc3148650c12e4fa7978f2cef64707495ee3' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/nav_content.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2208554135684d6cb819519-12710780',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'menu' => 0,
    'aMenuContainers' => 0,
    'aMenuFetch' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb831768_93399883',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb831768_93399883')) {function content_5684d6cb831768_93399883($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['menu']->value){?>
	<?php if (in_array($_smarty_tpl->tpl_vars['menu']->value,$_smarty_tpl->tpl_vars['aMenuContainers']->value)){?><?php echo $_smarty_tpl->tpl_vars['aMenuFetch']->value[$_smarty_tpl->tpl_vars['menu']->value];?>
<?php }else{ ?><?php echo $_smarty_tpl->getSubTemplate ("menu.".($_smarty_tpl->tpl_vars['menu']->value).".tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }?>
<?php }?><?php }} ?>