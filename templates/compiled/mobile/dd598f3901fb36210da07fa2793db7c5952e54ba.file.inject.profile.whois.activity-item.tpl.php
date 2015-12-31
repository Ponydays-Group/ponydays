<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:19:27
         compiled from "/var/www/bunker/plugins/mobiletpl/templates/skin/default/inject.profile.whois.activity-item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:44981825684d6ffef9913-50372505%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd598f3901fb36210da07fa2793db7c5952e54ba' => 
    array (
      0 => '/var/www/bunker/plugins/mobiletpl/templates/skin/default/inject.profile.whois.activity-item.tpl',
      1 => 1449125120,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '44981825684d6ffef9913-50372505',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oTopicUserProfileLast' => 0,
    'aLang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6fff32266_14743392',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6fff32266_14743392')) {function content_5684d6fff32266_14743392($_smarty_tpl) {?><?php if (!is_callable('smarty_function_date_format')) include '/var/www/bunker//engine/modules/viewer/plugs/function.date_format.php';
?><?php if ($_smarty_tpl->tpl_vars['oTopicUserProfileLast']->value){?>
<tr>
    <td colspan="2" class="cell-latest-post">
		<?php echo $_smarty_tpl->tpl_vars['aLang']->value['profile_latest_post'];?>
 —
        <time datetime="<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oTopicUserProfileLast']->value->getDateAdd(),'format'=>'c'),$_smarty_tpl);?>
" title="<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oTopicUserProfileLast']->value->getDateAdd(),'format'=>'j F Y, H:i'),$_smarty_tpl);?>
">
			<?php echo smarty_function_date_format(array('date'=>$_smarty_tpl->tpl_vars['oTopicUserProfileLast']->value->getDateAdd(),'hours_back'=>"12",'minutes_back'=>"60",'now'=>"60",'day'=>"day H:i",'format'=>"j F Y, H:i"),$_smarty_tpl);?>

        </time><br />
        <a href="<?php echo $_smarty_tpl->tpl_vars['oTopicUserProfileLast']->value->getUrl();?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['oTopicUserProfileLast']->value->getTitle(), ENT_QUOTES, 'UTF-8', true);?>
</a>
    </td>
</tr>
<?php }?><?php }} ?>