<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker/plugins/aceadminpanel/templates/skin/default/hook.statistics_performance_item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7093550205684d6cc81a146-84593395%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '69ff340ff67a1693aad56d5c7a9041be9a929fa7' => 
    array (
      0 => '/var/www/bunker/plugins/aceadminpanel/templates/skin/default/hook.statistics_performance_item.tpl',
      1 => 1444665230,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7093550205684d6cc81a146-84593395',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'aMemoryStats' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cc82dcb0_79194286',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cc82dcb0_79194286')) {function content_5684d6cc82dcb0_79194286($_smarty_tpl) {?><td>
    <h4>Memory</h4>
    memory limit: <strong><?php echo $_smarty_tpl->tpl_vars['aMemoryStats']->value['memory_limit'];?>
</strong><br/>
    memory usage: <strong><?php echo $_smarty_tpl->tpl_vars['aMemoryStats']->value['usage'];?>
</strong><br/>
    peak usage: <strong><?php echo $_smarty_tpl->tpl_vars['aMemoryStats']->value['peak_usage'];?>
</strong><br/>
</td>
<?php }} ?>