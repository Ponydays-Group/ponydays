<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:27
         compiled from "/var/www/bunker//templates/skin/mobile/actions/ActionProfile/profile_top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1827653135684d6ffad6706-39452078%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9f523ed9086c6aaca85f815918f1d81d59d1a741' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/actions/ActionProfile/profile_top.tpl',
      1 => 1449125157,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1827653135684d6ffad6706-39452078',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserProfile' => 0,
    'oUserCurrent' => 0,
    'oVote' => 0,
    'oUserFriend' => 0,
    'aLang' => 0,
    'sAction' => 0,
    'aParams' => 0,
    'iCountWallUser' => 0,
    'iCountCreated' => 0,
    'iCountFavourite' => 0,
    'iCountFriendsUser' => 0,
    'iUserCurrentCountTalkNew' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6ffddfc86_56783811',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6ffddfc86_56783811')) {function content_5684d6ffddfc86_56783811($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><div class="profile">
	<?php echo smarty_function_hook(array('run'=>'profile_top_begin','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

	
	<a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileAvatarPath(64);?>
" alt="avatar" class="avatar" itemprop="photo" /></a>
	
	<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->isOnline()){?><div class="status <?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->isOnline()){?>status-online<?php }else{ ?>status-offline<?php }?>"></div><?php }?>

	<div class="user-profile-rating-wrapper">
		<span class="user-profile-rating"><i class="icon-rating"></i> <span id="vote_total_user_alt_<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
"><?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getRating();?>
</span></span>
		<span class="user-profile-rating user-profile-strength"><i class="icon-strength"></i> <?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getSkill();?>
</span>
	</div>
	
	<h2 class="page-header user-login word-wrap <?php if (!$_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileName()){?>no-user-name<?php }?>" itemprop="nickname"><?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getLogin();?>
</h2>
	
	<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileName()){?>
		<p class="user-name" itemprop="name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileName(), ENT_QUOTES, 'UTF-8', true);?>
</p>
	<?php }?>
	
	<?php echo smarty_function_hook(array('run'=>'profile_top_end','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

</div>


<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()!=$_smarty_tpl->tpl_vars['oUserProfile']->value->getId()){?>
	<ul class="profile-actions full-width clearfix" id="profile_actions">
		<?php echo $_smarty_tpl->getSubTemplate ('actions/ActionProfile/friend_item.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('oUserFriend'=>$_smarty_tpl->tpl_vars['oUserProfile']->value->getUserFriend()), 0);?>

		<li><a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
add/?talk_users=<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getLogin();?>
" class="icon-send-message"></a></li>
		
		<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserProfile']->value->getId()!=$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()){?>
			<li class="vote-result vote-no-rating
				<?php if ($_smarty_tpl->tpl_vars['oVote']->value){?>
					<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->getRating()>0){?>
						vote-count-positive
					<?php }elseif($_smarty_tpl->tpl_vars['oUserProfile']->value->getRating()<0){?>
						vote-count-negative
					<?php }elseif($_smarty_tpl->tpl_vars['oUserProfile']->value->getRating()==0){?>
						vote-count-zero
					<?php }?>
				<?php }?>

				<?php if ($_smarty_tpl->tpl_vars['oVote']->value){?> 
					voted
															
					<?php if ($_smarty_tpl->tpl_vars['oVote']->value->getDirection()>0){?>
						voted-up
					<?php }elseif($_smarty_tpl->tpl_vars['oVote']->value->getDirection()<0){?>
						voted-down
					<?php }?>
				<?php }?>"

				id="vote_total_user_<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
"

				<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&!$_smarty_tpl->tpl_vars['oVote']->value){?>
					onclick="ls.tools.slide($('#vote_area_user_<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
'), $(this));"
				<?php }?>>
			</li>
		<?php }?>
	</ul>
<?php }?>


<?php if (!$_smarty_tpl->tpl_vars['oUserFriend']->value){?>
	<div id="add_friend_form" class="slide slide-bg-grey mb-10">
		<header class="modal-header">
			<h3><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_add_friend'];?>
</h3>
			<a href="#" class="close jqmClose"></a>
		</header>

		<form onsubmit="return ls.user.addFriend(this,<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
,'add');" class="modal-content">
			<p><label for="add_friend_text"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_friend_add_text_label'];?>
</label>
			<textarea id="add_friend_text" rows="3" class="input-text input-width-full"></textarea></p>

			<button type="submit" class="button button-primary"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_friend_add_submit'];?>
</button>
		</form>
	</div>
<?php }?>


<div id="vote_area_user_<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
" class="vote vote-blog">
	<div class="vote-item vote-up" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
,this,1,'user');"><i></i></div>
	<div class="vote-item vote-down" onclick="return ls.vote.vote(<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
,this,-1,'user');"><i></i></div>
</div>


<ul class="nav-foldable">
	<?php echo smarty_function_hook(array('run'=>'profile_sidebar_menu_item_first','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

	<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&($_smarty_tpl->tpl_vars['aParams']->value[0]=='whois'||$_smarty_tpl->tpl_vars['aParams']->value[0]=='')){?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_whois'];?>
</a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='wall'){?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
wall/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_wall'];?>
<?php if (($_smarty_tpl->tpl_vars['iCountWallUser']->value)>0){?> (<?php echo $_smarty_tpl->tpl_vars['iCountWallUser']->value;?>
)<?php }?></a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='created'){?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
created/topics/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_publication'];?>
<?php if (($_smarty_tpl->tpl_vars['iCountCreated']->value)>0){?> (<?php echo $_smarty_tpl->tpl_vars['iCountCreated']->value;?>
)<?php }?></a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='favourites'){?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
favourites/topics/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_favourites'];?>
<?php if (($_smarty_tpl->tpl_vars['iCountFavourite']->value)>0){?> (<?php echo $_smarty_tpl->tpl_vars['iCountFavourite']->value;?>
)<?php }?></a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='friends'){?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
friends/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_friends'];?>
<?php if (($_smarty_tpl->tpl_vars['iCountFriendsUser']->value)>0){?> (<?php echo $_smarty_tpl->tpl_vars['iCountFriendsUser']->value;?>
)<?php }?></a></li>
	<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='stream'){?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
stream/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_stream'];?>
</a></li>
	
	<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()==$_smarty_tpl->tpl_vars['oUserProfile']->value->getId()){?>
		<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='talk'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['talk_menu_inbox'];?>
<?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value){?> (<?php echo $_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value;?>
)<?php }?></a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='settings'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'settings'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['settings_menu'];?>
</a></li>
	<?php }?>
	<?php echo smarty_function_hook(array('run'=>'profile_sidebar_menu_item_last','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

</ul><?php }} ?>