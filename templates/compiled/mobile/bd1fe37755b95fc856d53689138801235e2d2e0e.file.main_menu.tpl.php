<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:59
         compiled from "/var/www/bunker/plugins/page/templates/skin/default/main_menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5693567415684d6e3e7fe39-94969315%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bd1fe37755b95fc856d53689138801235e2d2e0e' => 
    array (
      0 => '/var/www/bunker/plugins/page/templates/skin/default/main_menu.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5693567415684d6e3e7fe39-94969315',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aPagesMain' => 0,
    'sAction' => 0,
    'sEvent' => 0,
    'oPage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e3ea4469_88607807',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e3ea4469_88607807')) {function content_5684d6e3ea4469_88607807($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><?php  $_smarty_tpl->tpl_vars['oPage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oPage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aPagesMain']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oPage']->key => $_smarty_tpl->tpl_vars['oPage']->value){
$_smarty_tpl->tpl_vars['oPage']->_loop = true;
?>
	<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='page'&&$_smarty_tpl->tpl_vars['sEvent']->value==$_smarty_tpl->tpl_vars['oPage']->value->getUrl()){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'page'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getUrlFull();?>
/" ><?php echo $_smarty_tpl->tpl_vars['oPage']->value->getTitle();?>
</a><i></i></li>
<?php } ?>	<?php }} ?>