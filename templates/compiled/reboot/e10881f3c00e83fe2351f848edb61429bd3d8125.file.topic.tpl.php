<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:34
         compiled from "/var/www/bunker//templates/skin/reboot/actions/ActionBlog/topic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5662278315684d6cac4bc51-24695762%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e10881f3c00e83fe2351f848edb61429bd3d8125' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/actions/ActionBlog/topic.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5662278315684d6cac4bc51-24695762',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopic' => 0,
    'aLang' => 0,
    'aPagingCmt' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cacac876_72306972',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cacac876_72306972')) {function content_5684d6cacac876_72306972($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('menu'=>'blog'), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ('topic.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('comment_tree.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('iTargetId'=>$_smarty_tpl->tpl_vars['oTopic']->value->getId(),'iAuthorId'=>$_smarty_tpl->tpl_vars['oTopic']->value->getUserId(),'sAuthorNotice'=>$_smarty_tpl->tpl_vars['aLang']->value['topic_author'],'sTargetType'=>'topic','iCountComment'=>$_smarty_tpl->tpl_vars['oTopic']->value->getCountComment(),'sDateReadLast'=>$_smarty_tpl->tpl_vars['oTopic']->value->getDateRead(),'bAllowNewComment'=>$_smarty_tpl->tpl_vars['oTopic']->value->getForbidComment(),'sNoticeNotAllow'=>$_smarty_tpl->tpl_vars['aLang']->value['topic_comment_notallow'],'sNoticeCommentAdd'=>$_smarty_tpl->tpl_vars['aLang']->value['topic_comment_add'],'bAllowSubscribe'=>true,'oSubscribeComment'=>$_smarty_tpl->tpl_vars['oTopic']->value->getSubscribeNewComment(),'aPagingCmt'=>$_smarty_tpl->tpl_vars['aPagingCmt']->value), 0);?>



<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>