<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:59
         compiled from "/var/www/bunker//templates/skin/mobile/header_top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9942875805684d6e3d7b475-79422890%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fb3450ea71406bed58f2e94d68f255a57cb34e29' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/header_top.tpl',
      1 => 1451472201,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9942875805684d6e3d7b475-79422890',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'iUserCurrentCountTalkNew' => 0,
    'oUserCurrent' => 0,
    'oTopic' => 0,
    'aLang' => 0,
    'iMaxIdComment' => 0,
    'aPagingCmt' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e3de5ca2_16440131',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e3de5ca2_16440131')) {function content_5684d6e3de5ca2_16440131($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><nav id="header" class="clearfix">
	<div class="icon-userbar userbar-trigger" id="userbar-trigger"></div>

	<h1 class="site-name" <?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value){?>style="margin-right: 132px"<?php }?>><a href="<?php echo smarty_function_cfg(array('name'=>'path.root.web'),$_smarty_tpl);?>
"><?php echo smarty_function_cfg(array('name'=>'view.name'),$_smarty_tpl);?>
</a></h1>

	<?php echo smarty_function_hook(array('run'=>'userbar_nav'),$_smarty_tpl);?>

	
	<ul class="nav-userbar">
		<?php echo smarty_function_hook(array('run'=>'userbar_item'),$_smarty_tpl);?>


		<?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value){?>
			<li class="item-messages slide-trigger" id="item-messages" onclick="ls.tools.slide($('#messages'), $(this), true);"><a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
"></a></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oTopic']->value){?>
        <li class="new-comments" id="new_comments_counter" style="display: none;" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_count_new'];?>
" onclick="ls.comments.goToNextComment(); return false;"></li>
		<li class="update-comments" onclick="ls.comments.load(<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
,'topic'); return false;"><i id="update-comments" class="fa fa-refresh"></i></li>
		

		<input type="hidden" id="comment_last_id" value="<?php echo $_smarty_tpl->tpl_vars['iMaxIdComment']->value;?>
" />
		<input type="hidden" id="comment_use_paging" value="<?php if ($_smarty_tpl->tpl_vars['aPagingCmt']->value&&$_smarty_tpl->tpl_vars['aPagingCmt']->value['iCountPage']>1){?>1<?php }?>" />
<?php }?>
<li onclick="ls.toolbar.scroll.goUp()" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['toolbar_scrollup_go'];?>
" class="toolbar-topic-prev"><i class="fa fa-chevron-up"></i></li>
	<li onclick="ls.toolbar.scroll.goDown()" title="Вниз" class="toolbar-topic-prev"><i class="fa fa-chevron-down"></i></li>
		<li class="item-search slide-trigger" id="item-search" onclick="ls.tools.slide($('#search'), $(this), true);"></li>

		<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
			<li class="item-submit item-primary slide-trigger" id="item-submit" onclick="ls.tools.slide($('#write'), $(this), true);"></li>
		<?php }else{ ?>
			<li class="item-auth item-primary slide-trigger" id="item-auth" onclick="ls.tools.slide($('#window_login_form'), $(this), true);"></li>
		<?php }?>
	</ul>
</nav>

<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
	<?php echo $_smarty_tpl->getSubTemplate ('window_write.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }else{ ?>
	<?php echo $_smarty_tpl->getSubTemplate ('window_login.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?>


<form action="<?php echo smarty_function_router(array('page'=>'search'),$_smarty_tpl);?>
topics/" class="slide search-header" id="search">
	<div class="input-holder input-holder-text">
		<input type="text" placeholder="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['search'];?>
" maxlength="255" name="q" class="input-text input-width-full">
	</div>
	<div class="input-holder">
		<button type="submit" class="button button-primary"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['search_submit'];?>
</button>
	</div>
</form>


<?php echo smarty_function_hook(array('run'=>'header_banner_begin'),$_smarty_tpl);?>

<?php echo smarty_function_hook(array('run'=>'header_banner_end'),$_smarty_tpl);?>
<?php }} ?>