<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:59
         compiled from "/var/www/bunker//templates/skin/mobile/userbar_menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12516429295684d6e3c22292-71348837%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ac3a30333c338940b2a09df486a93554e76b2909' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/userbar_menu.tpl',
      1 => 1449125147,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12516429295684d6e3c22292-71348837',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserCurrent' => 0,
    'oUserProfile' => 0,
    'sAction' => 0,
    'aParams' => 0,
    'aLang' => 0,
    'iUserCurrentCountTopicDraft' => 0,
    'iUserCurrentCountTalkNew' => 0,
    'iCountWallUserCurrent' => 0,
    'iCountCreatedUserCurrent' => 0,
    'iCountFavouriteUserCurrent' => 0,
    'iCountFriendsUserCurrent' => 0,
    'LIVESTREET_SECURITY_KEY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e3d71cf6_97996437',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e3d71cf6_97996437')) {function content_5684d6e3d71cf6_97996437($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><aside id="userbar" class="userbar-menu">
	<div class="userbar-menu-inner" id="userbar-inner">
		<div class="userbar-menu-user">
			<a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getProfileAvatarPath(48);?>
" alt="avatar" class="avatar" /></a>
			<h3 class="login"><a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
"><?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getLogin();?>
</a></h3>
		</div>

		<div class="userbar-menu-rating-wrapper">
			<span class="user-profile-rating"><i class="icon-rating-grey"></i> <?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getRating();?>
</span>
			<span class="user-profile-rating user-profile-strength"><i class="icon-strength"></i> <?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getSkill();?>
</span>
		</div>

		<ul class="userbar-menu-items">
			<?php echo smarty_function_hook(array('run'=>'profile_sidebar_menu_item_first','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>


			<li class="userbar-item" <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&($_smarty_tpl->tpl_vars['aParams']->value[0]=='whois'||$_smarty_tpl->tpl_vars['aParams']->value[0]=='')){?>class="active"<?php }?>>
				<a href="#" onclick="jQuery('#userbar-submit-menu').slideToggle(); return false;"><div class="holder"><i class="icon-profile-submit-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create'];?>
</a>
			</li>
			
			<ul class="userbar-submit-menu" id="userbar-submit-menu">
				<?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTopicDraft']->value){?>
					<li class="write-item-type-draft">
						<i class="icon-submit-topic-userbar"></i>
						<a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
saved/" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_menu_saved'];?>
 (<?php echo $_smarty_tpl->tpl_vars['iUserCurrentCountTopicDraft']->value;?>
)</a>
					</li>
				<?php }?>
				<li class="write-item-type-topic">
					<i class="icon-submit-topic-userbar"></i>
					<a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_topic_topic'];?>
</a>
				</li>
				<li class="write-item-type-blog">
					<i class="icon-submit-blog-userbar"></i>
					<a href="<?php echo smarty_function_router(array('page'=>'blog'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_blog'];?>
</a>
				</li>
				<li class="write-item-type-message">
					<i class="icon-submit-message-userbar"></i>
					<a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
add" class="write-item-link"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create_talk'];?>
</a>
				</li>
				<?php echo smarty_function_hook(array('run'=>'write_item'),$_smarty_tpl);?>

			</ul>
			
			<li class="userbar-item userbar-item-messages <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='talk'){?>active<?php }?>">
				<a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
"><div class="holder"><i class="icon-profile-messages-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['talk_menu_inbox'];?>
</a>
				<?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value){?> 
					<a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
inbox/new" class="userbar-item-messages-number">+<?php echo $_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value;?>
</a>
				<?php }?>
			</li>
			<li class="userbar-item <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&($_smarty_tpl->tpl_vars['aParams']->value[0]=='whois'||$_smarty_tpl->tpl_vars['aParams']->value[0]=='')){?>active<?php }?>">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
"><div class="holder"><i class="icon-profile-profile-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_whois'];?>
</a>
			</li>
			<li class="userbar-item <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='wall'){?>active<?php }?>">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
wall/"><div class="holder"><i class="icon-profile-wall-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_wall'];?>
<?php if (($_smarty_tpl->tpl_vars['iCountWallUserCurrent']->value)>0){?> (<?php echo $_smarty_tpl->tpl_vars['iCountWallUserCurrent']->value;?>
)<?php }?></a>
			</li>
			<li class="userbar-item <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='created'){?>active<?php }?>">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
created/topics/"><div class="holder"><i class="icon-profile-submited-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_publication'];?>
<?php if (($_smarty_tpl->tpl_vars['iCountCreatedUserCurrent']->value)>0){?> (<?php echo $_smarty_tpl->tpl_vars['iCountCreatedUserCurrent']->value;?>
)<?php }?></a>
			</li>
			<li class="userbar-item <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='favourites'){?>active<?php }?>">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
favourites/topics/"><div class="holder"><i class="icon-profile-favourites-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_favourites'];?>
<?php if (($_smarty_tpl->tpl_vars['iCountFavouriteUserCurrent']->value)>0){?> (<?php echo $_smarty_tpl->tpl_vars['iCountFavouriteUserCurrent']->value;?>
)<?php }?></a>
			</li>
			<li class="userbar-item <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='friends'){?>active<?php }?>">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
friends/"><div class="holder"><i class="icon-profile-friends-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_friends'];?>
<?php if (($_smarty_tpl->tpl_vars['iCountFriendsUserCurrent']->value)>0){?> (<?php echo $_smarty_tpl->tpl_vars['iCountFriendsUserCurrent']->value;?>
)<?php }?></a>
			</li>
			<li class="userbar-item <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='profile'&&$_smarty_tpl->tpl_vars['aParams']->value[0]=='stream'){?>active<?php }?>">
				<a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
stream/"><div class="holder"><i class="icon-profile-activity-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_stream'];?>
</a>
			</li>
			<li class="userbar-item <?php if ($_smarty_tpl->tpl_vars['sAction']->value=='settings'){?>active<?php }?>">
				<a href="<?php echo smarty_function_router(array('page'=>'settings'),$_smarty_tpl);?>
"><div class="holder"><i class="icon-profile-settings-white"></i></div><?php echo $_smarty_tpl->tpl_vars['aLang']->value['settings_menu'];?>
</a>
			</li>

			<?php echo smarty_function_hook(array('run'=>'profile_sidebar_menu_item_last','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>


			<li class="userbar-item"><a href="<?php echo smarty_function_router(array('page'=>'login'),$_smarty_tpl);?>
exit/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['exit'];?>
</a></li>
		</ul>
	</div>
</aside><?php }} ?>