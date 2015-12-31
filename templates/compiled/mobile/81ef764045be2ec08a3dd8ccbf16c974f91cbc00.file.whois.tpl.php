<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:27
         compiled from "/var/www/bunker//templates/skin/mobile/actions/ActionProfile/whois.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5104072815684d6ff590226-83010627%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '81ef764045be2ec08a3dd8ccbf16c974f91cbc00' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/actions/ActionProfile/whois.tpl',
      1 => 1449125157,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5104072815684d6ff590226-83010627',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserProfile' => 0,
    'oGeoTarget' => 0,
    'aUserFieldValues' => 0,
    'aLang' => 0,
    'oField' => 0,
    'oUserCurrent' => 0,
    'oUserNote' => 0,
    'aUserFieldContactValues' => 0,
    'aUsersFriend' => 0,
    'iCountFriendsUser' => 0,
    'oSession' => 0,
    'oConfig' => 0,
    'oUserInviteFrom' => 0,
    'aUsersInvite' => 0,
    'oUserInvite' => 0,
    'aBlogsOwner' => 0,
    'oBlog' => 0,
    'aBlogAdministrators' => 0,
    'oBlogUser' => 0,
    'aBlogModerators' => 0,
    'aBlogUsers' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6ffa89ca5_50628922',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6ffa89ca5_50628922')) {function content_5684d6ffa89ca5_50628922($_smarty_tpl) {?><?php if (!is_callable('smarty_function_date_format')) include '/var/www/bunker//engine/modules/viewer/plugs/function.date_format.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_json')) include '/var/www/bunker//engine/modules/viewer/plugs/function.json.php';
?><?php $_smarty_tpl->tpl_vars["sMenuItemSelect"] = new Smarty_variable('profile', null, 0);?>
<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php $_smarty_tpl->tpl_vars["oSession"] = new Smarty_variable($_smarty_tpl->tpl_vars['oUserProfile']->value->getSession(), null, 0);?>
<?php $_smarty_tpl->tpl_vars["oVote"] = new Smarty_variable($_smarty_tpl->tpl_vars['oUserProfile']->value->getVote(), null, 0);?>
<?php $_smarty_tpl->tpl_vars["oGeoTarget"] = new Smarty_variable($_smarty_tpl->tpl_vars['oUserProfile']->value->getGeoTarget(), null, 0);?>



<?php echo $_smarty_tpl->getSubTemplate ('actions/ActionProfile/profile_top.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<br />

<?php $_smarty_tpl->tpl_vars["aUserFieldValues"] = new Smarty_variable($_smarty_tpl->tpl_vars['oUserProfile']->value->getUserFieldValues(true,array('')), null, 0);?>

<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileSex()!='other'||$_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileBirthday()||$_smarty_tpl->tpl_vars['oGeoTarget']->value||count($_smarty_tpl->tpl_vars['aUserFieldValues']->value)){?>
<div class="table-profile-info-wrapper">
	<h2 class="header-table"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_privat'];?>
</h2>
	
	
	<table class="table table-profile-info">		
		<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileSex()!='other'){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_sex'];?>
:</td>
				<td>
					<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileSex()=='man'){?>
						<?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_sex_man'];?>

					<?php }else{ ?>
						<?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_sex_woman'];?>

					<?php }?>
				</td>
			</tr>
		<?php }?>
			
			
		<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileBirthday()){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_birthday'];?>
:</td>
				<td><?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileBirthday(),'format'=>"j F Y"),$_smarty_tpl);?>
</td>
			</tr>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['oGeoTarget']->value){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_place'];?>
:</td>
				<td itemprop="address" itemscope itemtype="http://data-vocabulary.org/Address">
					<?php if ($_smarty_tpl->tpl_vars['oGeoTarget']->value->getCountryId()){?>
						<a href="<?php echo smarty_function_router(array('page'=>'people'),$_smarty_tpl);?>
country/<?php echo $_smarty_tpl->tpl_vars['oGeoTarget']->value->getCountryId();?>
/" itemprop="country-name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileCountry(), ENT_QUOTES, 'UTF-8', true);?>
</a><?php if ($_smarty_tpl->tpl_vars['oGeoTarget']->value->getCityId()){?>,<?php }?>
					<?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['oGeoTarget']->value->getCityId()){?>
						<a href="<?php echo smarty_function_router(array('page'=>'people'),$_smarty_tpl);?>
city/<?php echo $_smarty_tpl->tpl_vars['oGeoTarget']->value->getCityId();?>
/" itemprop="locality"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileCity(), ENT_QUOTES, 'UTF-8', true);?>
</a>
					<?php }?>
				</td>
			</tr>
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['aUserFieldValues']->value){?>
			<?php  $_smarty_tpl->tpl_vars['oField'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oField']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aUserFieldValues']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oField']->key => $_smarty_tpl->tpl_vars['oField']->value){
$_smarty_tpl->tpl_vars['oField']->_loop = true;
?>
				<tr>
					<td class="cell-label"><i class="icon-contact icon-contact-<?php echo $_smarty_tpl->tpl_vars['oField']->value->getName();?>
"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oField']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
:</td>
					<td><?php echo $_smarty_tpl->tpl_vars['oField']->value->getValue(true,true);?>
</td>
				</tr>
			<?php } ?>
		<?php }?>

		<?php echo smarty_function_hook(array('run'=>'profile_whois_privat_item','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

	</table>
</div>
<?php }?>

<?php echo smarty_function_hook(array('run'=>'profile_whois_item_after_privat','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>




<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()!=$_smarty_tpl->tpl_vars['oUserProfile']->value->getId()){?>
	<section class="profile-info-note full-width">
		<?php if ($_smarty_tpl->tpl_vars['oUserNote']->value){?>
			<script type="text/javascript">
				ls.usernote.sText = <?php echo smarty_function_json(array('var'=>$_smarty_tpl->tpl_vars['oUserNote']->value->getText()),$_smarty_tpl);?>
;
			</script>
		<?php }?>
		
		<h2 class="header-table"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_note_header'];?>
</h2>

		<div id="usernote-note" class="profile-note" <?php if (!$_smarty_tpl->tpl_vars['oUserNote']->value){?>style="display: none;"<?php }?>>
			<p id="usernote-note-text">
				<?php if ($_smarty_tpl->tpl_vars['oUserNote']->value){?>
					<?php echo $_smarty_tpl->tpl_vars['oUserNote']->value->getText();?>

				<?php }?>
			</p>
			
			<ul class="actions clearfix">
				<li><a href="#" onclick="return ls.usernote.showForm();" class="link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_form_edit'];?>
</a></li>
				<li><a href="#" onclick="return ls.usernote.remove(<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
);" class="link-dotted"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_form_delete'];?>
</a></li>
			</ul>
		</div>
		
		<div id="usernote-form" style="display: none;">
			<p><textarea rows="4" cols="20" id="usernote-form-text" class="input-text input-width-full"></textarea></p><br />
			<button type="submit" onclick="return ls.usernote.save(<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
);" class="button button-primary"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_form_save'];?>
</button>&nbsp;&nbsp;
			<button type="submit" onclick="return ls.usernote.hideForm();" class="button"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_form_cancel'];?>
</button>
		</div>
		
		<a href="#" onclick="return ls.usernote.showForm();" id="usernote-button-add" class="link-dotted" <?php if ($_smarty_tpl->tpl_vars['oUserNote']->value){?>style="display:none;"<?php }?>><?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_note_add'];?>
</a>
	</section>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileAbout()){?>					
	<div class="profile-info-about">
		<h2 class="header-table"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_about'];?>
</h2>
		<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getProfileAbout();?>

	</div>
<?php }?>



<?php $_smarty_tpl->tpl_vars["aUserFieldContactValues"] = new Smarty_variable($_smarty_tpl->tpl_vars['oUserProfile']->value->getUserFieldValues(true,array('contact')), null, 0);?>
<?php if ($_smarty_tpl->tpl_vars['aUserFieldContactValues']->value){?>
	<div class="table-profile-info-wrapper">
		<h2 class="header-table"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_contacts'];?>
</h2>
		
		<table class="table table-profile-info">
			<?php  $_smarty_tpl->tpl_vars['oField'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oField']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aUserFieldContactValues']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oField']->key => $_smarty_tpl->tpl_vars['oField']->value){
$_smarty_tpl->tpl_vars['oField']->_loop = true;
?>
				<tr>
					<td class="cell-label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oField']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
:</td>
					<td><?php echo $_smarty_tpl->tpl_vars['oField']->value->getValue(true,true);?>
</td>
				</tr>
			<?php } ?>
		</table>
	</div>
<?php }?>


<?php $_smarty_tpl->tpl_vars["aUserFieldContactValues"] = new Smarty_variable($_smarty_tpl->tpl_vars['oUserProfile']->value->getUserFieldValues(true,array('social')), null, 0);?>
<?php if ($_smarty_tpl->tpl_vars['aUserFieldContactValues']->value){?>
<div class="table-profile-info-wrapper">
	<h2 class="header-table"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_social'];?>
</h2>
	
	<ul class="profile-contacts">
		<?php  $_smarty_tpl->tpl_vars['oField'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oField']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aUserFieldContactValues']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oField']->key => $_smarty_tpl->tpl_vars['oField']->value){
$_smarty_tpl->tpl_vars['oField']->_loop = true;
?>
			<li class="contact-<?php echo $_smarty_tpl->tpl_vars['oField']->value->getName();?>
">
				<?php echo $_smarty_tpl->tpl_vars['oField']->value->getValue(true,true);?>

			</li>
		<?php } ?>
	</ul>
</div>
<?php }?>



<?php if ($_smarty_tpl->tpl_vars['aUsersFriend']->value){?>
	<div class="table-profile-info-wrapper">
		<h2 class="header-table mb-15"><a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
friends/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_friends'];?>
</a> <?php echo $_smarty_tpl->tpl_vars['iCountFriendsUser']->value;?>
</h2>
		
		<?php echo $_smarty_tpl->getSubTemplate ('user_list_avatar.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('aUsersList'=>$_smarty_tpl->tpl_vars['aUsersFriend']->value), 0);?>

	</div>
<?php }?>


<?php echo smarty_function_hook(array('run'=>'profile_whois_item','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>



<div class="table-profile-info-wrapper">
	<h2 class="header-table"><a href="<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getUserWebPath();?>
stream/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_activity'];?>
</a></h2>

	<table class="table table-profile-info">
		<tr>
			<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_date_registration'];?>
:</td>
			<td><?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oUserProfile']->value->getDateRegister()),$_smarty_tpl);?>
</td>
		</tr>	
		
		
		<?php if ($_smarty_tpl->tpl_vars['oSession']->value){?>				
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_date_last'];?>
:</td>
				<td><?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oSession']->value->getDateLast()),$_smarty_tpl);?>
</td>
			</tr>
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('general.reg.invite')&&$_smarty_tpl->tpl_vars['oUserInviteFrom']->value){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_invite_from'];?>
:</td>
				<td>							       						
					<a href="<?php echo $_smarty_tpl->tpl_vars['oUserInviteFrom']->value->getUserWebPath();?>
"><?php echo $_smarty_tpl->tpl_vars['oUserInviteFrom']->value->getLogin();?>
</a>&nbsp;         					
				</td>
			</tr>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('general.reg.invite')&&$_smarty_tpl->tpl_vars['aUsersInvite']->value){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_invite_to'];?>
:</td>
				<td>
					<?php  $_smarty_tpl->tpl_vars['oUserInvite'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oUserInvite']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aUsersInvite']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oUserInvite']->key => $_smarty_tpl->tpl_vars['oUserInvite']->value){
$_smarty_tpl->tpl_vars['oUserInvite']->_loop = true;
?>        						
						<a href="<?php echo $_smarty_tpl->tpl_vars['oUserInvite']->value->getUserWebPath();?>
"><?php echo $_smarty_tpl->tpl_vars['oUserInvite']->value->getLogin();?>
</a>&nbsp; 
					<?php } ?>
				</td>
			</tr>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['aBlogsOwner']->value){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_blogs_self'];?>
:</td>
				<td>							
					<?php  $_smarty_tpl->tpl_vars['oBlog'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oBlog']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aBlogsOwner']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['oBlog']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['oBlog']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['oBlog']->key => $_smarty_tpl->tpl_vars['oBlog']->value){
$_smarty_tpl->tpl_vars['oBlog']->_loop = true;
 $_smarty_tpl->tpl_vars['oBlog']->iteration++;
 $_smarty_tpl->tpl_vars['oBlog']->last = $_smarty_tpl->tpl_vars['oBlog']->iteration === $_smarty_tpl->tpl_vars['oBlog']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['blog_owner']['last'] = $_smarty_tpl->tpl_vars['oBlog']->last;
?>
						<a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oBlog']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['blog_owner']['last']){?>, <?php }?>								      		
					<?php } ?>
				</td>
			</tr>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['aBlogAdministrators']->value){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_blogs_administration'];?>
:</td>
				<td>
					<?php  $_smarty_tpl->tpl_vars['oBlogUser'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oBlogUser']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aBlogAdministrators']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['oBlogUser']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['oBlogUser']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['oBlogUser']->key => $_smarty_tpl->tpl_vars['oBlogUser']->value){
$_smarty_tpl->tpl_vars['oBlogUser']->_loop = true;
 $_smarty_tpl->tpl_vars['oBlogUser']->iteration++;
 $_smarty_tpl->tpl_vars['oBlogUser']->last = $_smarty_tpl->tpl_vars['oBlogUser']->iteration === $_smarty_tpl->tpl_vars['oBlogUser']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['blog_user']['last'] = $_smarty_tpl->tpl_vars['oBlogUser']->last;
?>
						<?php $_smarty_tpl->tpl_vars["oBlog"] = new Smarty_variable($_smarty_tpl->tpl_vars['oBlogUser']->value->getBlog(), null, 0);?>
						<a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oBlog']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['blog_user']['last']){?>, <?php }?>
					<?php } ?>
				</td>
			</tr>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['aBlogModerators']->value){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_blogs_moderation'];?>
:</td>
				<td>
					<?php  $_smarty_tpl->tpl_vars['oBlogUser'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oBlogUser']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aBlogModerators']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['oBlogUser']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['oBlogUser']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['oBlogUser']->key => $_smarty_tpl->tpl_vars['oBlogUser']->value){
$_smarty_tpl->tpl_vars['oBlogUser']->_loop = true;
 $_smarty_tpl->tpl_vars['oBlogUser']->iteration++;
 $_smarty_tpl->tpl_vars['oBlogUser']->last = $_smarty_tpl->tpl_vars['oBlogUser']->iteration === $_smarty_tpl->tpl_vars['oBlogUser']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['blog_user']['last'] = $_smarty_tpl->tpl_vars['oBlogUser']->last;
?>
						<?php $_smarty_tpl->tpl_vars["oBlog"] = new Smarty_variable($_smarty_tpl->tpl_vars['oBlogUser']->value->getBlog(), null, 0);?>
						<a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oBlog']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['blog_user']['last']){?>, <?php }?>
					<?php } ?>
				</td>
			</tr>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['aBlogUsers']->value){?>
			<tr>
				<td class="cell-label"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_blogs_join'];?>
:</td>
				<td>
					<?php  $_smarty_tpl->tpl_vars['oBlogUser'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oBlogUser']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aBlogUsers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['oBlogUser']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['oBlogUser']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['oBlogUser']->key => $_smarty_tpl->tpl_vars['oBlogUser']->value){
$_smarty_tpl->tpl_vars['oBlogUser']->_loop = true;
 $_smarty_tpl->tpl_vars['oBlogUser']->iteration++;
 $_smarty_tpl->tpl_vars['oBlogUser']->last = $_smarty_tpl->tpl_vars['oBlogUser']->iteration === $_smarty_tpl->tpl_vars['oBlogUser']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['blog_user']['last'] = $_smarty_tpl->tpl_vars['oBlogUser']->last;
?>
						<?php $_smarty_tpl->tpl_vars["oBlog"] = new Smarty_variable($_smarty_tpl->tpl_vars['oBlogUser']->value->getBlog(), null, 0);?>
						<a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oBlog']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['blog_user']['last']){?>, <?php }?>
					<?php } ?>
				</td>
			</tr>
		<?php }?>
		
		<?php echo smarty_function_hook(array('run'=>'profile_whois_activity_item','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>

	</table>
</div>

<?php echo smarty_function_hook(array('run'=>'profile_whois_item_end','oUserProfile'=>$_smarty_tpl->tpl_vars['oUserProfile']->value),$_smarty_tpl);?>



<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>