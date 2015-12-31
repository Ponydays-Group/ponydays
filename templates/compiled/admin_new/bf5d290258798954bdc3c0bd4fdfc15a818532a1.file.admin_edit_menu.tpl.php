<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker/plugins/mhb/templates/skin/default/admin_edit_menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14042826785684d6ccd6cb20-83658847%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bf5d290258798954bdc3c0bd4fdfc15a818532a1' => 
    array (
      0 => '/var/www/bunker/plugins/mhb/templates/skin/default/admin_edit_menu.tpl',
      1 => 1352404134,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14042826785684d6ccd6cb20-83658847',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6ccd78c03_67115879',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6ccd78c03_67115879')) {function content_5684d6ccd78c03_67115879($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?> <li><a href="<?php echo smarty_function_router(array('page'=>"admin"),$_smarty_tpl);?>
mhb/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['mhb']['admin_admin_menu'];?>
</a></li>
<?php }} ?>