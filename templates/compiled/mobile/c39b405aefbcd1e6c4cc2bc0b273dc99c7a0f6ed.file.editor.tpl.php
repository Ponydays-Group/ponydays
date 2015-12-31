<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:00
         compiled from "/var/www/bunker//templates/skin/mobile/editor.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2920656255684d6e402fa06-57226997%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c39b405aefbcd1e6c4cc2bc0b273dc99c7a0f6ed' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/editor.tpl',
      1 => 1449125144,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2920656255684d6e402fa06-57226997',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oConfig' => 0,
    'sSettingsTinymce' => 0,
    'sImgToLoad' => 0,
    'sSettingsMarkitup' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e40788a6_62890551',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e40788a6_62890551')) {function content_5684d6e40788a6_62890551($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
if (!is_callable('smarty_function_lang_load')) include '/var/www/bunker//engine/modules/viewer/plugs/function.lang_load.php';
?><?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('view.tinymce')){?>
	<?php if (!$_smarty_tpl->tpl_vars['sSettingsTinymce']->value){?>
		<?php $_smarty_tpl->tpl_vars["sSettingsTinymce"] = new Smarty_variable("ls.settings.getTinymce()", null, 0);?>
	<?php }?>

	<script src="<?php echo smarty_function_cfg(array('name'=>'path.root.engine_lib'),$_smarty_tpl);?>
/external/tinymce-jq/tiny_mce.js"></script>
	<script type="text/javascript">
		jQuery(function($){
			tinyMCE.init(<?php echo $_smarty_tpl->tpl_vars['sSettingsTinymce']->value;?>
);
		});
	</script>
<?php }else{ ?>
	<?php if (!$_smarty_tpl->tpl_vars['sImgToLoad']->value){?>
		<?php $_smarty_tpl->tpl_vars["sImgToLoad"] = new Smarty_variable("topic_text", null, 0);?>
	<?php }?>
	<?php echo $_smarty_tpl->getSubTemplate ('window_load_img.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('sToLoad'=>$_smarty_tpl->tpl_vars['sImgToLoad']->value), 0);?>


	<?php if (!$_smarty_tpl->tpl_vars['sSettingsTinymce']->value){?>
		<?php $_smarty_tpl->tpl_vars["sSettingsMarkitup"] = new Smarty_variable("ls.settings.getMarkitup()", null, 0);?>
	<?php }?>
	<script type="text/javascript">
		jQuery(function($){
			ls.lang.load(<?php echo smarty_function_lang_load(array('name'=>"panel_b,panel_i,panel_u,panel_s,panel_url,panel_url_promt,panel_code,panel_video,panel_image,panel_cut,panel_quote,panel_list,panel_list_ul,panel_list_ol,panel_title,panel_clear_tags,panel_video_promt,panel_list_li,panel_image_promt,panel_user,panel_user_promt"),$_smarty_tpl);?>
);
			// Подключаем редактор
			$('.markitup-editor').markItUp(<?php echo $_smarty_tpl->tpl_vars['sSettingsMarkitup']->value;?>
);
		});
	</script>
<?php }?><?php }} ?>