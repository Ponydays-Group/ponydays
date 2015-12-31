<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:19
         compiled from "/var/www/bunker//templates/skin/mobile/blog_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18304362075684d6f797fd86-60410784%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '75e5310536ab54dad7777dec43834ecc8b27c370' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/blog_list.tpl',
      1 => 1449125144,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18304362075684d6f797fd86-60410784',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aBlogs' => 0,
    'oBlog' => 0,
    'aLang' => 0,
    'oUserCurrent' => 0,
    'sBlogsEmptyList' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6f7a14609_79709374',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6f7a14609_79709374')) {function content_5684d6f7a14609_79709374($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['aBlogs']->value){?>
	<ul class="blog-list">
		<?php  $_smarty_tpl->tpl_vars['oBlog'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oBlog']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aBlogs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oBlog']->key => $_smarty_tpl->tpl_vars['oBlog']->value){
$_smarty_tpl->tpl_vars['oBlog']->_loop = true;
?>
			<?php $_smarty_tpl->tpl_vars["oUserOwner"] = new Smarty_variable($_smarty_tpl->tpl_vars['oBlog']->value->getOwner(), null, 0);?>

			<li>
				<a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
">
					<img src="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getAvatarPath(48);?>
" width="48" height="48" alt="avatar" class="avatar" />
				</a>
				
				<h3><a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oBlog']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a></h3>
				
				<p>
					<?php echo $_smarty_tpl->tpl_vars['aLang']->value['blogs_rating'];?>
: <?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getRating();?>
,
					<?php echo $_smarty_tpl->tpl_vars['aLang']->value['blogs_readers'];?>
: <?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getCountUser();?>

				</p>
				
				
				<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
					<?php if ($_smarty_tpl->tpl_vars['oBlog']->value->getType()=='close'){?>
						<i title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['blog_closed'];?>
" class="icon-blog-private"></i>
					<?php }else{ ?>
						<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value->getId()!=$_smarty_tpl->tpl_vars['oBlog']->value->getOwnerId()&&$_smarty_tpl->tpl_vars['oBlog']->value->getType()=='open'){?>
							<i class="icon-blog-join <?php if ($_smarty_tpl->tpl_vars['oBlog']->value->getUserIsJoin()){?>active<?php }?>" onclick="ls.blog.toggleJoin(this, <?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getId();?>
); return false;"></i>
						<?php }else{ ?>
							<i class="icon-blog-owner"></i>
						<?php }?>
					<?php }?>
				<?php }?>
			</li>
		<?php } ?>
	</ul>
<?php }else{ ?>
	<?php if ($_smarty_tpl->tpl_vars['sBlogsEmptyList']->value){?>
		<div class="notice-empty">
			<?php echo $_smarty_tpl->tpl_vars['sBlogsEmptyList']->value;?>

		</div>
	<?php }?>
<?php }?><?php }} ?>