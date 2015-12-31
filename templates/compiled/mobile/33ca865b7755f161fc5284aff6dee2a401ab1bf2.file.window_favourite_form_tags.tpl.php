<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:59
         compiled from "/var/www/bunker//templates/skin/mobile/window_favourite_form_tags.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17873083805684d6e3c07151-54255718%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '33ca865b7755f161fc5284aff6dee2a401ab1bf2' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/window_favourite_form_tags.tpl',
      1 => 1449125147,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17873083805684d6e3c07151-54255718',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserCurrent' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e3c1eb06_06099437',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e3c1eb06_06099437')) {function content_5684d6e3c1eb06_06099437($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
	<div id="favourite-form-tags" class="slide slide-bg-grey">
		<form onsubmit="return ls.favourite.saveTags(this);" class="modal-content">
			<input type="hidden" name="target_type" value="" id="favourite-form-tags-target-type">
			<input type="hidden" name="target_id" value="" id="favourite-form-tags-target-id">

			<p><input type="text" name="tags" value="" id="favourite-form-tags-tags" class="autocomplete-tags-sep input-text input-width-full"></p>
			<button type="submit" name="" class="button button-primary" /><?php echo $_smarty_tpl->tpl_vars['aLang']->value['favourite_form_tags_button_save'];?>
</button>
			<button type="submit" name="" class="button jqmClose" /><?php echo $_smarty_tpl->tpl_vars['aLang']->value['favourite_form_tags_button_cancel'];?>
</button>
		</form>
	</div>
<?php }?><?php }} ?>