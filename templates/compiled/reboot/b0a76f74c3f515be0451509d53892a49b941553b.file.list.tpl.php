<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:37
         compiled from "/var/www/bunker//templates/skin/reboot/actions/ActionUserfeed/list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12791657585684d709e0f0c6-92785259%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b0a76f74c3f515be0451509d53892a49b941553b' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/actions/ActionUserfeed/list.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12791657585684d709e0f0c6-92785259',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aTopics' => 0,
    'bDisableGetMoreButton' => 0,
    'iUserfeedLastId' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d709e44f82_07275860',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d709e44f82_07275860')) {function content_5684d709e44f82_07275860($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('menu'=>'blog'), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('topic_list.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>




<?php if (count($_smarty_tpl->tpl_vars['aTopics']->value)){?>
    <?php if (!$_smarty_tpl->tpl_vars['bDisableGetMoreButton']->value){?>
        <div id="userfeed_loaded_topics"></div>
        <input type="hidden" id="userfeed_last_id" value="<?php echo $_smarty_tpl->tpl_vars['iUserfeedLastId']->value;?>
" />
        <a class="stream-get-more" id="userfeed_get_more" href="javascript:ls.userfeed.getMore()"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['userfeed_get_more'];?>
 &darr;</a>
    <?php }?>
<?php }else{ ?>
    <?php echo $_smarty_tpl->tpl_vars['aLang']->value['userfeed_no_events'];?>

<?php }?>



<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>