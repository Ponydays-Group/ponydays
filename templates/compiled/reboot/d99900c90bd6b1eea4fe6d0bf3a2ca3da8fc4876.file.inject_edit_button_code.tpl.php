<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker/plugins/editcomment/templates/skin/default/inject_edit_button_code.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12621770655684d6cc655f13-94881026%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd99900c90bd6b1eea4fe6d0bf3a2ca3da8fc4876' => 
    array (
      0 => '/var/www/bunker/plugins/editcomment/templates/skin/default/inject_edit_button_code.tpl',
      1 => 1363757570,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12621770655684d6cc655f13-94881026',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'iTargetId' => 0,
    'sTargetType' => 0,
    'aLang' => 0,
    'oConfig' => 0,
    'cv' => 0,
    'oUserCurrent' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cc6a5604_35545506',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cc6a5604_35545506')) {function content_5684d6cc6a5604_35545506($_smarty_tpl) {?><script type="text/javascript">
    jQuery(document).ready(function ($) {
        ls.comments.options.edit_button_code = '<button type="submit" class="button button-primary" name="submit_edit" id="comment-button-submit-edit" onclick="ls.comments.edit(\'form_comment\',<?php echo $_smarty_tpl->tpl_vars['iTargetId']->value;?>
,\'<?php echo $_smarty_tpl->tpl_vars['sTargetType']->value;?>
\'); return false;"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['editcomment']['edit_button_title'];?>
</button>';
    <?php $_smarty_tpl->tpl_vars['cv'] = new Smarty_variable($_smarty_tpl->tpl_vars['oConfig']->value->Get('plugin.editcomment.show_history_button'), null, 0);?>
    <?php if (($_smarty_tpl->tpl_vars['cv']->value!=0&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator())||($_smarty_tpl->tpl_vars['cv']->value==1)||($_smarty_tpl->tpl_vars['cv']->value==2&&(in_array($_smarty_tpl->tpl_vars['oUserCurrent']->value->getId(),$_smarty_tpl->tpl_vars['oConfig']->value->Get('plugin.editcomment.comment_editors'))))){?>
        ls.comments.options.history_button_code = '<button type="button" class="button" name="submit_history" id="comment-button-history" onclick="ls.comments.showHistory(); return false;"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['editcomment']['history_button_title'];?>
</button>';
        <?php }else{ ?>
        ls.comments.options.history_button_code = '';
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['oConfig']->value->Get('plugin.editcomment.show_cancel_button')){?>
        ls.comments.options.cancel_button_code = '<button type="button" class="button" name="submit_cancel" id="comment-button-cancel" onclick="ls.comments.cancelEditComment(); return false;"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['editcomment']['cancel_button_title'];?>
</button>';
        <?php }else{ ?>
        ls.comments.options.cancel_button_code = '';
    <?php }?>
    });
</script>

<?php }} ?>