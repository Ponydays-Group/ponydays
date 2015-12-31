<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:53
         compiled from "/var/www/bunker/plugins/page/templates/skin/default/actions/ActionPage/add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20221129145684d719234dc3-42435341%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '67e3efba64e4c6c33fece667b1c821120c0d6672' => 
    array (
      0 => '/var/www/bunker/plugins/page/templates/skin/default/actions/ActionPage/add.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20221129145684d719234dc3-42435341',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oConfig' => 0,
    'LIVESTREET_SECURITY_KEY' => 0,
    'aLang' => 0,
    'aPages' => 0,
    'oPage' => 0,
    '_aRequest' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d71935b011_71201163',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d71935b011_71201163')) {function content_5684d71935b011_71201163($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
if (!is_callable('smarty_function_lang_load')) include '/var/www/bunker//engine/modules/viewer/plugs/function.lang_load.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><?php echo $_smarty_tpl->getSubTemplate ('window_load_img.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('sToLoad'=>'page_text'), 0);?>



<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('view.tinymce')){?>
	<script type="text/javascript" src="<?php echo smarty_function_cfg(array('name'=>'path.root.engine_lib'),$_smarty_tpl);?>
/external/tinymce-jq/tiny_mce.js"></script>
	
		<script type="text/javascript">
			jQuery(function($){
				tinyMCE.init(ls.settings.getTinymce());
			});
		</script>
	

<?php }else{ ?>
	<?php echo $_smarty_tpl->getSubTemplate ('window_load_img.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('sToLoad'=>'page_text'), 0);?>

	<script type="text/javascript">
		jQuery(function($){
			ls.lang.load(<?php echo smarty_function_lang_load(array('name'=>"panel_b,panel_i,panel_u,panel_s,panel_url,panel_url_promt,panel_code,panel_video,panel_image,panel_cut,panel_quote,panel_list,panel_list_ul,panel_list_ol,panel_title,panel_clear_tags,panel_video_promt,panel_list_li,panel_image_promt,panel_user,panel_user_promt"),$_smarty_tpl);?>
);
			// Подключаем редактор
			$('#page_text').markItUp(ls.settings.getMarkitup());
		});
	</script>
<?php }?>


<form action="" method="POST">
	<?php echo smarty_function_hook(array('run'=>'plugin_page_form_add_begin'),$_smarty_tpl);?>

	<input type="hidden" name="security_ls_key" value="<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
" />

	<p><label for="page_pid"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_parent_page'];?>
</label>
	<select name="page_pid" id="page_pid" class="input-width-300">
		<option value="0"></option>
		<?php  $_smarty_tpl->tpl_vars['oPage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oPage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aPages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oPage']->key => $_smarty_tpl->tpl_vars['oPage']->value){
$_smarty_tpl->tpl_vars['oPage']->_loop = true;
?>
			<option style="margin-left: <?php echo $_smarty_tpl->tpl_vars['oPage']->value->getLevel()*20;?>
px;" value="<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getId();?>
" <?php if ($_smarty_tpl->tpl_vars['_aRequest']->value['page_pid']==$_smarty_tpl->tpl_vars['oPage']->value->getId()){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['oPage']->value->getTitle();?>
(/<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getUrlFull();?>
/)</option>
		<?php } ?>
	</select></p>


	<p><label for="page_title"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_title'];?>
:</label>
	<input type="text" id="page_title" class="input-text input-width-full" name="page_title" value="<?php echo $_smarty_tpl->tpl_vars['_aRequest']->value['page_title'];?>
" class="input-wide" />	</p>


	<p><label for="page_url"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_url'];?>
:</label>
	<input type="text" class="input-text input-width-full" id="page_url" name="page_url" value="<?php echo $_smarty_tpl->tpl_vars['_aRequest']->value['page_url'];?>
" class="input-wide" />	</p>


	<label for="page_text"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_text'];?>
:</label>
	<textarea name="page_text" id="page_text" rows="20" class="mce-editor input-width-full"><?php echo $_smarty_tpl->tpl_vars['_aRequest']->value['page_text'];?>
</textarea><br />

	<p><label for="page_seo_keywords"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_seo_keywords'];?>
:</label>
	<input type="text" class="input-text input-width-full" id="page_seo_keywords" name="page_seo_keywords" value="<?php echo $_smarty_tpl->tpl_vars['_aRequest']->value['page_seo_keywords'];?>
" class="input-wide" />
	<span class="note"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_seo_keywords_notice'];?>
</span></p>

	<p><label for="page_seo_description"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_seo_description'];?>
:</label>
	<input type="text" class="input-text input-width-full" id="page_seo_description" name="page_seo_description" value="<?php echo $_smarty_tpl->tpl_vars['_aRequest']->value['page_seo_description'];?>
" class="input-wide" />
	<span class="note"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_seo_description_notice'];?>
</span></p>

	<p><label for="page_sort"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_sort'];?>
:</label>
	<input type="text" id="page_sort" class="input-text input-width-full" name="page_sort" value="<?php echo $_smarty_tpl->tpl_vars['_aRequest']->value['page_sort'];?>
" class="input-wide" />
	<span class="note"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_sort_notice'];?>
</span></p>

	<p>
		<label><input type="checkbox" id="page_auto_br" name="page_auto_br" value="1" class="input-checkbox" <?php if ($_smarty_tpl->tpl_vars['_aRequest']->value['page_auto_br']==1){?>checked<?php }?>/> <?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_auto_br'];?>
</label>
		<label><input type="checkbox" id="page_active" name="page_active" value="1" class="input-checkbox" <?php if ($_smarty_tpl->tpl_vars['_aRequest']->value['page_active']==1){?>checked<?php }?> /> <?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_active'];?>
</label>
		<label><input type="checkbox" id="page_main" name="page_main" value="1" class="input-checkbox" <?php if ($_smarty_tpl->tpl_vars['_aRequest']->value['page_main']==1){?>checked<?php }?> /> <?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_main'];?>
</label>
	</p>

	<?php echo smarty_function_hook(array('run'=>'plugin_page_form_add_end'),$_smarty_tpl);?>

	<p>
		<button type="submit" class="button button-primary" name="submit_page_save"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_submit_save'];?>
</button>
		<button name="submit_page_cancel" class="button" onclick="window.location='<?php echo smarty_function_router(array('page'=>'page'),$_smarty_tpl);?>
admin/'; return false;" /><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create_submit_cancel'];?>
</button>
	</p>

	<input type="hidden" name="page_id" value="<?php echo $_smarty_tpl->tpl_vars['_aRequest']->value['page_id'];?>
">
</form><?php }} ?>