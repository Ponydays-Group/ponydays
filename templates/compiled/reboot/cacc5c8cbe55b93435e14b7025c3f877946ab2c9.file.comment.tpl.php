<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/comment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10207767465684d6cbecfa97-60574811%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cacc5c8cbe55b93435e14b7025c3f877946ab2c9' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/comment.tpl',
      1 => 1451475910,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10207767465684d6cbecfa97-60574811',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oComment' => 0,
    'oUserCurrent' => 0,
    'sDateReadLast' => 0,
    'bOneComment' => 0,
    'oUser' => 0,
    'oConfig' => 0,
    'aLang' => 0,
    'bNoCommentFavourites' => 0,
    'bAllowNewComment' => 0,
    'oVote' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cc1437f6_57521344',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cc1437f6_57521344')) {function content_5684d6cc1437f6_57521344($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_date_format')) include '/var/www/bunker//engine/modules/viewer/plugs/function.date_format.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><?php $_smarty_tpl->tpl_vars["oUser"] = new Smarty_variable($_smarty_tpl->tpl_vars['oComment']->value->getUser(), null, 0);?>
<?php $_smarty_tpl->tpl_vars["oVote"] = new Smarty_variable($_smarty_tpl->tpl_vars['oComment']->value->getVote(), null, 0);?>
<?php $_smarty_tpl->tpl_vars["oId"] = new Smarty_variable($_smarty_tpl->tpl_vars['oComment']->value->getTarget(), null, 0);?>

<section id="comment_id_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" class="comment
														<?php if ($_smarty_tpl->tpl_vars['oComment']->value->isBad()){?>
															comment-bad
														<?php }?>
														<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getDelete()){?>
															comment-deleted
														<?php }elseif($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oComment']->value->getUserId()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()){?>
															comment-self
														<?php }elseif($_smarty_tpl->tpl_vars['sDateReadLast']->value<=$_smarty_tpl->tpl_vars['oComment']->value->getDate()){?>
															comment-new
														<?php }?>
														">
	<?php if (!$_smarty_tpl->tpl_vars['oComment']->value->getDelete()||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->isGlobalModerator())||$_smarty_tpl->tpl_vars['bOneComment']->value||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator())){?>
		<a name="comment<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
"></a>
		<div class="folding fa fa-minus-square" id="folding"></div>

		<a href="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getUserWebPath();?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getProfileAvatarPath(48);?>
" alt="avatar" class="comment-avatar" /></a>


		<ul class="comment-info">
			<li class="comment-author">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getUserWebPath();?>
"><?php echo $_smarty_tpl->tpl_vars['oUser']->value->getLogin();?>
</a>
			</li>
			<li class="comment-date">
				<a href="<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('module.comment.nested_per_page')){?><?php echo smarty_function_router(array('page'=>'comments'),$_smarty_tpl);?>
<?php }else{ ?>#comment<?php }?><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" class="link-dotted" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_url_notice'];?>
">
					<time datetime="<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oComment']->value->getDate(),'format'=>'c'),$_smarty_tpl);?>
"><?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oComment']->value->getDate(),'format'=>"j F Y, H:i"),$_smarty_tpl);?>
</time>
				</a>
			</li>

			<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getPid()){?>
				<li class="goto-comment-parent"><a href="#" onclick="ls.comments.goToParentComment(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
,<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getPid();?>
); return false;" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_goto_parent'];?>
">↑</a></li>
			<?php }?>
			<li class="goto-comment-child"><a href="#" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_goto_child'];?>
">↓</a></li>




			<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&!$_smarty_tpl->tpl_vars['bNoCommentFavourites']->value){?>
				<li class="comment-favourite">
					<div onclick="return ls.favourite.toggle(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
,this,'comment');" class="favourite <?php if ($_smarty_tpl->tpl_vars['oComment']->value->getIsFavourite()){?>active<?php }?>"><i class="favourite-icon fa fa-heart"></i></div>
					<span class="favourite-count" id="fav_count_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
"><?php if ($_smarty_tpl->tpl_vars['oComment']->value->getCountFavourite()>0){?><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getCountFavourite();?>
<?php }?></span>
				</li>
			<?php }?>
		</ul>


		<div id="comment_content_id_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" class="comment-content text">
			<?php if ($_smarty_tpl->tpl_vars['oComment']->value->isBad()){?>
				<span onclick="children[0].style.display = 'block'; children[1].style.display='none' "><span style="display: none;"><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getText();?>
</span><a href="#" onclick="return false">Комментарий скрыт. Кликните, чтобы раскрыть.</a></span>
			<?php }else{ ?>
				<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getText();?>

			<?php }?>
		</div>


		<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
			<ul class="comment-actions">
				<?php if (!$_smarty_tpl->tpl_vars['oComment']->value->getDelete()&&!$_smarty_tpl->tpl_vars['bAllowNewComment']->value){?>
					<li><a href="#" onclick="ls.comments.toggleCommentForm(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
); return false;" class="reply-link link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_answer'];?>
</a></li>
				<?php }?>
<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getTargetType()!='talk'){?>
				<?php if (!$_smarty_tpl->tpl_vars['oComment']->value->getDelete()&&$_smarty_tpl->tpl_vars['oUserCurrent']->value&&($_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()||($_smarty_tpl->tpl_vars['oUserCurrent']->value->isGlobalModerator()&&$_smarty_tpl->tpl_vars['oComment']->value->getTarget()->getBlog()->getType()=="open"))){?>
					<li><a href="#" class="comment-delete link-dotted" onclick="ls.comments.toggle(this,<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
); return false;"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_delete'];?>
</a></li>
				<?php }?>

				<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getDelete()&&$_smarty_tpl->tpl_vars['oUserCurrent']->value&&($_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()||$_smarty_tpl->tpl_vars['oUserCurrent']->value->isGlobalModerator())){?>
					<li><a href="#" class="comment-repair link-dotted" onclick="ls.comments.toggle(this,<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
); return false;"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_repair'];?>
</a></li>
				<?php }?>
<?php }?>
				<?php echo smarty_function_hook(array('run'=>'comment_action','comment'=>$_smarty_tpl->tpl_vars['oComment']->value),$_smarty_tpl);?>

				<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getTargetType()!='talk'){?>
				<li style="margin-right: 0px;" id="vote_area_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" class="comment-vote vote
																		<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getRating()>0){?>
																			vote-count-positive
																		<?php }elseif($_smarty_tpl->tpl_vars['oComment']->value->getRating()<0){?>
																			vote-count-negative
																		<?php }?>
																		<?php if ($_smarty_tpl->tpl_vars['oVote']->value){?>
																			voted
																			<?php if ($_smarty_tpl->tpl_vars['oVote']->value->getDirection()>0){?>
																				voted-up
																			<?php }else{ ?>
																				voted-down
																			<?php }?>
																		<?php }?>">
					
					<div class="vote-up" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
,this,1,'comment');"><i class="fa fa-arrow-up"></i></div>
					<span class="vote-count" id="vote_total_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
"><?php if ($_smarty_tpl->tpl_vars['oComment']->value->getRating()>0){?>+<?php }?><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getRating();?>
</span>
					<div class="vote-down" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
,this,-1,'comment');"><i class="fa fa-arrow-down"></i></div>
				</li>
			<?php }?>
			</ul>
		<?php }?>
	<?php }else{ ?>
		<?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_was_delete'];?>

	<?php }?>
</section>
<?php }} ?>