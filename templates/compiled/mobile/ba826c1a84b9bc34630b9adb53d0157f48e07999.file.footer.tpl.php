<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:00
         compiled from "/var/www/bunker//templates/skin/mobile/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11949366335684d6e4112258-43577975%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ba826c1a84b9bc34630b9adb53d0157f48e07999' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/footer.tpl',
      1 => 1451397255,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11949366335684d6e4112258-43577975',
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
  'unifunc' => 'content_5684d6e4153a72_98738086',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e4153a72_98738086')) {function content_5684d6e4153a72_98738086($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
?>			<?php echo smarty_function_hook(array('run'=>'content_end'),$_smarty_tpl);?>

		</div> <!-- /content -->

		
		<footer id="footer">
			<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
				<ul class="footer-profile-links clearfix">
					<li><a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
"><i class="icon-profile-profile"></i></a></li>
					<li><a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
"><i class="icon-profile-messages"></i></a></li>
					<li><a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
created/topics/"><i class="icon-profile-submited"></i></a></li>
					<li><a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
favourites/topics/"><i class="icon-profile-favourites"></i></a></li>
					<li><a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
friends/"><i class="icon-profile-friends"></i></a></li>
					<li><a href="<?php echo smarty_function_router(array('page'=>'stream'),$_smarty_tpl);?>
"><i class="icon-profile-activity"></i></a></li>
					<li><a href="<?php echo smarty_function_router(array('page'=>'settings'),$_smarty_tpl);?>
"><i class="icon-profile-settings"></i></a></li>
				</ul>
			<?php }?>
			
			<div class="footer-inner">
				<ul class="footer-links clearfix">
					<li><a href="<?php echo smarty_function_cfg(array('name'=>'path.root.web'),$_smarty_tpl);?>
/?force-mobile=off"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['desktop_version'];?>
</a></li>
					<li><a href="<?php echo smarty_function_router(array('page'=>'rss'),$_smarty_tpl);?>
">RSS</a></li>
				</ul>
				
				<div class="copyright">
					&copy; <?php echo smarty_function_cfg(array('name'=>'view.name'),$_smarty_tpl);?>

					
					<br />
					<br />
				
					<?php echo smarty_function_hook(array('run'=>'copyright'),$_smarty_tpl);?>
<br />
					Дизайн от <a href="http://designmobile.ru">DesignMobile</a>
				</div>
			</div>
			
			<?php echo smarty_function_hook(array('run'=>'footer_end'),$_smarty_tpl);?>

		</footer>
	</div> <!-- /wrapper -->
</div> <!-- /container -->

<?php echo smarty_function_hook(array('run'=>'body_end'),$_smarty_tpl);?>


</body>
</html>
<?php }} ?>