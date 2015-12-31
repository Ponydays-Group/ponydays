<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:41
         compiled from "/var/www/bunker//templates/skin/mobile/topic_part_header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17274123175684d70d08ffb9-24454676%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '11b7d5709a28f1118a50c38a7f9ee6db99178b12' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/topic_part_header.tpl',
      1 => 1449125146,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17274123175684d70d08ffb9-24454676',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopic' => 0,
    'bTopicList' => 0,
    'aLang' => 0,
    'oBlog' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70d10c307_23397809',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70d10c307_23397809')) {function content_5684d70d10c307_23397809($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars["oBlog"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getBlog(), null, 0);?>
<?php $_smarty_tpl->tpl_vars["oUser"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getUser(), null, 0);?>
<?php $_smarty_tpl->tpl_vars["oVote"] = new Smarty_variable($_smarty_tpl->tpl_vars['oTopic']->value->getVote(), null, 0);?>


<article class="topic topic-type-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getType();?>
 js-topic <?php if (!$_smarty_tpl->tpl_vars['bTopicList']->value){?>topic-single<?php }?>">
	<header class="topic-header">
		<h1 class="topic-title word-wrap">
			<?php if ($_smarty_tpl->tpl_vars['bTopicList']->value){?>
				<a href="<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getUrl();?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oTopic']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a>
			<?php }else{ ?>
				<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oTopic']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>

			<?php }?>

			<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getPublish()==0){?>   
				<i class="icon-topic-draft" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_unpublish'];?>
"></i>
			<?php }?>
			
			<?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getType()=='link'){?> 
				<i class="icon-topic-link" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_link'];?>
"></i>
			<?php }?>
		</h1>
		
		
		<div class="topic-info">
			<i class="icon-blog <?php if ($_smarty_tpl->tpl_vars['oBlog']->value->getUserIsJoin()){?>active<?php }?>"></i><a href="<?php echo $_smarty_tpl->tpl_vars['oBlog']->value->getUrlFull();?>
" class="topic-blog"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oBlog']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a>
		</div>
	</header><?php }} ?>