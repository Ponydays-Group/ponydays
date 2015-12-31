<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:40
         compiled from "/var/www/bunker//templates/skin/mobile/actions/ActionBlog/topic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11605493795684d70cead4a7-58623194%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3827a9d3025fc5f471925e78e6a081e844c4e04f' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/actions/ActionBlog/topic.tpl',
      1 => 1449125154,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11605493795684d70cead4a7-58623194',
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
  'unifunc' => 'content_5684d70cf17c34_44475664',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70cf17c34_44475664')) {function content_5684d70cf17c34_44475664($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('menu'=>'blog','noBg'=>true), 0);?>


<?php echo $_smarty_tpl->getSubTemplate ('topic.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('comment_tree.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('iTargetId'=>$_smarty_tpl->tpl_vars['oTopic']->value->getId(),'iAuthorId'=>$_smarty_tpl->tpl_vars['oTopic']->value->getUserId(),'sAuthorNotice'=>$_smarty_tpl->tpl_vars['aLang']->value['topic_author'],'sTargetType'=>'topic','iCountComment'=>$_smarty_tpl->tpl_vars['oTopic']->value->getCountComment(),'sDateReadLast'=>$_smarty_tpl->tpl_vars['oTopic']->value->getDateRead(),'bAllowNewComment'=>$_smarty_tpl->tpl_vars['oTopic']->value->getForbidComment(),'sNoticeNotAllow'=>$_smarty_tpl->tpl_vars['aLang']->value['topic_comment_notallow'],'sNoticeCommentAdd'=>$_smarty_tpl->tpl_vars['aLang']->value['topic_comment_add'],'bAllowSubscribe'=>true,'oSubscribeComment'=>$_smarty_tpl->tpl_vars['oTopic']->value->getSubscribeNewComment(),'aPagingCmt'=>$_smarty_tpl->tpl_vars['aPagingCmt']->value), 0);?>



<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>