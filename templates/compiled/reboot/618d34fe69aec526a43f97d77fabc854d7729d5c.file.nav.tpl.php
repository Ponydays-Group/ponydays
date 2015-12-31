<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/nav.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21215739345684d6cb0e4e35-36277549%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '618d34fe69aec526a43f97d77fabc854d7729d5c' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/nav.tpl',
      1 => 1451543931,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21215739345684d6cb0e4e35-36277549',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserCurrent' => 0,
    'sMenuHeadItemSelect' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb14f186_15764761',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb14f186_15764761')) {function content_5684d6cb14f186_15764761($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><nav id="nav">
	<ul class="nav nav-main">
		<?php if (!$_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
			<li <?php if ($_smarty_tpl->tpl_vars['sMenuHeadItemSelect']->value=='blog'){?>class="active"<?php }?>><a href="<?php echo smarty_function_cfg(array('name'=>'path.root.web'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_title'];?>
</a></li>
		<?php }else{ ?>
			<li <?php if ($_smarty_tpl->tpl_vars['sMenuHeadItemSelect']->value=='blog'){?>class="active"<?php }?>><a href="<?php echo smarty_function_cfg(array('name'=>'path.root.web'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['userfeed_title'];?>
</a></li>
		<?php }?>
		<li <?php if ($_smarty_tpl->tpl_vars['sMenuHeadItemSelect']->value=='blogs'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'blogs'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['blogs'];?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['sMenuHeadItemSelect']->value=='people'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'people'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['people'];?>
</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['sMenuHeadItemSelect']->value=='stream'){?>class="active"<?php }?>><a href="<?php echo smarty_function_router(array('page'=>'stream'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['stream_menu'];?>
</a></li>
		<li><a href="http://freepony.ru/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['freepony'];?>
</a></li>

		<li class="quote" style="float: left;">
		<a href="#" style="padding-top: 3px; padding-bottom" 0px;>
<img src="<?php echo smarty_function_cfg(array('name'=>"path.static.skin"),$_smarty_tpl);?>
/images/woona.gif">
</a>
</li>	
	<li class="quote" style="float: left;">
		<a href="#">
		<h1>
		<em>
<?php echo $_smarty_tpl->getSubTemplate ("quote.php", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</em>
</h1>
</a>
</li>
	</ul>
	<?php echo smarty_function_hook(array('run'=>'main_menu'),$_smarty_tpl);?>

</nav>
<?php }} ?>