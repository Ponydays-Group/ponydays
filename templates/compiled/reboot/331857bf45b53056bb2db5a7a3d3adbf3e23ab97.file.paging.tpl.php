<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:38
         compiled from "/var/www/bunker//templates/skin/reboot/paging.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12274092695684d70a3a1eb3-30637163%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '331857bf45b53056bb2db5a7a3d3adbf3e23ab97' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/paging.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12274092695684d70a3a1eb3-30637163',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aPaging' => 0,
    'aLang' => 0,
    'iPage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70a43d979_03364783',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70a43d979_03364783')) {function content_5684d70a43d979_03364783($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['aPaging']->value&&$_smarty_tpl->tpl_vars['aPaging']->value['iCountPage']>1){?> 
	<div class="pagination">
		<ul>
			<?php if ($_smarty_tpl->tpl_vars['aPaging']->value['iCurrentPage']>1){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sBaseUrl'];?>
/<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sGetParams'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_first'];?>
"><<</a></li><?php }?>
			
			
			<?php if ($_smarty_tpl->tpl_vars['aPaging']->value['iPrevPage']){?>
				<li><a href="<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sBaseUrl'];?>
/page<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['iPrevPage'];?>
/<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sGetParams'];?>
" class="js-paging-prev-page" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_previos'];?>
"><</a></li>
			<?php }?>
			
			
			<?php  $_smarty_tpl->tpl_vars['iPage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['iPage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aPaging']->value['aPagesLeft']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['iPage']->key => $_smarty_tpl->tpl_vars['iPage']->value){
$_smarty_tpl->tpl_vars['iPage']->_loop = true;
?>
				<li><a href="<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sBaseUrl'];?>
/page<?php echo $_smarty_tpl->tpl_vars['iPage']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sGetParams'];?>
"><?php echo $_smarty_tpl->tpl_vars['iPage']->value;?>
</a></li>
			<?php } ?>
			
			<li class="active"><span><?php echo $_smarty_tpl->tpl_vars['aPaging']->value['iCurrentPage'];?>
</span></li>
			
			<?php  $_smarty_tpl->tpl_vars['iPage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['iPage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aPaging']->value['aPagesRight']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['iPage']->key => $_smarty_tpl->tpl_vars['iPage']->value){
$_smarty_tpl->tpl_vars['iPage']->_loop = true;
?>
				<li><a href="<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sBaseUrl'];?>
/page<?php echo $_smarty_tpl->tpl_vars['iPage']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sGetParams'];?>
"><?php echo $_smarty_tpl->tpl_vars['iPage']->value;?>
</a></li>
			<?php } ?>
			
			
			<?php if ($_smarty_tpl->tpl_vars['aPaging']->value['iNextPage']){?>
				<li><a href="<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sBaseUrl'];?>
/page<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['iNextPage'];?>
/<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sGetParams'];?>
" class="js-paging-next-page" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_next'];?>
">></a></li>
			<?php }?>
			
			
			<?php if ($_smarty_tpl->tpl_vars['aPaging']->value['iCurrentPage']<$_smarty_tpl->tpl_vars['aPaging']->value['iCountPage']){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sBaseUrl'];?>
/page<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['iCountPage'];?>
/<?php echo $_smarty_tpl->tpl_vars['aPaging']->value['sGetParams'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['paging_last'];?>
">>></a></li><?php }?>					
		</ul>
	</div>
<?php }?><?php }} ?>