<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker/templates/skin/reboot/toolbar_scrollup.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9888206445684d6ccd9c317-45458848%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1d3991880e0b9770398ef0cebe7fa35b01250b79' => 
    array (
      0 => '/var/www/bunker/templates/skin/reboot/toolbar_scrollup.tpl',
      1 => 1451291089,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9888206445684d6ccd9c317-45458848',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6ccda1a77_63586718',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6ccda1a77_63586718')) {function content_5684d6ccda1a77_63586718($_smarty_tpl) {?><section class="toolbar-up" id="toolbar_up">
	<a href="#" style="margin-top: 30px;" onclick="ls.toolbar.scroll.goUp()" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['toolbar_scrollup_go'];?>
" class="toolbar-topic-prev"><i class="fa fa-chevron-up"></i></a>
</section>
<section class="toolbar-down" id="toolbar_down">
	<a href="#" onclick="ls.toolbar.scroll.goDown()" title="Вниз" class="toolbar-topic-prev"><i class="fa fa-chevron-down"></i></a>
</section>
<?php }} ?>