<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/window_favourite_form_tags.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19472574255684d6cb0cdda2-10442646%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'df55015b5061936f42ace22d70ad1dafd5129e6c' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/window_favourite_form_tags.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19472574255684d6cb0cdda2-10442646',
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
  'unifunc' => 'content_5684d6cb0e0372_15637226',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb0e0372_15637226')) {function content_5684d6cb0e0372_15637226($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
	<div id="favourite-form-tags" class="modal">
		<header class="modal-header">
			<h3><?php echo $_smarty_tpl->tpl_vars['aLang']->value['add_favourite_tags'];?>
</h3>
			<a href="#" class="close jqmClose"></a>
		</header>
		
		
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