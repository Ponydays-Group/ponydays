<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:41
         compiled from "/var/www/bunker/plugins/editcomment/templates/skin/default/inject_editcomment_command.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3354485365684d70d842799-52777855%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7e6c05b071a9db71d57f928247d468db8a22b2b5' => 
    array (
      0 => '/var/www/bunker/plugins/editcomment/templates/skin/default/inject_editcomment_command.tpl',
      1 => 1363757570,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3354485365684d70d842799-52777855',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aLang' => 0,
    'iCommentId' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70d853501_00653153',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70d853501_00653153')) {function content_5684d70d853501_00653153($_smarty_tpl) {?><li><a href="#" class="editcomment_editlink" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['editcomment']['edit_command_title'];?>
" onclick="ls.comments.editComment(<?php echo $_smarty_tpl->tpl_vars['iCommentId']->value;?>
); return false;"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['editcomment']['edit_command_title'];?>
</a></li><?php }} ?>