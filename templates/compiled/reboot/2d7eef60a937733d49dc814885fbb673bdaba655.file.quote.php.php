<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:35
         compiled from "/var/www/bunker//templates/skin/reboot/quote.php" */ ?>
<?php /*%%SmartyHeaderCode:16577244775684d6cb154dc4-17311852%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2d7eef60a937733d49dc814885fbb673bdaba655' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/quote.php',
      1 => 1449848341,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16577244775684d6cb154dc4-17311852',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb1618b0_51047334',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb1618b0_51047334')) {function content_5684d6cb1618b0_51047334($_smarty_tpl) {?><?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; echo SmartyPhpTag(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

$quote = array(

1  => "А ты отписался в МБД?",
2  => "Луна флудить зовет!",
3  => "Тепло и лампы с флудом.",
4  => 'Диктатура МБД.',
5  => 'Как сказал товарищ Луна.',
6  => 'Дойдем до 5000!',
7  => 'Бункер — место, где пасутся драмы.'

);

srand ((double) microtime() * 1000000);
$randnum = rand(1,7);

echo"$quote[$randnum]";
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo SmartyPhpTag(array(), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }} ?>