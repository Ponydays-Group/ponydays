<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:31
         compiled from "/var/www/bunker//templates/skin/mobile/actions/ActionProfile/friend_item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10334506965684d703568956-60846770%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '846baff716e38cc7a05accf6aad5d8120bb209c9' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/actions/ActionProfile/friend_item.tpl',
      1 => 1449125157,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10334506965684d703568956-60846770',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserFriend' => 0,
    'USER_FRIEND_ACCEPT' => 0,
    'USER_FRIEND_OFFER' => 0,
    'aLang' => 0,
    'oUserProfile' => 0,
    'USER_FRIEND_REJECT' => 0,
    'oUserCurrent' => 0,
    'USER_FRIEND_NULL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d7036523d4_85626724',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d7036523d4_85626724')) {function content_5684d7036523d4_85626724($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['oUserFriend']->value&&($_smarty_tpl->tpl_vars['oUserFriend']->value->getFriendStatus()==$_smarty_tpl->tpl_vars['USER_FRIEND_ACCEPT']->value+$_smarty_tpl->tpl_vars['USER_FRIEND_OFFER']->value||$_smarty_tpl->tpl_vars['oUserFriend']->value->getFriendStatus()==$_smarty_tpl->tpl_vars['USER_FRIEND_ACCEPT']->value+$_smarty_tpl->tpl_vars['USER_FRIEND_ACCEPT']->value)){?>
	<li id="delete_friend_item"><a href="#" class="icon-friend active" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_friend_del'];?>
" onclick="return ls.user.removeFriend(this,<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
,'del');"></a></li>
<?php }elseif($_smarty_tpl->tpl_vars['oUserFriend']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getStatusTo()==$_smarty_tpl->tpl_vars['USER_FRIEND_REJECT']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getStatusFrom()==$_smarty_tpl->tpl_vars['USER_FRIEND_OFFER']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getUserTo()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()){?>
	<li id="add_friend_item"><a href="#" class="icon-friend" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_friend_add'];?>
" onclick="return ls.user.addFriend(this,<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
,'accept');"></a></li>
<?php }elseif($_smarty_tpl->tpl_vars['oUserFriend']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getFriendStatus()==$_smarty_tpl->tpl_vars['USER_FRIEND_OFFER']->value+$_smarty_tpl->tpl_vars['USER_FRIEND_REJECT']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getUserTo()!=$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()){?>
									
<?php }elseif($_smarty_tpl->tpl_vars['oUserFriend']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getFriendStatus()==$_smarty_tpl->tpl_vars['USER_FRIEND_OFFER']->value+$_smarty_tpl->tpl_vars['USER_FRIEND_NULL']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getUserFrom()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()){?>
	<li><i class="icon-friend-waiting"></i></li>						
<?php }elseif($_smarty_tpl->tpl_vars['oUserFriend']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getFriendStatus()==$_smarty_tpl->tpl_vars['USER_FRIEND_OFFER']->value+$_smarty_tpl->tpl_vars['USER_FRIEND_NULL']->value&&$_smarty_tpl->tpl_vars['oUserFriend']->value->getUserTo()==$_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()){?>
	<li id="add_friend_item"><a href="#" class="icon-friend" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_friend_add'];?>
" onclick="return ls.user.addFriend(this,<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
,'accept');"></a></li>
<?php }elseif(!$_smarty_tpl->tpl_vars['oUserFriend']->value){?>
	<li id="add_friend_item"><a href="#" class="icon-friend" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_friend_add'];?>
" id="add_friend_show" onclick="jQuery('#add_friend_form').slideToggle(); jQuery('#add_friend_form textarea').focus(); return false;"></a></li>
<?php }else{ ?>
	<li id="add_friend_item"><a href="#" class="icon-friend" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_friend_add'];?>
" onclick="return ls.user.addFriend(this,<?php echo $_smarty_tpl->tpl_vars['oUserProfile']->value->getId();?>
,'link');"></a></li>
<?php }?><?php }} ?>