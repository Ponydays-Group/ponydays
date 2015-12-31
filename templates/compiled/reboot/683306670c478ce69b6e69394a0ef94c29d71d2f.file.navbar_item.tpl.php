<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker/plugins/feedbacks/templates/skin/default/navbar_item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9196899935684d6cb05c945-67923344%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '683306670c478ce69b6e69394a0ef94c29d71d2f' => 
    array (
      0 => '/var/www/bunker/plugins/feedbacks/templates/skin/default/navbar_item.tpl',
      1 => 1451290809,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9196899935684d6cb05c945-67923344',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb06df96_93955939',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb06df96_93955939')) {function content_5684d6cb06df96_93955939($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><li class="item-messages" title="Ответы"><i class="item-icon"></i></i><a href="<?php echo smarty_function_router(array('page'=>'feedbacks'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['feedbacks']['answers_menu'];?>
<i class="fa fa-comment right"></i></a></li>
<?php }} ?>