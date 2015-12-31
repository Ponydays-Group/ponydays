<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/smiles.php" */ ?>
<?php /*%%SmartyHeaderCode:18652720575684d6cbb621c1-98563511%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '996eab448473dc8ecb278520f0495ed6b0f4177a' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/smiles.php',
      1 => 1450428059,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18652720575684d6cbb621c1-98563511',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cbb66fb7_80301987',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cbb66fb7_80301987')) {function content_5684d6cbb66fb7_80301987($_smarty_tpl) {?><?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; echo SmartyPhpTag(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

$smiles = scandir('/var/www/static/smiles/');
unset($smiles[array_search('.', $smiles)]);
unset($smiles[array_search('..', $smiles)]);
echo($smiles[array_rand($smiles, 1)]);
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo SmartyPhpTag(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>