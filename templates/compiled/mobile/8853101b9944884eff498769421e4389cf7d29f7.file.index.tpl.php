<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:19
         compiled from "/var/www/bunker//templates/skin/mobile/actions/ActionBlogs/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:146210875684d6f78f4c36-49011927%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8853101b9944884eff498769421e4389cf7d29f7' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/actions/ActionBlogs/index.tpl',
      1 => 1449125154,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '146210875684d6f78f4c36-49011927',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aLang' => 0,
    'sBlogsRootPage' => 0,
    'aPaging' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6f79337a4_44664669',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6f79337a4_44664669')) {function content_5684d6f79337a4_44664669($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('sMenuHeadItemSelect'=>"blogs"), 0);?>



<form action="" method="POST" id="form-blogs-search" onsubmit="return false;" class="search search-item no-mg">
	<input type="text" placeholder="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['blogs_search_title_hint'];?>
" autocomplete="off" name="blog_title" class="input-text" value="" onkeyup="ls.timer.run(ls.blog.searchBlogs,'blogs_search',['form-blogs-search'],1000);">
</form>

<div id="blogs-list-search" style="display:none;"></div>

<div id="blogs-list-original">
	<?php echo smarty_function_router(array('page'=>'blogs','assign'=>'sBlogsRootPage'),$_smarty_tpl);?>

	<?php echo $_smarty_tpl->getSubTemplate ('blog_list.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('bBlogsUseOrder'=>true,'sBlogsRootPage'=>$_smarty_tpl->tpl_vars['sBlogsRootPage']->value), 0);?>

	<?php echo $_smarty_tpl->getSubTemplate ('paging.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('aPaging'=>$_smarty_tpl->tpl_vars['aPaging']->value), 0);?>

</div>


<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>