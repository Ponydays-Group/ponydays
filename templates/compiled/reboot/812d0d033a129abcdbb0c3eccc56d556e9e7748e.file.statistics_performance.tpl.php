<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker//templates/skin/reboot/statistics_performance.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17253215605684d6cc7d62f3-66472696%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '812d0d033a129abcdbb0c3eccc56d556e9e7748e' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/statistics_performance.tpl',
      1 => 1444665004,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17253215605684d6cc7d62f3-66472696',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'bIsShowStatsPerformance' => 0,
    'oUserCurrent' => 0,
    'aStatsPerformance' => 0,
    'iTimeFullPerformance' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cc813663_81090471',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cc813663_81090471')) {function content_5684d6cc813663_81090471($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><?php if ($_smarty_tpl->tpl_vars['bIsShowStatsPerformance']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value&&$_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()){?>
	<div class="stat-performance">
		<?php echo smarty_function_hook(array('run'=>'statistics_performance_begin'),$_smarty_tpl);?>

		<table>
			<tr>
				<td>
					<h4>MySql</h4>
					query: <strong><?php echo $_smarty_tpl->tpl_vars['aStatsPerformance']->value['sql']['count'];?>
</strong><br />
					time: <strong><?php echo $_smarty_tpl->tpl_vars['aStatsPerformance']->value['sql']['time'];?>
</strong>
				</td>
				<td>
					<h4>Cache</h4>
					query: <strong><?php echo $_smarty_tpl->tpl_vars['aStatsPerformance']->value['cache']['count'];?>
</strong><br />
					&mdash; set: <strong><?php echo $_smarty_tpl->tpl_vars['aStatsPerformance']->value['cache']['count_set'];?>
</strong><br />
					&mdash; get: <strong><?php echo $_smarty_tpl->tpl_vars['aStatsPerformance']->value['cache']['count_get'];?>
</strong><br />
					time: <strong><?php echo $_smarty_tpl->tpl_vars['aStatsPerformance']->value['cache']['time'];?>
</strong>
				</td>
				<td>
					<h4>PHP</h4>	
					time load modules: <strong><?php echo $_smarty_tpl->tpl_vars['aStatsPerformance']->value['engine']['time_load_module'];?>
</strong><br />
					full time: <strong><?php echo $_smarty_tpl->tpl_vars['iTimeFullPerformance']->value;?>
</strong>
				</td>
				<?php echo smarty_function_hook(array('run'=>'statistics_performance_item'),$_smarty_tpl);?>

			</tr>
		</table>
		<?php echo smarty_function_hook(array('run'=>'statistics_performance_end'),$_smarty_tpl);?>

	</div>
<?php }?><?php }} ?>