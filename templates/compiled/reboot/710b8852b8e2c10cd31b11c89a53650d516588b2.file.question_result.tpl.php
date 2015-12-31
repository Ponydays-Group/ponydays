<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:38
         compiled from "/var/www/bunker//templates/skin/reboot/question_result.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1334013635684d70a2abd76-17584070%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '710b8852b8e2c10cd31b11c89a53650d516588b2' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/question_result.tpl',
      1 => 1450961894,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1334013635684d70a2abd76-17584070',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopic' => 0,
    'aAnswer' => 0,
    'key' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d70a333358_66961883',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d70a333358_66961883')) {function content_5684d70a333358_66961883($_smarty_tpl) {?><ul class="poll-result" id="poll-result-original-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
">
	<?php  $_smarty_tpl->tpl_vars['aAnswer'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aAnswer']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['oTopic']->value->getQuestionAnswers(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aAnswer']->key => $_smarty_tpl->tpl_vars['aAnswer']->value){
$_smarty_tpl->tpl_vars['aAnswer']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['aAnswer']->key;
?>
		<li <?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getQuestionAnswerMax()==$_smarty_tpl->tpl_vars['aAnswer']->value['count']){?>class="most"<?php }?>>
			<dl>
				<dt>
					<strong><?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getQuestionAnswerPercent($_smarty_tpl->tpl_vars['key']->value);?>
%</strong><br />
					<span>(<?php echo $_smarty_tpl->tpl_vars['aAnswer']->value['count'];?>
)</span>
				</dt>
				<dd><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['aAnswer']->value['text'], ENT_QUOTES, 'UTF-8', true);?>
<div style="width: <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getQuestionAnswerPercent($_smarty_tpl->tpl_vars['key']->value);?>
%;" ></div></dd>
			</dl>
		</li>
	<?php } ?>
</ul>


<ul class="poll-result" id="poll-result-sort-<?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
" style="display: none;">
	<?php  $_smarty_tpl->tpl_vars['aAnswer'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aAnswer']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['oTopic']->value->getQuestionAnswers(true); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aAnswer']->key => $_smarty_tpl->tpl_vars['aAnswer']->value){
$_smarty_tpl->tpl_vars['aAnswer']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['aAnswer']->key;
?>
		<li <?php if ($_smarty_tpl->tpl_vars['oTopic']->value->getQuestionAnswerMax()==$_smarty_tpl->tpl_vars['aAnswer']->value['count']){?>class="most"<?php }?>>
			<dl>
				<dt>
					<strong><?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getQuestionAnswerPercent($_smarty_tpl->tpl_vars['key']->value);?>
%</strong><br />
					<span>(<?php echo $_smarty_tpl->tpl_vars['aAnswer']->value['count'];?>
)</span>
				</dt>
				<dd><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['aAnswer']->value['text'], ENT_QUOTES, 'UTF-8', true);?>
<div style="width: <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getQuestionAnswerPercent($_smarty_tpl->tpl_vars['key']->value);?>
%;" ></div></dd>
			</dl>
		</li>
	<?php } ?>
</ul>


<button type="submit" class="button button-icon" title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_question_vote_result_sort'];?>
" onclick="return ls.poll.switchResult(this, <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getId();?>
);"><i class="fa fa-align-left"></i></button>

<span class="poll-total poll-total-result"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_question_vote_result'];?>
: <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getQuestionCountVote();?>
 | <?php echo $_smarty_tpl->tpl_vars['aLang']->value['topic_question_abstain_result'];?>
: <?php echo $_smarty_tpl->tpl_vars['oTopic']->value->getQuestionCountVoteAbstain();?>
</span><?php }} ?>