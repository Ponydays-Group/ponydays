<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker/plugins/aceadminpanel/templates/skin/admin_new/inc.menu.main.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14962521155684d6ccc139d3-42047167%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1b4f40dff7d49feff505445210b67138b493125c' => 
    array (
      0 => '/var/www/bunker/plugins/aceadminpanel/templates/skin/admin_new/inc.menu.main.tpl',
      1 => 1451140553,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14962521155684d6ccc139d3-42047167',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'oLang' => 0,
    'sEvent' => 0,
    'sMenuSubItemSelect' => 0,
    'oUserProfile' => 0,
    'aPluginActive' => 0,
    'oConfig' => 0,
    'aLang' => 0,
    'LIVESTREET_SECURITY_KEY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6ccd5b518_12381183',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6ccd5b518_12381183')) {function content_5684d6ccd5b518_12381183($_smarty_tpl) {?><?php if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
?><ul class="nav nav-list nav-menu well well-small">
    <li class="nav-header"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_panel;?>
</li>
    <li class="nav-menu_info <?php if ($_smarty_tpl->tpl_vars['sEvent']->value==''||$_smarty_tpl->tpl_vars['sEvent']->value=='info'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
info/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_info;?>
</a>
    </li>
    <li class="nav-menu_params <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='params'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
params/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->adm_menu_params;?>
</a>
    </li>

    <li class="nav-header"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_config;?>
</li>
    <li class="nav-menu_settings <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='settings'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
site/settings/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_settings;?>
</a>
    </li>
    <li class="nav-menu_reset <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='reset'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
site/reset/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_reset;?>
</a>
    </li>
    <li class="nav-menu_plugins <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='plugins'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
plugins/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_plugins;?>
</a>
    </li>

    <li class="nav-header"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_site;?>
</li>
    <li class="nav-menu_users <?php if ($_smarty_tpl->tpl_vars['sEvent']->value=='users'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
users/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_users;?>

        <?php if ($_smarty_tpl->tpl_vars['oUserProfile']->value){?><i class="icon icon-arrow-right"></i><?php }?>
        </a>
    </li>
    <li class="nav-menu_banlist <?php if ($_smarty_tpl->tpl_vars['sEvent']->value=='banlist'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
banlist/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_banlist;?>
</a>
    </li>
    <li class="nav-menu_invites <?php if ($_smarty_tpl->tpl_vars['sEvent']->value=='invites'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
invites/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_invites;?>
</a>
    </li>
    <li class="nav-menu_blogs <?php if ($_smarty_tpl->tpl_vars['sEvent']->value=='blogs'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
blogs/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_blogs;?>
</a>
    </li>
    <?php if ($_smarty_tpl->tpl_vars['aPluginActive']->value['aceblogextender']&&$_smarty_tpl->tpl_vars['oConfig']->value->GetValue('plugin.aceblogextender.category.enable')){?>
    <li class="nav-menu_categories <?php if ($_smarty_tpl->tpl_vars['sMenuSubItemSelect']->value=='plugins_admin_aceblogextender'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
plugins/aceblogextender/categories/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_categories;?>
</a>
    </li>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['aPluginActive']->value['page']&&$_smarty_tpl->tpl_vars['oConfig']->value->GetValue('plugin.page')){?>
    <li class="nav-menu_pages <?php if ($_smarty_tpl->tpl_vars['sEvent']->value=='pages'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
pages/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_pages;?>
</a>
    </li>
    <?php }?>

    <?php echo smarty_function_hook(array('run'=>'admin_menu_item'),$_smarty_tpl);?>


    <li class="nav-header"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_additional;?>
</li>
    <li class="nav-menu_db <?php if ($_smarty_tpl->tpl_vars['sEvent']->value=='db'){?>active<?php }?>">
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
db/"><?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_db;?>
</a>
    </li>
    <li id="admin_action_submenu" class="nav-menu_others" >
        <a href="<?php echo smarty_function_router(array('page'=>'admin'),$_smarty_tpl);?>
others/">
        <?php echo $_smarty_tpl->tpl_vars['oLang']->value->_adm_menu_additional_item;?>

            <i class="icon-chevron-right icon-gray"></i>
        </a>
    </li>
</ul>

<div id="admin_action_item" style="display: none;">
    <ul class="nav nav-list">
        <li><a href="<?php echo smarty_function_router(array('page'=>"admin"),$_smarty_tpl);?>
userfields/"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['admin_list_userfields'];?>
</a></li>
        <li><a href="<?php echo smarty_function_router(array('page'=>"admin"),$_smarty_tpl);?>
restorecomment/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['admin_list_restorecomment'];?>
</a></li>
        <li><a href="<?php echo smarty_function_router(array('page'=>"admin"),$_smarty_tpl);?>
recalcfavourite/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['admin_list_recalcfavourite'];?>
</a></li>
        <li><a href="<?php echo smarty_function_router(array('page'=>"admin"),$_smarty_tpl);?>
recalcvote/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['admin_list_recalcvote'];?>
</a></li>
        <li><a href="<?php echo smarty_function_router(array('page'=>"admin"),$_smarty_tpl);?>
recalctopic/?security_ls_key=<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['aLang']->value['admin_list_recalctopic'];?>
</a></li>
        <?php echo smarty_function_hook(array('run'=>'admin_action_item'),$_smarty_tpl);?>

    </ul>
    <br/>
    <div class="nav nav-list">
        <?php echo smarty_function_hook(array('run'=>'admin_action'),$_smarty_tpl);?>

    </div>
</div>

<script type="">
    var $ace = $ace || { };

    $ace.submenuAction = function(el) {
        var target = $(el);
        var popover = target.getPopover();
        if (!popover.isVisible()) {
            target.popover('show');
            popover.bind('mouseenter', function(){
                popover.data('is-mouse-over', 1);
            });
            popover.bind('mouseleave', function(){
                popover.data('is-mouse-over', null);
                setTimeout(function () {
                            if (!target.data('is-mouse-over'))
                                target.popover('hide');
                        },
                        500
                );
            });
        }
        target.data('is-mouse-over', 1);
    }

    $(function () {
        var c = $('#admin_action_item').find('li').first().children().first();
        if (c && c.length && c[0].nodeName == 'BR') {
            $(c).detach();
        }
        var options = {
            title:false,
            content:function () {
                return $('#admin_action_item').html();
            },
            html:true,
            trigger:'manual',
            css:{
                width:'auto'
            },
            events:{
                'click':function () {
                    $ace.submenuAction(this);
                },
                'mouseover':function () {
                    $ace.submenuAction(this);
                },
                'mouseout':function () {
                    //return;
                    var target = $(this);
                    setTimeout(function () {
                                if (target.getPopover().isVisible() && !target.getPopover().data('is-mouse-over')) {
                                    target.popover('hide');
                                }
                            },
                            500
                    );
                    target.data('is-mouse-over', null);
                }
            }
        };
        var submenu = $('#admin_action_submenu');
        var popover = submenu.setPopover(options);
        popover.mouseover(function(){
            $(this).data('is-mouse-over', true);
        });
        popover.mouseout(function(){ alert(1);
            $(this).data('is-mouse-over', null);
        });
        $('body').click(function () {
            submenu.popover('hide');
            if (submenu.getPopover().isVisible()) {
                submenu.popover('hide');
            }
        });

        /*
        $('#admin_action_item a').each(function(){
            var href = $(this).prop('href');
            $(this).prop('href', href.replace(aRouter['admin'], aRouter['admin'] + 'x/'));
        });
        */
    });
</script>
<?php }} ?>