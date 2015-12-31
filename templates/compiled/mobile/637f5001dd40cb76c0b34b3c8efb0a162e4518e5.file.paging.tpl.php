<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:19
         compiled from "/var/www/bunker//templates/skin/mobile/paging.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5707322165684d6f7a1cc54-81307896%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '637f5001dd40cb76c0b34b3c8efb0a162e4518e5' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/paging.tpl',
      1 => 1449125145,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5707322165684d6f7a1cc54-81307896',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aPaging' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6f7a7d4a4_67692430',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6f7a7d4a4_67692430')) {function content_5684d6f7a7d4a4_67692430($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
?><?php if ($_smarty_tpl->tpl_vars['aPaging']->value&&$_smarty_tpl->tpl_vars['aPaging']->value['iCountPage']>1){?> 
	<div class="pagination">
		<?php if ($_smarty_tpl->tpl_vars['aPaging']->value['iPrevPage']){?>
			<a href="<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sBaseUrl'];?>
/page<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['iPrevPage'];?>
/<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sGetParams'];?>
" class="pagination-arrow pagination-arrow-prev js-paging-prev-page" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_previos'];?>
"><span></span></a>
		<?php }else{ ?>
			<div class="pagination-arrow pagination-arrow-prev inactive" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_previos'];?>
"><span></span></div>
		<?php }?>
		
		<?php ob_start();?><?php echo smarty_function_cfg(array('name'=>'module.topic.per_page'),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["iPerPage"] = new Smarty_variable($_tmp1, null, 0);?>
		<div class="pagination-current"><span><?php echo $_smarty_tpl->tpl_vars['aPaging']->value['iCurrentPage'];?>
</span> <?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_out_of'];?>
 <?php echo $_smarty_tpl->tpl_vars['aPaging']->value['iCountPage'];?>
</div>
		
		<?php if ($_smarty_tpl->tpl_vars['aPaging']->value['iNextPage']){?>
			<a href="<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sBaseUrl'];?>
/page<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['iNextPage'];?>
/<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sGetParams'];?>
" class="pagination-arrow pagination-arrow-next js-paging-next-page" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_next'];?>
"><span></span></a>
		<?php }else{ ?>
			<div class="pagination-arrow pagination-arrow-next inactive" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_next'];?>
"><span></span></div>
		<?php }?>			
	</div>
<?php }?><?php }} ?>