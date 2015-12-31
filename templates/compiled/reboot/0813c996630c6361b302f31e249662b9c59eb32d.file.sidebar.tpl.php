<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/sidebar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12713667685684d6cb174b04-85619659%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0813c996630c6361b302f31e249662b9c59eb32d' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/sidebar.tpl',
      1 => 1449659417,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12713667685684d6cb174b04-85619659',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'sidebarPosition' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb181d30_73272550',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb181d30_73272550')) {function content_5684d6cb181d30_73272550($_smarty_tpl) {?><aside id="sidebar" <?php if ($_smarty_tpl->tpl_vars['sidebarPosition']->value=='left'){?>class="sidebar-left"<?php }?>>
	<?php echo $_smarty_tpl->getSubTemplate ('blocks.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('group'=>'right'), 0);?>

</aside>
<?php }} ?>