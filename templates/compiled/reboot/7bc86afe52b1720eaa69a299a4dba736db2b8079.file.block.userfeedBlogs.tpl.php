<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:37
         compiled from "/var/www/bunker//templates/skin/reboot/blocks/block.userfeedBlogs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8816408555684d709ed2611-03796874%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7bc86afe52b1720eaa69a299a4dba736db2b8079' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/blocks/block.userfeedBlogs.tpl',
      1 => 1451300925,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8816408555684d709ed2611-03796874',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserCurrent' => 0,
    'aLang' => 0,
    'aUserfeedBlogs' => 0,
    'oBlog' => 0,
    'iBlogId' => 0,
    'aUserfeedSubscribedBlogs' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70a00deb9_05451695',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70a00deb9_05451695')) {function content_5684d70a00deb9_05451695($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
	<section class="block block-type-activity">
		<header class="block-header">
			<h3><?php echo $_smarty_tpl->tpl_vars['aLang']->value['userfeed_block_blogs_title'];?>
</h3>
		</header>
		
		<div class="block-content">
                        <small class="note"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['userfeed_settings_note_follow_blogs'];?>
</small>
                        <span class="subscribe"> <span onclick=<?php  $_smarty_tpl->tpl_vars['oBlog'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oBlog']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aUserfeedBlogs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oBlog']->key => $_smarty_tpl->tpl_vars['oBlog']->value){
$_smarty_tpl->tpl_vars['oBlog']->_loop = true;
?>ls.userfeed.subscribe('blogs',<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getId();?>
);<?php } ?>>Подписаться на все блоги</span> | <span onclick=<?php  $_smarty_tpl->tpl_vars['oBlog'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oBlog']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aUserfeedBlogs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oBlog']->key => $_smarty_tpl->tpl_vars['oBlog']->value){
$_smarty_tpl->tpl_vars['oBlog']->_loop = true;
?>ls.userfeed.unsubscribe('blogs',<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getId();?>
);<?php } ?>>Отписаться от всех блогов</span></span><br><br>
			<?php if (count($_smarty_tpl->tpl_vars['aUserfeedBlogs']->value)){?>
				<ul class="stream-settings-blogs">
					<?php  $_smarty_tpl->tpl_vars['oBlog'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oBlog']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aUserfeedBlogs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oBlog']->key => $_smarty_tpl->tpl_vars['oBlog']->value){
$_smarty_tpl->tpl_vars['oBlog']->_loop = true;
?>
						<?php $_smarty_tpl->tpl_vars['iBlogId'] = new Smarty_variable($_smarty_tpl->tpl_vars['oBlog']->value->getId(), null, 0);?>
						<li><input class="userfeedBlogCheckbox input-checkbox"
									type="checkbox"
									<?php if (isset($_smarty_tpl->tpl_vars['aUserfeedSubscribedBlogs']->value[$_smarty_tpl->tpl_vars['iBlogId']->value])){?> checked="checked"<?php }?>
									onClick="if (jQuery(this).prop('checked')) { ls.userfeed.subscribe('blogs',<?php echo $_smarty_tpl->tpl_vars['iBlogId']->value;?>
) } else { ls.userfeed.unsubscribe('blogs',<?php echo $_smarty_tpl->tpl_vars['iBlogId']->value;?>
) } " />
							<a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
"><?php if ($_smarty_tpl->tpl_vars['oBlog']->value->getType()=="close"){?><i class="fa fa-lock"></i>&nbsp<?php }elseif($_smarty_tpl->tpl_vars['oBlog']->value->getType()=="invite"){?><i class="fa fa-unlock"></i>&nbsp<?php }?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oBlog']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a>
						</li>
					<?php } ?>
				</ul>
			<?php }else{ ?>
				<small class="notice-empty"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['userfeed_no_blogs'];?>
</small>
			<?php }?>
		</div>
	</section>
<?php }?>
<?php }} ?>