<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/blocks/block.stream_comment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19671596255684d6cb585515-93069331%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ddf8bf39e402efd669170a2267959a057d28c366' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/blocks/block.stream_comment.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19671596255684d6cb585515-93069331',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aComments' => 0,
    'oComment' => 0,
    'oTopic' => 0,
    'oUser' => 0,
    'oBlog' => 0,
    'oConfig' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb647bc7_02769927',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb647bc7_02769927')) {function content_5684d6cb647bc7_02769927($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/var/www/bunker/engine/lib/external/Smarty/libs/plugins/modifier.truncate.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_date_format')) include '/var/www/bunker//engine/modules/viewer/plugs/function.date_format.php';
if (!is_callable('smarty_modifier_declension')) include '/var/www/bunker//engine/modules/viewer/plugs/modifier.declension.php';
?><ul class="item-list">
	<?php  $_smarty_tpl->tpl_vars['oComment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oComment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aComments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['oComment']->key => $_smarty_tpl->tpl_vars['oComment']->value){
$_smarty_tpl->tpl_vars['oComment']->_loop = true;
?>
		<?php $_smarty_tpl->tpl_vars["oUser"] = new Smarty_variable($_smarty_tpl->tpl_vars['oComment']->value->getUser(), null, 0);?>
		<?php $_smarty_tpl->tpl_vars["oTopic"] = new Smarty_variable($_smarty_tpl->tpl_vars['oComment']->value->getTarget(), null, 0);?>
		<?php $_smarty_tpl->tpl_vars["oBlog"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getBlog(), null, 0);?>
		
		<li class="js-title-comment" title="<?php echo htmlspecialchars(smarty_modifier_truncate(trim(preg_replace('!<[^>]*?>!', ' ', $_smarty_tpl->tpl_vars['oComment']->value->getText())),100,'...'), ENT_QUOTES, 'UTF-8', true);?>
">

			
			<a href="<?php echo $_smarty_tpl->tpl_vars['oUser']->value->getUserWebPath();?>
" class="author"><?php echo $_smarty_tpl->tpl_vars['oUser']->value->getLogin();?>
</a> в
			<a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
" class="blog-name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oBlog']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a>:<br>
			<a href="<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('module.comment.nested_per_page')){?><?php echo smarty_function_router(array('page'=>'comments'),$_smarty_tpl);?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getUrl();?>
#comment<?php }?><?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oTopic']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a>
			
			<p>
				<time datetime="<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oComment']->value->getDate(),'format'=>'c'),$_smarty_tpl);?>
"><?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oComment']->value->getDate(),'hours_back'=>"12",'minutes_back'=>"60",'now'=>"60",'day'=>"day H:i",'format'=>"j F Y, H:i"),$_smarty_tpl);?>
</time> |
				<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getCountComment();?>
 <?php echo smarty_modifier_declension($_smarty_tpl->tpl_vars['oTopic']->value->getCountComment(),$_smarty_tpl->tpl_vars['aLang']->value['comment_declension'],'russian');?>

			</p>
		</li>
	<?php } ?>
</ul>


<footer>
	<a href="<?php echo smarty_function_router(array('page'=>'comments'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_stream_comments_all'];?>
</a> | <a href="<?php echo smarty_function_router(array('page'=>'rss'),$_smarty_tpl);?>
allcomments/">RSS</a>
</footer><?php }} ?>