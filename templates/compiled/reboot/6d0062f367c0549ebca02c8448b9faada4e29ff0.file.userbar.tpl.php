<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:34
         compiled from "/var/www/bunker//templates/skin/reboot/userbar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8693544905684d6caed5223-27365839%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6d0062f367c0549ebca02c8448b9faada4e29ff0' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/userbar.tpl',
      1 => 1451546305,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8693544905684d6caed5223-27365839',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oUserCurrent' => 0,
    'aLang' => 0,
    'iUserCurrentCountTalkNew' => 0,
    'LIVESTREET_SECURITY_KEY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cb047511_64957306',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cb047511_64957306')) {function content_5684d6cb047511_64957306($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><input type="checkbox" id="hmt" class="hidden-menu-ticker">
<label class="btn-menu" for="hmt">
    <span class="first"></span>
    <span class="second"></span>
    <span class="third"></span>
    <a href="/">
        <img src="<?php echo smarty_function_cfg(array('name'=>"path.static.skin"),$_smarty_tpl);?>
/logo.png" class="logo">
    </a>
</label>

<div class="nav nav-userbar hidden-menu">
<ul>
    <a href="/">
    </a>
    <?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
    <li class="nav-userbar-username" title="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getLogin();?>
">
        <a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
" class="username">
            <?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getLogin();?>

            <img src="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getProfileAvatarPath(24);?>
" alt="avatar" class="avatar" />
        </a>
    </li>
    <li title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create'];?>
">
        <a href="<?php echo smarty_function_router(array('page'=>'topic'),$_smarty_tpl);?>
add/" class="write" id="modal_write_show">
            <?php echo $_smarty_tpl->tpl_vars['aLang']->value['block_create'];?>

            <i class="fa fa-pencil-square-o"></i>
        </a>
    </li>
    <li title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_privat_messages'];?>
<?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value){?> (<?php echo $_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value;?>
)<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'talk'),$_smarty_tpl);?>
" <?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value){?>class="new-messages"<?php }?> id="new_messages">
            <?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_privat_messages'];?>
<?php if ($_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value){?> (<?php echo $_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value;?>
)<?php }?>
            <i class="fa fa-envelope<?php if (!$_smarty_tpl->tpl_vars['iUserCurrentCountTalkNew']->value){?>-o<?php }?>"></i>
        </a>
    </li>
    <?php echo smarty_function_hook(array('run'=>'athead'),$_smarty_tpl);?>

                        <?php echo smarty_function_hook(array('run'=>'atmenu'),$_smarty_tpl);?>

    <?php echo smarty_function_hook(array('run'=>'userbar_item'),$_smarty_tpl);?>

    <li title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_favourites'];?>
">
        <a href="<?php echo $_smarty_tpl->tpl_vars['oUserCurrent']->value->getUserWebPath();?>
favourites/topics/">
            <?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_menu_profile_favourites'];?>

            <i class="fa fa-star-half-o"></i>
        </a>
    </li>
    <li title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_settings'];?>
">
        <a href="<?php echo smarty_function_router(array('page'=>'settings'),$_smarty_tpl);?>
profile/">
            <?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_settings'];?>

            <i class="fa fa-cogs"></i>
        </a>
    </li>
    <li title="Широкий режим">
        <a href="#" onclick="wide(); return false;" class="widemode" id="wide">
            Широкий режим
        </a>
    </li>
    <li title="Открыть спойлеры">
        <a href="#" onclick="despoil(); return false;" class="spoil" id="spoil">
            Открыть спойлеры
            <i class="fa fa-eye-slash"></i>
        </a>
    </li>
    </ul>
    <ul class="userbar_info">
    <li title="<?php echo $_smarty_tpl->tpl_vars['aLang']->value['exit'];?>
">
        <a href="<?php echo smarty_function_router(array('page'=>'login'),$_smarty_tpl);?>
exit/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
">
            <?php echo $_smarty_tpl->tpl_vars['aLang']->value['exit'];?>

            <i class="fa fa-times-circle-o"></i>
        </a>
    </li>
    </ul>
    <?php }else{ ?>
    <?php echo smarty_function_hook(array('run'=>'userbar_item'),$_smarty_tpl);?>

    <li>
        <a href="<?php echo smarty_function_router(array('page'=>'login'),$_smarty_tpl);?>
" class="js-login-form-show">
            <?php echo $_smarty_tpl->tpl_vars['aLang']->value['user_login_submit'];?>

            <i class="fa fa-user"></i>
        </a>
    </li>
    <li>
        <a href="<?php echo smarty_function_router(array('page'=>'registration'),$_smarty_tpl);?>
" class="js-registration-form-show">
            <?php echo $_smarty_tpl->tpl_vars['aLang']->value['registration_submit'];?>

            <i class="fa fa-user-plus"></i>
        </a>
    </li>
    <li title="Широкий режим">
        <a href="#" onclick="wide(); return false;" class="widemode" id="wide">
            Широкий режим
        </a>
    </li>
    <li title="Открыть спойлеры">
        <a href="#" onclick="despoil(); return false;" class="spoil" id="spoil">
            Открыть спойлеры
            <i class="fa fa-eye-slash"></i>
        </a>
    </li>
    </ul>
    <?php }?>
    </ul>
    <ul class="userbar_info">
    <li title="О версии <?php echo Config::Get('site_version');?>
">
        <a href="/page/about">
            О версии <?php echo Config::Get('site_version');?>

            <i class="fa fa-info-circle"></i>
        </a>
    </li>
    <li title="Сообщить об ошибке">
        <a href="http://bug.lunavod.ru/">
            Сообщить об ошибке
            <i class="fa fa-exclamation-circle"></i>
        </a>
    </li>
<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
    <li>
        <a href="<?php echo smarty_function_router(array('page'=>'login'),$_smarty_tpl);?>
exit/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
">
            <?php echo $_smarty_tpl->tpl_vars['aLang']->value['exit'];?>

            <i class="fa fa-times-circle-o"></i>
        </a>
    </li>
<?php }?>
    </ul>
</div>
</nav>

<script>
    function wide(){
        var gifs = document.getElementById('sidebar');
        var element = document.getElementById('content');
        element.style.right = "30px";
        element.style.margin = "0px";
        gifs.style.display = "none";
        var el = document.getElementById("wide");
        el.className= "standartmode";
        el.innerHTML = 'Стандартный режим'
        el.parentNode.title = 'Стандартный режим'
        el.onclick=function(){dewide(); return false;}
    }
    function dewide(){
        var gifs = document.getElementById('sidebar');
        var element = document.getElementById('content');
        element.style.right = "";
        element.style.margin = "";
        gifs.style.display = "";
        var el = document.getElementById("wide");
        el.className = "widemode";
        el.innerHTML = 'Широкий режим'
        el.parentNode.title = 'Широкий режим'
        el.onclick=function(){wide(); return false;}
    }
</script>

<?php }} ?>