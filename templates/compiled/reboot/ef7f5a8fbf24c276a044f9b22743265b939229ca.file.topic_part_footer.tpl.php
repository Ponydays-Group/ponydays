<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/topic_part_footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7945395205684d6cbb75816-91637594%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ef7f5a8fbf24c276a044f9b22743265b939229ca' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/topic_part_footer.tpl',
      1 => 1450374420,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7945395205684d6cbb75816-91637594',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopic' => 0,
    'aLang' => 0,
    'sTag' => 0,
    'oUserCurrent' => 0,
    'oFavourite' => 0,
    'oVote' => 0,
    'oConfig' => 0,
    'oUser' => 0,
    'bVoteInfoShow' => 0,
    'bTopicList' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cbd597a4_63953117',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cbd597a4_63953117')) {function content_5684d6cbd597a4_63953117($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?>	<?php $_smarty_tpl->tpl_vars["oBlog"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getBlog(), null, 0);?>
	<?php $_smarty_tpl->tpl_vars["oUser"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getUser(), null, 0);?>
	<?php $_smarty_tpl->tpl_vars["oVote"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getVote(), null, 0);?>
	<?php $_smarty_tpl->tpl_vars["oFavourite"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getFavourite(), null, 0);?>


	<footer class="topic-footer">
		<ul class="topic-tags js-favourite-insert-after-form js-favourite-tags-topic-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
">
			<li><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_tags'];?>
:</li>
			
			<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getTagsArray()){?><?php  $_smarty_tpl->tpl_vars['sTag'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sTag']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['oTopic']->value->getTagsArray(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['sTag']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['sTag']->key => $_smarty_tpl->tpl_vars['sTag']->value){
$_smarty_tpl->tpl_vars['sTag']->_loop = true;
 $_smarty_tpl->tpl_vars['sTag']->index++;
 $_smarty_tpl->tpl_vars['sTag']->first = $_smarty_tpl->tpl_vars['sTag']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['tags_list']['first'] = $_smarty_tpl->tpl_vars['sTag']->first;
?><li><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['tags_list']['first']){?>, <?php }?><a rel="tag" href="<?php echo smarty_function_router(array('page'=>'tag'),$_smarty_tpl);?>
<?php echo rawurlencode($_smarty_tpl->tpl_vars['sTag']->value);?>
/"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sTag']->value, ENT_QUOTES, 'UTF-8', true);?>
</a></li><?php } ?><?php }else{ ?><li><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_tags_empty'];?>
</li><?php }?><?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?><?php if ($_smarty_tpl->tpl_vars['oFavourite']->value){?><?php  $_smarty_tpl->tpl_vars['sTag'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sTag']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['oFavourite']->value->getTagsArray(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['sTag']->key => $_smarty_tpl->tpl_vars['sTag']->value){
$_smarty_tpl->tpl_vars['sTag']->_loop = true;
?><li class="topic-tags-user js-favourite-tag-user">, <a rel="tag" href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
favourites/topics/tag/<?php echo rawurlencode($_smarty_tpl->tpl_vars['sTag']->value);?>
/"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['sTag']->value, ENT_QUOTES, 'UTF-8', true);?>
</a></li><?php } ?><?php }?><li class="topic-tags-edit js-favourite-tag-edit" <?php if (!$_smarty_tpl->tpl_vars['oFavourite']->value){?>style="display:none;"<?php }?>><a href="#" onclick="return ls.favourite.showEditTags(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,'topic',this);" class="link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['favourite_form_tags_button_show'];?>
</a></li><?php }?>
		</ul>


		<ul class="topic-info">
			<li id="vote_area_topic_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
" class="stickyDa vote 
																<?php if ($_smarty_tpl->tpl_vars['oVote']->value||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getUserId()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId())||strtotime($_smarty_tpl->tpl_vars['oTopic']->value->getDateAdd())<time()-$_smarty_tpl->tpl_vars['oConfig']->value->GetValue('acl.vote.topic.limit_time')){?>
																	<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getRating()>0){?>
																		vote-count-positive
																	<?php }elseif($_smarty_tpl->tpl_vars['oTopic']->value->getRating()<0){?>
																		vote-count-negative
																	<?php }?>
																<?php }?>
																
																<?php if ($_smarty_tpl->tpl_vars['oVote']->value){?> 
																	voted
																	
																	<?php if ($_smarty_tpl->tpl_vars['oVote']->value->getDirection()>0){?>
																		voted-up
																	<?php }elseif($_smarty_tpl->tpl_vars['oVote']->value->getDirection()<0){?>
																		voted-down
																	<?php }?>
																<?php }?>">
				<?php if ($_smarty_tpl->tpl_vars['oVote']->value||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getUserId()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId())||strtotime($_smarty_tpl->tpl_vars['oTopic']->value->getDateAdd())<time()-$_smarty_tpl->tpl_vars['oConfig']->value->GetValue('acl.vote.topic.limit_time')){?>
					<?php $_smarty_tpl->tpl_vars["bVoteInfoShow"] = new Smarty_variable(true, null, 0);?>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
					<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()!=$_smarty_tpl->tpl_vars['oUser']->value->getId()){?>
				<div class="vote-up" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,this,1,'topic');"><i class="fa fa-chevron-up"></i></div>
				<div class="vote-down" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,this,-1,'topic');"><i class="fa fa-chevron-down"></i></div>
					<?php }?>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['bVoteInfoShow']->value){?>
					<div id="vote-info-topic-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
" style="display: none;">
						+ <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountVoteUp();?>
<br/>
						- <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountVoteDown();?>
<br/>
						&nbsp; <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountVoteAbstain();?>
<br/>
						<?php echo smarty_function_hook(array('run'=>'topic_show_vote_stats','topic'=>$_smarty_tpl->tpl_vars['oTopic']->value),$_smarty_tpl);?>

					</div>
				<?php }?>
			</li>

			<li class="topic-info-author"><a rel="author" href="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getUserWebPath();?>
"><?php echo $_smarty_tpl->tpl_vars['oUser']->value->getLogin();?>
</a></li>
			<li class="topic-info-favourite">
				<div onclick="return ls.favourite.toggle(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,this,'topic');" class="favourite <?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getIsFavourite()){?>active<?php }?>"><i class="favourite-icon fa fa-heart"></i></div>
				<span class="favourite-count" id="fav_count_topic_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
"><?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountFavourite();?>
</span>
			</li>
						
			<?php if ($_smarty_tpl->tpl_vars['bTopicList']->value){?>
				<li class="topic-info-comments">
					<a href="<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getUrl();?>
#comments" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_comment_read'];?>
"><?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountComment();?>
 <i class="fa fa-comments"></i></a>
					<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getCountCommentNew()){?><span>+<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountCommentNew();?>
</span><?php }?>
				</li>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['bTopicList']->value){?>
				<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getTextShort()!=$_smarty_tpl->tpl_vars['oTopic']->value->getText()){?>
					<li class="cut">
					<a href="<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getUrl();?>
#cut" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_read_more'];?>
">
						<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getCutText()){?>
							<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCutText();?>

						<?php }else{ ?>
							<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_read_more'];?>

						<?php }?>
					</a>
						</li>
				<?php }?>
			<?php }?>
			<?php echo smarty_function_hook(array('run'=>'topic_show_info','topic'=>$_smarty_tpl->tpl_vars['oTopic']->value),$_smarty_tpl);?>

		</ul>

		
		<?php if (!$_smarty_tpl->tpl_vars['bTopicList']->value){?>
			<?php echo smarty_function_hook(array('run'=>'topic_show_end','topic'=>$_smarty_tpl->tpl_vars['oTopic']->value),$_smarty_tpl);?>

		<?php }?>
	</footer>
</article> <!-- /.topic -->
<?php }} ?>