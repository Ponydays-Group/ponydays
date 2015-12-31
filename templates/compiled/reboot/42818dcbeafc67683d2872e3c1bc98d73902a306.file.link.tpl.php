<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker/plugins/profiler/templates/skin/default/link.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8833305815684d6cc7bf771-57268333%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '42818dcbeafc67683d2872e3c1bc98d73902a306' => 
    array (
      0 => '/var/www/bunker/plugins/profiler/templates/skin/default/link.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8833305815684d6cc7bf771-57268333',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oConfig' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cc7ce039_19619945',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cc7ce039_19619945')) {function content_5684d6cc7ce039_19619945($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><p align="center">
	Profiler: <?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('sys.logs.profiler')){?>On<?php }else{ ?>Off<?php }?> | 
	<a href="<?php echo smarty_function_router(array('page'=>'profiler'),$_smarty_tpl);?>
">Profiler reports</a>
</p><?php }} ?>