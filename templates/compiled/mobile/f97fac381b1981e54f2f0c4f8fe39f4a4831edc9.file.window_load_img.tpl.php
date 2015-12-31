<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:00
         compiled from "/var/www/bunker//templates/skin/mobile/window_load_img.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3694327165684d6e407d197-55379771%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f97fac381b1981e54f2f0c4f8fe39f4a4831edc9' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/window_load_img.tpl',
      1 => 1449125147,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3694327165684d6e407d197-55379771',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aLang' => 0,
    'sToLoad' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e40fa1b5_90341038',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e40fa1b5_90341038')) {function content_5684d6e40fa1b5_90341038($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><div class="modal modal-image-upload" id="window_upload_img">
	<header class="modal-header">
		<h3><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg'];?>
</h3>
		<a href="#" class="close jqmClose"></a>
	</header>
	
	<div class="modal-content">
		<ul class="nav nav-pills nav-pills-tabs">
			<li class="active js-block-upload-img-item" data-type="pc"><a href="#"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_from_pc'];?>
</a></li>
			<li class="js-block-upload-img-item" data-type="link"><a href="#"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_from_link'];?>
</a></li>
		</ul>
	
		<form method="POST" action="" enctype="multipart/form-data" id="block_upload_img_content_pc" onsubmit="return false;" class="tab-content js-block-upload-img-content" data-type="pc">
			<p><label for="img_file"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_file'];?>
:</label>
			<input type="file" name="img_file" id="img_file" value="" class="input-text input-width-full" /></p>
			
			<?php echo smarty_function_hook(array('run'=>"uploadimg_source"),$_smarty_tpl);?>

			
			<p>
				<label for="form-image-align"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align'];?>
:</label>
				<select name="align" id="form-image-align" class="input-width-full">
					<option value=""><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align_no'];?>
</option>
					<option value="left"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align_left'];?>
</option>
					<option value="right"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align_right'];?>
</option>
					<option value="center"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align_center'];?>
</option>
				</select>
			</p>
			
			<p><label for="form-image-title"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_title'];?>
:</label>
			<input type="text" name="title" id="form-image-title" value="" class="input-text input-width-full" /></p>
			
			<?php echo smarty_function_hook(array('run'=>"uploadimg_additional"),$_smarty_tpl);?>

			
			<button type="submit" class="button button-primary" onclick="ls.ajaxUploadImg('block_upload_img_content_pc','<?php echo $_smarty_tpl->tpl_vars['sToLoad']->value;?>
');"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_submit'];?>
</button>
			<button type="submit" class="button jqmClose"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_cancel'];?>
</button>
		</form>
		
		
		<form method="POST" action="" enctype="multipart/form-data" id="block_upload_img_content_link" onsubmit="return false;" style="display: none;" class="tab-content js-block-upload-img-content" data-type="link">
			<p><label for="img_file"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_url'];?>
:</label>
			<input type="text" name="img_url" id="img_url" value="http://" class="input-text input-width-full" /></p>

			<p>
				<label for="form-image-url-align"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align'];?>
:</label>
				<select name="align" id="form-image-url-align" class="input-width-full">
					<option value=""><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align_no'];?>
</option>
					<option value="left"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align_left'];?>
</option>
					<option value="right"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align_right'];?>
</option>
					<option value="center"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_align_center'];?>
</option>
				</select>
			</p>

			<p><label for="form-image-url-title"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_title'];?>
:</label>
				<input type="text" name="title" id="form-image-url-title" value="" class="input-text input-width-full" /></p>

			<?php echo smarty_function_hook(array('run'=>"uploadimg_link_additional"),$_smarty_tpl);?>


			<button type="submit" class="button button-primary" onclick="ls.topic.insertImageToEditor(jQuery('#img_url').val(),jQuery('#form-image-url-align').val(),jQuery('#form-image-url-title').val());"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_link_submit_paste'];?>
</button>
			<?php echo $_smarty_tpl->tpl_vars['aLang']->value['or'];?>

			<button type="submit" class="button button-primary" onclick="ls.ajaxUploadImg('block_upload_img_content_link','<?php echo $_smarty_tpl->tpl_vars['sToLoad']->value;?>
');"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_link_submit_load'];?>
</button>
			<button type="submit" class="button jqmClose"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['uploadimg_cancel'];?>
</button>
		</form>
	</div>
</div>
	<?php }} ?>