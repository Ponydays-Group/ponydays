<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:41
         compiled from "/var/www/bunker//templates/skin/mobile/comment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3505994195684d70d5eece5-11178278%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd3a43db4fce663e7558b34648730d336ea3ffd74' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/comment.tpl',
      1 => 1451397137,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3505994195684d70d5eece5-11178278',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oComment' => 0,
    'oUserCurrent' => 0,
    'sDateReadLast' => 0,
    'bOneComment' => 0,
    'oVote' => 0,
    'oUser' => 0,
    'iAuthorId' => 0,
    'oConfig' => 0,
    'aLang' => 0,
    'bAllowNewComment' => 0,
    'bNoCommentFavourites' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70d81cd42_50616501',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70d81cd42_50616501')) {function content_5684d70d81cd42_50616501($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_date_format')) include '/var/www/bunker//engine/modules/viewer/plugs/function.date_format.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><?php $_smarty_tpl->tpl_vars["oUser"] = new Smarty_variable($_smarty_tpl->tpl_vars['oComment']->value->getUser(), null, 0);?>
<?php $_smarty_tpl->tpl_vars["oVote"] = new Smarty_variable($_smarty_tpl->tpl_vars['oComment']->value->getVote(), null, 0);?>


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
														<?php }?>">
	<?php if (!$_smarty_tpl->tpl_vars['oComment']->value->getDelete()||$_smarty_tpl->tpl_vars['bOneComment']->value||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator())){?>
		<a name="comment<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
"></a>
		
		<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getTargetType()!='talk'){?>	
			<span class="vote-result-comment
				<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getRating()>0){?>
					vote-count-positive
				<?php }elseif($_smarty_tpl->tpl_vars['oComment']->value->getRating()<0){?>
					vote-count-negative
				<?php }elseif($_smarty_tpl->tpl_vars['oComment']->value->getRating()==0){?>
					vote-count-zero
				<?php }?>

				<?php if ($_smarty_tpl->tpl_vars['oVote']->value){?> 
					voted
															
					<?php if ($_smarty_tpl->tpl_vars['oVote']->value->getDirection()>0){?>
						voted-up
					<?php }elseif($_smarty_tpl->tpl_vars['oVote']->value->getDirection()<0){?>
						voted-down
					<?php }?>
				<?php }?>}" 

				id="vote_total_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
">
				<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getRating()>0){?>+<?php }?><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getRating();?>

			</span>
		<?php }?>

		<a href="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getUserWebPath();?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getProfileAvatarPath(48);?>
" alt="avatar" class="comment-avatar" /></a>
		
		
		<ul class="comment-info <?php if ($_smarty_tpl->tpl_vars['iAuthorId']->value==$_smarty_tpl->tpl_vars['oUser']->value->getId()){?>comment-topic-author<?php }?>">
			<li class="comment-author">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getUserWebPath();?>
"><?php echo $_smarty_tpl->tpl_vars['oUser']->value->getLogin();?>
</a>
			</li>
			<li class="comment-date">
				<a href="<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('module.comment.nested_per_page')){?><?php echo smarty_function_router(array('page'=>'comments'),$_smarty_tpl);?>
<?php }else{ ?>#comment<?php }?><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_url_notice'];?>
">
					<time datetime="<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oComment']->value->getDate(),'format'=>'c'),$_smarty_tpl);?>
"><?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oComment']->value->getDate(),'hours_back'=>"12",'minutes_back'=>"60",'now'=>"60",'day'=>"day H:i",'format'=>"j F Y, H:i"),$_smarty_tpl);?>
</time>
				</a>
				
				<div class="comment-new-mark"></div>
			</li>
		</ul>
		
		
		<div id="comment_content_id_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" class="comment-content text">
			<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getText();?>

		</div>
			
			
		<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
			<ul class="comment-actions clearfix">
				<?php if (!$_smarty_tpl->tpl_vars['oComment']->value->getDelete()&&!$_smarty_tpl->tpl_vars['bAllowNewComment']->value){?>
					<li><a href="#" onclick="ls.comments.toggleCommentForm(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
); return false;" class="reply-link link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_answer'];?>
</a></li>
				<?php }?>
					
				<?php if (!$_smarty_tpl->tpl_vars['oComment']->value->getDelete()&&$_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()){?>
					<li><a href="#" class="comment-delete link-dotted" onclick="ls.comments.toggle(this,<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
); return false;"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_delete'];?>
</a></li>
				<?php }?>
				
				<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getDelete()&&$_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()){?>   										
					<li><a href="#" class="comment-repair link-dotted" onclick="ls.comments.toggle(this,<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
); return false;"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_repair'];?>
</a></li>
				<?php }?>
			
				<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&!$_smarty_tpl->tpl_vars['oVote']->value&&$_smarty_tpl->tpl_vars['oComment']->value->getTargetType()!='talk'){?>
					<li>
						<a href="#"

						onclick="ls.tools.slide($('#vote_area_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
'), $(this)); return false;"

						class="link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_rate'];?>
</a>
					</li>
				<?php }?>
				
				<li><a href="<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('module.comment.nested_per_page')){?><?php echo smarty_function_router(array('page'=>'comments'),$_smarty_tpl);?>
<?php }else{ ?>#comment<?php }?><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" class="link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_link'];?>
</a></li>
			
				<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&!$_smarty_tpl->tpl_vars['bNoCommentFavourites']->value){?>
					<li class="comment-favourite" onclick="return ls.favourite.toggle(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
,'#fav_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
','comment');">
						<div id="fav_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" class="favourite icon-favourite <?php if ($_smarty_tpl->tpl_vars['oComment']->value->getIsFavourite()){?>active<?php }?>"></div>
						<span class="favourite-count" id="fav_count_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
"><?php if ($_smarty_tpl->tpl_vars['oComment']->value->getCountFavourite()>0){?><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getCountFavourite();?>
<?php }?></span>
					</li>
				<?php }?>
				
				<?php echo smarty_function_hook(array('run'=>'comment_action','comment'=>$_smarty_tpl->tpl_vars['oComment']->value),$_smarty_tpl);?>

			</ul>
		<?php }?>


			
		<?php if ($_smarty_tpl->tpl_vars['oComment']->value->getTargetType()!='talk'){?>						
			<div id="vote_area_comment_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
" class="vote">
				<div class="vote-item vote-down" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
,this,-1,'comment');"><i></i></div>
				<div class="vote-item vote-up" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
,this,1,'comment');"><i></i></div>
			</div>
		<?php }?>
	<?php }else{ ?>				
		<?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_was_delete'];?>

	<?php }?>	
</section><?php }} ?>