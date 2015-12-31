<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:41
         compiled from "/var/www/bunker//templates/skin/mobile/topic_part_footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3152268425684d70d112f92-45256372%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e6ae15e416931159cd3c47ef1f8823a7407c91e3' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/topic_part_footer.tpl',
      1 => 1449125146,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3152268425684d70d112f92-45256372',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopic' => 0,
    'oVote' => 0,
    'oUserCurrent' => 0,
    'oConfig' => 0,
    'aLang' => 0,
    'bTopicList' => 0,
    'sTag' => 0,
    'oFavourite' => 0,
    'bVoteInfoShow' => 0,
    'oUser' => 0,
    'oBlog' => 0,
    'LIVESTREET_SECURITY_KEY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70d4878c8_29189474',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70d4878c8_29189474')) {function content_5684d70d4878c8_29189474($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_block_hookb')) include '/var/www/bunker//engine/modules/viewer/plugs/block.hookb.php';
if (!is_callable('smarty_function_date_format')) include '/var/www/bunker//engine/modules/viewer/plugs/function.date_format.php';
if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
?>	<?php $_smarty_tpl->tpl_vars["oBlog"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getBlog(), null, 0);?>
	<?php $_smarty_tpl->tpl_vars["oUser"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getUser(), null, 0);?>
	<?php $_smarty_tpl->tpl_vars["oVote"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getVote(), null, 0);?>
	<?php $_smarty_tpl->tpl_vars["oFavourite"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getFavourite(), null, 0);?>

	<?php if ($_smarty_tpl->tpl_vars['oVote']->value||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getUserId()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId())||strtotime($_smarty_tpl->tpl_vars['oTopic']->value->getDateAdd())<time()-$_smarty_tpl->tpl_vars['oConfig']->value->GetValue('acl.vote.topic.limit_time')){?>
		<?php $_smarty_tpl->tpl_vars["bVoteInfoShow"] = new Smarty_variable(true, null, 0);?>
	<?php }?>

	<footer class="topic-footer">
		<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getType()=='link'){?>
			<div class="topic-url">
				<a href="<?php echo smarty_function_router(array('page'=>'link'),$_smarty_tpl);?>
go/<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
/" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_link_count_jump'];?>
: <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getLinkCountJump();?>
"><?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getLinkUrl();?>
</a>
			</div>
		<?php }?>
	
		<?php if (!$_smarty_tpl->tpl_vars['bTopicList']->value){?>
			<ul class="topic-tags js-favourite-insert-after-form js-favourite-tags-topic-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
">
				<li><strong><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_tags'];?>
:</strong></li>
				
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
		<?php }?>


		<ul class="topic-info clearfix">
			<?php if (!$_smarty_tpl->tpl_vars['bTopicList']->value){?>
				<li class="topic-info-vote">
					<div class="vote-result
								<?php if ($_smarty_tpl->tpl_vars['oVote']->value||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getUserId()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId())||strtotime($_smarty_tpl->tpl_vars['oTopic']->value->getDateAdd())<time()-$_smarty_tpl->tpl_vars['oConfig']->value->GetValue('acl.vote.topic.limit_time')){?>
									<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getRating()>0){?>
										vote-count-positive
									<?php }elseif($_smarty_tpl->tpl_vars['oTopic']->value->getRating()<0){?>
										vote-count-negative
									<?php }elseif($_smarty_tpl->tpl_vars['oTopic']->value->getRating()==0){?>
										vote-count-zero
									<?php }?>
								<?php }?>
								
								<?php if ($_smarty_tpl->tpl_vars['oVote']->value){?> 
									voted
																			
									<?php if ($_smarty_tpl->tpl_vars['oVote']->value->getDirection()>0){?>
										voted-up
									<?php }elseif($_smarty_tpl->tpl_vars['oVote']->value->getDirection()<0){?>
										voted-down
									<?php }elseif($_smarty_tpl->tpl_vars['oVote']->value->getDirection()==0){?>
										voted-zero
									<?php }?>
								<?php }?>
				
								<?php if ((strtotime($_smarty_tpl->tpl_vars['oTopic']->value->getDateAdd())<time()-$_smarty_tpl->tpl_vars['oConfig']->value->GetValue('acl.vote.topic.limit_time')&&!$_smarty_tpl->tpl_vars['oVote']->value)||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getUserId()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId())){?>
									vote-nobuttons
								<?php }?>" 
				
						id="vote_total_topic_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
" 
				
						<?php if (!$_smarty_tpl->tpl_vars['oVote']->value&&!$_smarty_tpl->tpl_vars['bVoteInfoShow']->value){?>
							onclick="ls.tools.slide($('#vote_area_topic_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
'), $(this));"
						<?php }?>
				
						<?php if (false){?>
							onclick="ls.tools.slide($('#vote-info-topic-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
'), $(this));"
						<?php }?>>
						<?php if ($_smarty_tpl->tpl_vars['bVoteInfoShow']->value){?>
							<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getRating()>0){?>+<?php }?><?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getRating();?>

						<?php }?>
					</div>
				</li>
			<?php }?>
			
			<li class="topic-info-views">
				<i class="icon-views"></i> <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountRead();?>

			</li>

			<li class="topic-info-favourite <?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getIsFavourite()){?>active<?php }?>" onclick="return ls.favourite.toggle(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,'#fav_topic_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
','topic');">
				<i id="fav_topic_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
" class="favourite icon-favourite <?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value->getIsFavourite()){?>active<?php }?>"></i>
				<span class="favourite-count" id="fav_count_topic_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
"><?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getCountFavourite()>0){?><?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountFavourite();?>
<?php }?></span>
			</li>
			
			<?php if ($_smarty_tpl->tpl_vars['bTopicList']->value){?>
				<li class="topic-info-comments">
					<a href="<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getUrl();?>
#comments" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_comment_read'];?>
">
						<i class="icon-comments <?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getCountComment()!=0&&($_smarty_tpl->tpl_vars['oTopic']->value->getCountComment()==$_smarty_tpl->tpl_vars['oTopic']->value->getCountCommentNew()||$_smarty_tpl->tpl_vars['oTopic']->value->getCountCommentNew())){?>active<?php }?>"></i>
						<span class="comments-count">
							<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountComment();?>

							<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getCountCommentNew()){?><span class="comments-new">+<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountCommentNew();?>
</span><?php }?>
						</span>
					</a> 
				</li>
			<?php }?>

			<li class="topic-info-share" onclick="ls.tools.slide($('#topic_share_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
'), $(this));">
				<i class="icon-share"></i>
			</li>
		</ul>

		<?php if ($_smarty_tpl->tpl_vars['bVoteInfoShow']->value){?>
			<div id="vote-info-topic-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
" class="slide slide-bg-grey">
				+ <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountVoteUp();?>
<br/>
				- <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountVoteDown();?>
<br/>
				&nbsp; <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountVoteAbstain();?>
<br/>
				<?php echo smarty_function_hook(array('run'=>'topic_show_vote_stats','topic'=>$_smarty_tpl->tpl_vars['oTopic']->value),$_smarty_tpl);?>

			</div>
		<?php }?>
		
		<div class="slide slide-topic-info-extra" id="topic_share_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
">
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('hookb', array('run'=>"topic_share",'topic'=>$_smarty_tpl->tpl_vars['oTopic']->value,'bTopicList'=>$_smarty_tpl->tpl_vars['bTopicList']->value)); $_block_repeat=true; echo smarty_block_hookb(array('run'=>"topic_share",'topic'=>$_smarty_tpl->tpl_vars['oTopic']->value,'bTopicList'=>$_smarty_tpl->tpl_vars['bTopicList']->value), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

				<div class="yashare-auto-init" data-yashareTitle="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oTopic']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
" data-yashareLink="<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getUrl();?>
" data-yashareL10n="ru" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus"></div>
			<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_hookb(array('run'=>"topic_share",'topic'=>$_smarty_tpl->tpl_vars['oTopic']->value,'bTopicList'=>$_smarty_tpl->tpl_vars['bTopicList']->value), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		</div>


		<div id="vote_area_topic_<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
" class="vote">
			<div class="vote-item vote-up" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,this,1,'topic');"><i></i></div>
			<?php if (!$_smarty_tpl->tpl_vars['bVoteInfoShow']->value){?>
				<div class="vote-item vote-zero" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_vote_count'];?>
: <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountVote();?>
" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,this,0,'topic');">
					<i></i> Воздержаться
				</div>
			<?php }?>
			<div class="vote-item vote-down" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,this,-1,'topic');"><i></i></div>
		</div>



		<ul class="topic-info-extra clearfix">
			<li class="topic-info-author">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getUserWebPath();?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getProfileAvatarPath(48);?>
" alt="avatar" /></a>
				<p><a rel="author" href="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getUserWebPath();?>
"><?php echo $_smarty_tpl->tpl_vars['oUser']->value->getLogin();?>
</a></p>
				<time datetime="<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oTopic']->value->getDateAdd(),'format'=>'c'),$_smarty_tpl);?>
" title="<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oTopic']->value->getDateAdd(),'format'=>'j F Y, H:i'),$_smarty_tpl);?>
">
					<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oTopic']->value->getDateAdd(),'format'=>"j F Y, H:i"),$_smarty_tpl);?>

				</time>
			</li>

			<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
				<li class="topic-info-extra-trigger" onclick="ls.tools.slide($('#topic-extra-target-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
'), $(this));">
					<i class="icon-topic-menu"></i>
				</li>
			<?php }?>
			
			<?php echo smarty_function_hook(array('run'=>'topic_show_info','topic'=>$_smarty_tpl->tpl_vars['oTopic']->value),$_smarty_tpl);?>

		</ul>


		<ul class="slide slide-topic-info-extra" id="topic-extra-target-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
">
			<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
				<li><a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
add/?talk_users=<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getLogin();?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['send_message_to_author'];?>
</a></li>
			<?php }?>

			<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&($_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()==$_smarty_tpl->tpl_vars['oTopic']->value->getUserId()||$_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()||$_smarty_tpl->tpl_vars['oBlog']->value->getUserIsAdministrator()||$_smarty_tpl->tpl_vars['oBlog']->value->getUserIsModerator()||$_smarty_tpl->tpl_vars['oBlog']->value->getOwnerId()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId())){?>
				<li><a href="<?php echo smarty_function_cfg(array('name'=>'path.root.web'),$_smarty_tpl);?>
/<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getType();?>
/edit/<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
/" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_edit'];?>
" class="actions-edit"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_edit'];?>
</a></li>
			<?php }?>
			
			<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&($_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()||$_smarty_tpl->tpl_vars['oBlog']->value->getUserIsAdministrator()||$_smarty_tpl->tpl_vars['oBlog']->value->getOwnerId()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId())){?>
				<li><a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
delete/<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_delete'];?>
" onclick="return confirm('<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_delete_confirm'];?>
');" class="actions-delete"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_delete'];?>
</a></li>
			<?php }?>
		</ul>

		
		<?php if (!$_smarty_tpl->tpl_vars['bTopicList']->value){?>
			<?php echo smarty_function_hook(array('run'=>'topic_show_end','topic'=>$_smarty_tpl->tpl_vars['oTopic']->value),$_smarty_tpl);?>

		<?php }?>
	</footer>
</article> <!-- /.topic --><?php }} ?>