<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/comment_tree.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10964656705684d6cbd71aa5-31307988%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6b06c7999c70967677aacd26bdcd64e8d050691f' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/comment_tree.tpl',
      1 => 1447457150,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10964656705684d6cbd71aa5-31307988',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aPagingCmt' => 0,
    'iTargetId' => 0,
    'sTargetType' => 0,
    'iMaxIdComment' => 0,
    'iCountComment' => 0,
    'bAllowSubscribe' => 0,
    'oUserCurrent' => 0,
    'aLang' => 0,
    'oSubscribeComment' => 0,
    'aComments' => 0,
    'oComment' => 0,
    'cmtlevel' => 0,
    'oConfig' => 0,
    'nesting' => 0,
    'bAllowNewComment' => 0,
    'sNoticeNotAllow' => 0,
    'sNoticeCommentAdd' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cbec19e8_62298294',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cbec19e8_62298294')) {function content_5684d6cbec19e8_62298294($_smarty_tpl) {?><?php if (!is_callable('smarty_function_add_block')) include '/var/www/bunker//engine/modules/viewer/plugs/function.add_block.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><?php echo smarty_function_add_block(array('group'=>'toolbar','name'=>'toolbar_comment.tpl','aPagingCmt'=>$_smarty_tpl->tpl_vars['aPagingCmt']->value,'iTargetId'=>$_smarty_tpl->tpl_vars['iTargetId']->value,'sTargetType'=>$_smarty_tpl->tpl_vars['sTargetType']->value,'iMaxIdComment'=>$_smarty_tpl->tpl_vars['iMaxIdComment']->value),$_smarty_tpl);?>


<?php echo smarty_function_hook(array('run'=>'comment_tree_begin','iTargetId'=>$_smarty_tpl->tpl_vars['iTargetId']->value,'sTargetType'=>$_smarty_tpl->tpl_vars['sTargetType']->value),$_smarty_tpl);?>


<div class="comments" id="comments">
	<header class="comments-header">
		<h3>Комментариев: <span id="count-comments"><?php echo $_smarty_tpl->tpl_vars['iCountComment']->value;?>
</span></h3>
		
		<?php if ($_smarty_tpl->tpl_vars['bAllowSubscribe']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
			<label for="comment_subscribe"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_subscribe'];?>
</label>

			<input <?php if ($_smarty_tpl->tpl_vars['oSubscribeComment']->value&&$_smarty_tpl->tpl_vars['oSubscribeComment']->value->getStatus()){?>checked="checked"<?php }?> type="checkbox" id="comment_subscribe" class="input-checkbox" onchange="ls.subscribe.toggle('<?php echo $_smarty_tpl->tpl_vars['sTargetType']->value;?>
_new_comment','<?php echo $_smarty_tpl->tpl_vars['iTargetId']->value;?>
','',this.checked);">
		<?php }?>
	
		<a name="comments"></a>
	</header>


	<?php $_smarty_tpl->tpl_vars["nesting"] = new Smarty_variable("-1", null, 0);?>
	<?php  $_smarty_tpl->tpl_vars['oComment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['oComment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aComments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['oComment']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['oComment']->iteration=0;
 $_smarty_tpl->tpl_vars['oComment']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['oComment']->key => $_smarty_tpl->tpl_vars['oComment']->value){
$_smarty_tpl->tpl_vars['oComment']->_loop = true;
 $_smarty_tpl->tpl_vars['oComment']->iteration++;
 $_smarty_tpl->tpl_vars['oComment']->index++;
 $_smarty_tpl->tpl_vars['oComment']->first = $_smarty_tpl->tpl_vars['oComment']->index === 0;
 $_smarty_tpl->tpl_vars['oComment']->last = $_smarty_tpl->tpl_vars['oComment']->iteration === $_smarty_tpl->tpl_vars['oComment']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['rublist']['first'] = $_smarty_tpl->tpl_vars['oComment']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['rublist']['last'] = $_smarty_tpl->tpl_vars['oComment']->last;
?>
		<?php $_smarty_tpl->tpl_vars["cmtlevel"] = new Smarty_variable($_smarty_tpl->tpl_vars['oComment']->value->getLevel(), null, 0);?>
		
		<?php if ($_smarty_tpl->tpl_vars['cmtlevel']->value>$_smarty_tpl->tpl_vars['oConfig']->value->GetValue('module.comment.max_tree')){?>
			<?php $_smarty_tpl->tpl_vars["cmtlevel"] = new Smarty_variable($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('module.comment.max_tree'), null, 0);?>
		<?php }?>
		
		<?php if ($_smarty_tpl->tpl_vars['nesting']->value<$_smarty_tpl->tpl_vars['cmtlevel']->value){?> 
		<?php }elseif($_smarty_tpl->tpl_vars['nesting']->value>$_smarty_tpl->tpl_vars['cmtlevel']->value){?>    	
			<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['name'] = 'closelist1';
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['nesting']->value-$_smarty_tpl->tpl_vars['cmtlevel']->value+1) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist1']['total']);
?></div><?php endfor; endif; ?>
		<?php }elseif(!$_smarty_tpl->getVariable('smarty')->value['foreach']['rublist']['first']){?>
			</div>
		<?php }?>
		
		<div class="comment-wrapper" id="comment_wrapper_id_<?php echo $_smarty_tpl->tpl_vars['oComment']->value->getId();?>
">
		
		<?php echo $_smarty_tpl->getSubTemplate ('comment.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
 
		<?php $_smarty_tpl->tpl_vars["nesting"] = new Smarty_variable($_smarty_tpl->tpl_vars['cmtlevel']->value, null, 0);?>
		<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['rublist']['last']){?>
			<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['name'] = 'closelist2';
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['nesting']->value+1) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['closelist2']['total']);
?></div><?php endfor; endif; ?>    
		<?php }?>
	<?php } ?>
</div>				
	
	
<?php echo $_smarty_tpl->getSubTemplate ('comment_paging.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('aPagingCmt'=>$_smarty_tpl->tpl_vars['aPagingCmt']->value), 0);?>


<?php echo smarty_function_hook(array('run'=>'comment_tree_end','iTargetId'=>$_smarty_tpl->tpl_vars['iTargetId']->value,'sTargetType'=>$_smarty_tpl->tpl_vars['sTargetType']->value),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['bAllowNewComment']->value){?>
	<?php echo $_smarty_tpl->tpl_vars['sNoticeNotAllow']->value;?>

<?php }else{ ?>
	<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
		<?php echo $_smarty_tpl->getSubTemplate ('editor.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('sImgToLoad'=>'form_comment_text','sSettingsTinymce'=>'ls.settings.getTinymceComment()','sSettingsMarkitup'=>'ls.settings.getMarkitupComment()'), 0);?>


		<h4 class="reply-header" id="comment_id_0">
			<a href="#" class="link-dotted" onclick="ls.comments.toggleCommentForm(0); return false;"><?php echo $_smarty_tpl->tpl_vars['sNoticeCommentAdd']->value;?>
</a>
		</h4>
		
		
		<div id="reply" class="reply">		
			<form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
				<?php echo smarty_function_hook(array('run'=>'form_add_comment_begin'),$_smarty_tpl);?>

				
				<textarea name="comment_text" id="form_comment_text" class="mce-editor markitup-editor input-width-full"></textarea>
				
				<?php echo smarty_function_hook(array('run'=>'form_add_comment_end'),$_smarty_tpl);?>

				
				<button type="submit" name="submit_comment" 
						id="comment-button-submit" 
						onclick="ls.comments.add('form_comment',<?php echo $_smarty_tpl->tpl_vars['iTargetId']->value;?>
,'<?php echo $_smarty_tpl->tpl_vars['sTargetType']->value;?>
'); return false;" 
						class="button button-primary"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_add'];?>
</button>
				<button type="button" onclick="ls.comments.preview();" class="button"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_preview'];?>
</button>
				
				<input type="hidden" name="reply" value="0" id="form_comment_reply" />
				<input type="hidden" name="cmt_target_id" value="<?php echo $_smarty_tpl->tpl_vars['iTargetId']->value;?>
" />
			</form>
		</div>
	<?php }else{ ?>
		<?php echo $_smarty_tpl->tpl_vars['aLang']->value['comment_unregistered'];?>

	<?php }?>
<?php }?>	


<?php }} ?>