<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:59
         compiled from "/var/www/bunker//templates/skin/mobile/system_message.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18001045215684d6e3f2dc52-28397767%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9dd6928e71f2d24c51ab9e4075898f06ef5b3a81' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/system_message.tpl',
      1 => 1449125146,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18001045215684d6e3f2dc52-28397767',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'noShowSystemMessage' => 0,
    'aMsgError' => 0,
    'aMsg' => 0,
    'aMsgNotice' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e402a604_81329333',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e402a604_81329333')) {function content_5684d6e402a604_81329333($_smarty_tpl) {?><?php if (!$_smarty_tpl->tpl_vars['noShowSystemMessage']->value){?>
	<?php if ($_smarty_tpl->tpl_vars['aMsgError']->value){?>
		<ul class="system-message-error">
			<?php  $_smarty_tpl->tpl_vars['aMsg'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aMsg']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aMsgError']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aMsg']->key => $_smarty_tpl->tpl_vars['aMsg']->value){
$_smarty_tpl->tpl_vars['aMsg']->_loop = true;
?>
				<li>
					<?php if ($_smarty_tpl->tpl_vars['aMsg']->value['title']!=''){?>
						<strong><?php echo $_smarty_tpl->tpl_vars['aMsg']->value['title'];?>
</strong>:
					<?php }?>
					<?php echo $_smarty_tpl->tpl_vars['aMsg']->value['msg'];?>

				</li>
			<?php } ?>
		</ul>
	<?php }?>


	<?php if ($_smarty_tpl->tpl_vars['aMsgNotice']->value){?>
		<ul class="system-message-notice">
			<?php  $_smarty_tpl->tpl_vars['aMsg'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aMsg']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aMsgNotice']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aMsg']->key => $_smarty_tpl->tpl_vars['aMsg']->value){
$_smarty_tpl->tpl_vars['aMsg']->_loop = true;
?>
				<li>
					<?php if ($_smarty_tpl->tpl_vars['aMsg']->value['title']!=''){?>
						<strong><?php echo $_smarty_tpl->tpl_vars['aMsg']->value['title'];?>
</strong>:
					<?php }?>
					<?php echo $_smarty_tpl->tpl_vars['aMsg']->value['msg'];?>

				</li>
			<?php } ?>
		</ul>
	<?php }?>
<?php }?><?php }} ?>