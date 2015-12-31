<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:41
         compiled from "/var/www/bunker/plugins/page/templates/skin/default/actions/ActionPage/admin.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14775207195684d70d898307-90638581%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '663f145fb4d0d56942a09d2ad9a4cba5b1273674' => 
    array (
      0 => '/var/www/bunker/plugins/page/templates/skin/default/actions/ActionPage/admin.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14775207195684d70d898307-90638581',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aTemplateWebPathPlugin' => 0,
    'aLang' => 0,
    'aParams' => 0,
    'aTemplatePathPlugin' => 0,
    'oPageEdit' => 0,
    'aPages' => 0,
    'oPage' => 0,
    'LIVESTREET_SECURITY_KEY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70da40379_43824557',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70da40379_43824557')) {function content_5684d70da40379_43824557($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
?><?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<link rel="stylesheet" type="text/css" href="<?php echo ($_smarty_tpl->tpl_vars['aTemplateWebPathPlugin']->value['page']).('css/style.css');?>
" media="all" />


<div>
	<h2 class="page-header"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin'];?>
</h2>
	
	
	<?php if ($_smarty_tpl->tpl_vars['aParams']->value[0]=='new'){?>
		<h3 class="page-sub-header"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['create'];?>
</h3>
		<?php echo $_smarty_tpl->getSubTemplate (($_smarty_tpl->tpl_vars['aTemplatePathPlugin']->value['page']).('actions/ActionPage/add.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php }elseif($_smarty_tpl->tpl_vars['aParams']->value[0]=='edit'){?>
		<h3 class="page-sub-header"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['edit'];?>
 «<?php echo $_smarty_tpl->tpl_vars['oPageEdit']->value->getTitle();?>
»</h3>
		<?php echo $_smarty_tpl->getSubTemplate (($_smarty_tpl->tpl_vars['aTemplatePathPlugin']->value['page']).('actions/ActionPage/add.tpl'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php }else{ ?>
		<a href="<?php echo smarty_function_router(array('page'=>'page'),$_smarty_tpl);?>
admin/new/" class="page-new"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['new'];?>
</a><br /><br />
	<?php }?>


	<table cellspacing="0" class="table">
		<thead>
			<tr>
				<th width="180px"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_title'];?>
</th>
				<th align="center" ><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_url'];?>
</th>    	
				<th align="center" width="50px"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_active'];?>
</th>    	   	
				<th align="center" width="70px"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_main'];?>
</th>    	   	
				<th align="center" width="80px"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_action'];?>
</th>
			</tr>
		</thead>
		
		<tbody>
			<?php  $_smarty_tpl->tpl_vars['oPage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oPage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aPages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oPage']->key => $_smarty_tpl->tpl_vars['oPage']->value){
$_smarty_tpl->tpl_vars['oPage']->_loop = true;
?> 	
				<tr>
					<td>
						<img src="<?php echo ($_smarty_tpl->tpl_vars['aTemplateWebPathPlugin']->value['page']).('images/');?>
<?php if ($_smarty_tpl->tpl_vars['oPage']->value->getLevel()==0){?>folder<?php }else{ ?>document<?php }?>.gif" alt="" title="" border="0" style="margin-left: <?php echo $_smarty_tpl->tpl_vars['oPage']->value->getLevel()*20;?>
px;"/>
						<a href="<?php echo smarty_function_router(array('page'=>'page'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getUrlFull();?>
/"><?php echo $_smarty_tpl->tpl_vars['oPage']->value->getTitle();?>
</a>
					</td>
					<td>
						/<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getUrlFull();?>
/
					</td>   
					<td align="center">
						<?php if ($_smarty_tpl->tpl_vars['oPage']->value->getActive()){?>
							<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_active_yes'];?>

						<?php }else{ ?>
							<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_active_no'];?>

						<?php }?>
					</td>
					<td align="center">
						<?php if ($_smarty_tpl->tpl_vars['oPage']->value->getMain()){?>
							<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_active_yes'];?>

						<?php }else{ ?>
							<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_active_no'];?>

						<?php }?>
					</td>
					<td align="center">  
						<a href="<?php echo smarty_function_router(array('page'=>'page'),$_smarty_tpl);?>
admin/edit/<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getId();?>
/"><img src="<?php echo ($_smarty_tpl->tpl_vars['aTemplateWebPathPlugin']->value['page']).('images/edit.png');?>
" alt="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_action_edit'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_action_edit'];?>
" /></a>
						<a href="<?php echo smarty_function_router(array('page'=>'page'),$_smarty_tpl);?>
admin/delete/<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getId();?>
/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
" onclick="return confirm('«<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getTitle();?>
»: <?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_action_delete_confirm'];?>
');"><img src="<?php echo ($_smarty_tpl->tpl_vars['aTemplateWebPathPlugin']->value['page']).('images/delete.png');?>
" alt="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_action_delete'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_action_delete'];?>
" /></a>
						<a href="<?php echo smarty_function_router(array('page'=>'page'),$_smarty_tpl);?>
admin/sort/<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getId();?>
/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
"><img src="<?php echo ($_smarty_tpl->tpl_vars['aTemplateWebPathPlugin']->value['page']).('images/up.png');?>
" alt="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_sort_up'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_sort_up'];?>
 (<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getSort();?>
)" /></a>
						<a href="<?php echo smarty_function_router(array('page'=>'page'),$_smarty_tpl);?>
admin/sort/<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getId();?>
/down/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
"><img src="<?php echo ($_smarty_tpl->tpl_vars['aTemplateWebPathPlugin']->value['page']).('images/down.png');?>
" alt="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_sort_down'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['plugin']['page']['admin_sort_down'];?>
 (<?php echo $_smarty_tpl->tpl_vars['oPage']->value->getSort();?>
)" /></a>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>


<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>