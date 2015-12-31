<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker/plugins/talkbell/templates/skin/default/window_message.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7611595375684d6ccda7748-40621739%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3a82565eeb8012218965bb3d38e975ec56f530c1' => 
    array (
      0 => '/var/www/bunker/plugins/talkbell/templates/skin/default/window_message.tpl',
      1 => 1449306968,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7611595375684d6ccda7748-40621739',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'sTWPTalkbell' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6ccdae3c7_13456169',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6ccdae3c7_13456169')) {function content_5684d6ccdae3c7_13456169($_smarty_tpl) {?><script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['sTWPTalkbell']->value;?>
/js/soundmanager2.js"></script>

<script language="JavaScript" type="text/javascript">

function doMany() {
    ls.ajax(aRouter['talkbell'], {security_ls_key:LIVESTREET_SECURITY_KEY}, function (result) {
        if (result.bStateError) {
            ls.msg.error(null, result.sMsg);
        } else {
            if (!result.bStError) {
                if (result.bSc) {
                    var aSc = result.aHtmlSc;
                    for (var i = 0; i < aSc.length; i++) {
                        console.log(aSc[i]);
                        console.log(jQuery(aSc[i]).text());
                        ls.msg.notify('У вас новое сообщение', "К письму " + jQuery(aSc[i]).text());
                    }
                }
                if (result.bSt) {
                    var aSt = result.aHtmlSt;
                    for (var i = 0; i < aSt.length; i++) {
                        ls.msg.notice('', aSt[i]);
                    }
                }
            }
        }
    });
    return false;
}

setInterval(doMany, 20000)


</script>

<?php }} ?>