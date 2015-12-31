<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:22:11
         compiled from "/var/www/bunker//templates/skin/reboot/actions/ActionProfile/sidebar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18881944665684d7a34dd3c5-14848720%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7bfa35fc9d25c9dabffcbeb5675ffc6e38311ed0' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/actions/ActionProfile/sidebar.tpl',
      1 => 1451484211,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18881944665684d7a34dd3c5-14848720',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserProfile' => 0,
    'aLang' => 0,
    'sAction' => 0,
    'oUserCurrent' => 0,
    'oUserNote' => 0,
    'aParams' => 0,
    'iCountWallUser' => 0,
    'iCountCreated' => 0,
    'iCountFavourite' => 0,
    'iCountFriendsUser' => 0,
    'iUserCurrentCountTalkNew' => 0,
    'oAceUserProfile' => 0,
    'LIVESTREET_SECURITY_KEY' => 0,
    'oLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d7a3735815_89905718',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d7a3735815_89905718')) {function content_5684d7a3735815_89905718($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_lang_load')) include '/var/www/bunker//engine/modules/viewer/plugs/function.lang_load.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_json')) include '/var/www/bunker//engine/modules/viewer/plugs/function.json.php';
?><?php echo smarty_function_hook(array('run'=>'profile_sidebar_begin','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>


<section class="block block-type-profile">
	<div class="profile-photo-wrapper">
		<div class="status <?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->isOnline()){?>status-online<?php }else{ ?>status-offline<?php }?>"><?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->isOnline()){?><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_status_online'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_status_offline'];?>
<?php }?></div>
		<a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileFotoPath();?>
" alt="photo" class="profile-photo" id="foto-img" /></a>
	</div>
	
	<?php if ($_smarty_tpl->tpl_vars['sAction']->value=='settings'&&$_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()==$_smarty_tpl->tpl_vars['oUserProfile']->value->getId()){?>
		<script type="text/javascript">
			jQuery(function($){
				$('#foto-upload').file({ name:'foto' }).choose(function(e, input) {
					ls.user.uploadFoto(null,input);
				});
			});
		</script>
		
		<p class="upload-photo">
			<a href="#" id="foto-upload" class="link-dotted"><?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value->getProfileFoto()){?><?php echo $_smarty_tpl->tpl_vars['aLang']->value['settings_profile_photo_change'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['aLang']->value['settings_profile_photo_upload'];?>
<?php }?></a>&nbsp;&nbsp;&nbsp;
			<a href="#" id="foto-remove" class="link-dotted" onclick="return ls.user.removeFoto();" style="<?php if (!$_smarty_tpl->tpl_vars['oUserCurrent']->value->getProfileFoto()){?>display:none;<?php }?>"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['settings_profile_foto_delete'];?>
</a>
		</p>

		<div class="modal" id="foto-resize">
			<header class="modal-header">
				<h3><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg'];?>
</h3>
			</header>
			
			<div class="modal-content">
				<img src="" alt="" id="foto-resize-original-img"><br />
				<button type="submit" class="button button-primary" onclick="return ls.user.resizeFoto();"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['settings_profile_avatar_resize_apply'];?>
</button>
				<button type="submit" class="button" onclick="return ls.user.cancelFoto();"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['settings_profile_avatar_resize_cancel'];?>
</button>
			</div>
		</div>
	<?php }?>
</section>



<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()!=$_smarty_tpl->tpl_vars['oUserProfile']->value->getId()){?>
	<script type="text/javascript">
		jQuery(function($){
			ls.lang.load(<?php echo smarty_function_lang_load(array('name'=>"profile_user_unfollow,profile_user_follow"),$_smarty_tpl);?>
);
		});
	</script>

	<section class="block block-type-profile-actions">
		<div class="block-content">
			<ul class="profile-actions" id="profile_actions">
				<?php echo $_smarty_tpl->getSubTemplate ('actions/ActionProfile/friend_item.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('oUserFriend'=>$_smarty_tpl->tpl_vars['oUserProfile']->value->getUserFriend()), 0);?>

				<li><a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
add/?talk_users=<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getLogin();?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_write_prvmsg'];?>
</a></li>						
				<li>
					<a href="#" onclick="ls.user.followToggle(this, <?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
); return false;" class="<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->isFollow()){?>followed<?php }?>">
						<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->isFollow()){?><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_user_unfollow'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_user_follow'];?>
<?php }?>
					</a>
				</li>
				<?php echo smarty_function_hook(array('run'=>'profile_sidebar_show','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

			</ul>
		</div>
	</section>
<?php }?>	



<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()!=$_smarty_tpl->tpl_vars['oUserProfile']->value->getId()){?>
	<section class="block block-type-profile-note">
		<?php if ($_smarty_tpl->tpl_vars['oUserNote']->value){?>
			<script type="text/javascript">
				ls.usernote.sText = <?php echo smarty_function_json(array('var'=>$_smarty_tpl->tpl_vars['oUserNote']->value->getText()),$_smarty_tpl);?>
;
			</script>
		<?php }?>

		<div id="usernote-note" class="profile-note" <?php if (!$_smarty_tpl->tpl_vars['oUserNote']->value){?>style="display: none;"<?php }?>>
			<p id="usernote-note-text">
				<?php if ($_smarty_tpl->tpl_vars['oUserNote']->value){?>
					<?php echo $_smarty_tpl->tpl_vars['oUserNote']->value->getText();?>

				<?php }?>
			</p>
			
			<ul class="actions">
				<li><a href="#" onclick="return ls.usernote.showForm();" class="link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_form_edit'];?>
</a></li>
				<li><a href="#" onclick="return ls.usernote.remove(<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
);" class="link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_form_delete'];?>
</a></li>
			</ul>
		</div>
		
		<div id="usernote-form" style="display: none;">
			<p><textarea rows="4" cols="20" id="usernote-form-text" class="input-text input-width-full"></textarea></p>
			<button type="submit" onclick="return ls.usernote.save(<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
);" class="button button-primary"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_form_save'];?>
</button>
			<button type="submit" onclick="return ls.usernote.hideForm();" class="button"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_form_cancel'];?>
</button>
		</div>
		
		<a href="#" onclick="return ls.usernote.showForm();" id="usernote-button-add" class="link-dotted" <?php if ($_smarty_tpl->tpl_vars['oUserNote']->value){?>style="display:none;"<?php }?>><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_add'];?>
</a>
	</section>
<?php }?>

<?php echo smarty_function_hook(array('run'=>'profile_sidebar_menu_before','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>


<section class="block block-type-profile-nav">
	<ul class="nav nav-pills nav-profile">
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
			<?php echo smarty_function_hook(array('run'=>'athead'),$_smarty_tpl);?>

			<?php echo smarty_function_hook(array('run'=>'atmenu'),$_smarty_tpl);?>

		<?php }?>
		<?php echo smarty_function_hook(array('run'=>'profile_sidebar_menu_item_last','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

	</ul>
</section>
<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()!=$_smarty_tpl->tpl_vars['oAceUserProfile']->value->getId()&&($_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()||$_smarty_tpl->tpl_vars['oUserCurrent']->value->isGlobalModerator())){?>
<section class="block">
<header class="block-header"><h3>Бан</h3></header>
<div class="block-content">
<?php if (!$_smarty_tpl->tpl_vars['oAceUserProfile']->value->IsBannedByLogin()){?>
                    <form method="post" action="https://reboot.lunavod.ru/api/ban/" class="well well-small">
                        <br>
                        <input name="security_ls_key" value="<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
" type="hidden">

                        <input name="ban_login" value="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getLogin();?>
" type="hidden">

                        <label class="radio">
                            <input name="ban_period" value="days" checked="" type="radio">
                            Бан на
                            <input name="ban_days" id="ban_days" class="num1" type="text"> дней
                        </label>

                        <label class="radio">
                            <input name="ban_period" value="unlim" type="radio">
                            Бан бессрочный
                        </label>

                        <label for="ban_comment">Комментарий</label>
                        <input name="ban_comment" id="ban_comment" maxlength="255" type="text">
			<br>
                        <input name="adm_user_ref" value="https://reboot.lunavod.ru/admin/users/" type="hidden">
                        <input name="adm_user_action" value="adm_ban_user" type="hidden">
                        <button type="submit" name="adm_action_submit" class="btn btn-primary">Забанить</button>
                    </form>
                </div>
</section>
<?php }else{ ?>
<div class="alert alert-block">
    <?php echo $_smarty_tpl->tpl_vars['oLang']->value->adm_ban_upto;?>

    : <?php if ($_smarty_tpl->tpl_vars['oAceUserProfile']->value->getBanLine()){?><?php echo $_smarty_tpl->tpl_vars['oAceUserProfile']->value->getBanLine();?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['oLang']->value->adm_ban_unlim;?>
<?php }?>
    <br/>
    <strong><?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getBanComment();?>
</strong>
</div>
<form method="post" action="https://reboot.lunavod.ru/api/ban/" class="well well-small">
                        <input name="security_ls_key" value="<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
" type="hidden">

                        <input name="ban_login" value="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getLogin();?>
" type="hidden">
<input name="clear" type="hidden" value="true">
<button type="submit" name="adm_action_submit" class="btn btn-primary">Разбанить</button>
</form>
</div>
</section>
<?php }?>
<?php }?>
<?php echo smarty_function_hook(array('run'=>'profile_sidebar_end','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

<?php }} ?>