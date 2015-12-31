<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:36
         compiled from "/var/www/bunker//templates/skin/reboot/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11188659625684d6cc795a92-52523734%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd9dd9c5e902809b78cf94a76b721c341ee2f105' => 
    array (
      0 => '/var/www/bunker//templates/skin/reboot/footer.tpl',
      1 => 1451465742,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11188659625684d6cc795a92-52523734',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6cc7adf41_27857703',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6cc7adf41_27857703')) {function content_5684d6cc7adf41_27857703($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
?>			<?php echo smarty_function_hook(array('run'=>'content_end'),$_smarty_tpl);?>

		</div> <!-- /content -->
	</div> <!-- /wrapper -->


	<footer id="footer">
		<div style="text-align: center; font-size: 16pt;">
			<span style="display: block; clear: both;">Светлая\темная тема</span>
			<div style="height: 80px; margin: 10px auto 0px; width: 80px;">
				<img src="<?php echo smarty_function_cfg(array('name'=>"path.static.skin"),$_smarty_tpl);?>
/images/dark-to-day.png" onclick="bunkerStyle()" title="Светлый режим/темный режим" class="switch-theme" />
			</div>
                <p style="margin: 10px; font-size: 12pt;"><a href="/?force-mobile=on">Мобильная версия</a></p>
		</div>
		<?php echo smarty_function_hook(array('run'=>'body_end'),$_smarty_tpl);?>

	</footer>
</div> <!-- /container -->



			<?php echo $_smarty_tpl->getSubTemplate ('toolbar.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

			<script>
				


				function bunkerStyle() {
        var date = new Date;
        date.setDate(date.getDate() + 100);
        if(getCookie("SiteStyle") == "Dark") {
                document.cookie = "SiteStyle=Light; path=/; expires=" + date.toUTCString();
                location.reload();
        } else {
                if(getCookie("SiteStyle") == "Light") {
                        document.cookie = "SiteStyle=Dark; path=/; expires=" + date.toUTCString();
                        location.reload();
                } else {
                        document.cookie = "SiteStyle=Dark; path=/; expires=" + date.toUTCString();
                        location.reload();
                }
        }
}
var allNew = document.querySelectorAll('.spoiler-title');
console.log(allNew)
idx=0
for(idx=0;idx<allNew.length;idx++){	
	allNew[idx].className="spoiler-title spoiler-close" 
}

var despoil = function() {
	var allBody = document.querySelectorAll('.spoiler-body');
	idx=0
	for(idx=0;idx<allBody.length;idx++){	
		allBody[idx].style.display="inline" 
	}
	var allNew = document.querySelectorAll('.spoiler-title');
	idx=0
	for(idx=0;idx<allNew.length;idx++){	
		allNew[idx].className="spoiler-title spoiler-open" 
	}
	var el = document.getElementById("spoil");
    el.innerHTML = 'Закрыть спойлеры<i class="fa fa-eye-slash">';
    el.parentNode.title = 'Закрыть спойлеры'
    el.onclick=function(){spoil(); return false;};
}

var spoil = function() {
	var allBody = document.querySelectorAll('.spoiler-body');
	idx=0
	for(idx=0;idx<allBody.length;idx++){	
		allBody[idx].style.display="none" 
	}
	var allNew = document.querySelectorAll('.spoiler-title');
	idx=0
	for(idx=0;idx<allNew.length;idx++){	
		allNew[idx].className="spoiler-title spoiler-close" 
	}
	var el = document.getElementById("spoil");
    el.innerHTML = 'Открыть спойлеры<i class="fa fa-eye-slash">';
    el.parentNode.title = 'Открыть спойлеры'
    el.onclick=function(){despoil(); return false;};
}
$('.btn-menu').click(function(){panel()})

pc = 0;
var panel = function(){
	pc++;
	if (pc == 16) {
		woona()
	}
}
</script>
</body>
</html>
<?php }} ?>