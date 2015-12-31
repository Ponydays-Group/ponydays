<?php /* Smarty version Smarty-3.1.8, created on 2015-12-31 10:18:59
         compiled from "/var/www/bunker//templates/skin/mobile/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9932075075684d6e3abbc19-67905203%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bea24d5635ec86a080f6d615735629ecd213d023' => 
    array (
      0 => '/var/www/bunker//templates/skin/mobile/header.tpl',
      1 => 1451469592,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9932075075684d6e3abbc19-67905203',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'sHtmlTitle' => 0,
    'sHtmlDescription' => 0,
    'sHtmlKeywords' => 0,
    'aHtmlHeadFiles' => 0,
    'aHtmlRssAlternate' => 0,
    'sHtmlCanonical' => 0,
    'bRefreshToHome' => 0,
    'LIVESTREET_SECURITY_KEY' => 0,
    '_sPhpSessionId' => 0,
    'oConfig' => 0,
    'aRouter' => 0,
    'sPage' => 0,
    'sPath' => 0,
    'aLangJs' => 0,
    'oUserCurrent' => 0,
    'body_classes' => 0,
    'noBg' => 0,
    'aPaging' => 0,
    'sidebarPosition' => 0,
    'sMenuItemSelect' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_5684d6e3bfbb96_14975855',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5684d6e3bfbb96_14975855')) {function content_5684d6e3bfbb96_14975855($_smarty_tpl) {?><?php if (!is_callable('smarty_function_hook')) include '/var/www/bunker//engine/modules/viewer/plugs/function.hook.php';
if (!is_callable('smarty_function_cfg')) include '/var/www/bunker//engine/modules/viewer/plugs/function.cfg.php';
if (!is_callable('smarty_function_router')) include '/var/www/bunker//engine/modules/viewer/plugs/function.router.php';
if (!is_callable('smarty_function_json')) include '/var/www/bunker//engine/modules/viewer/plugs/function.json.php';
if (!is_callable('smarty_function_lang_load')) include '/var/www/bunker//engine/modules/viewer/plugs/function.lang_load.php';
?><!doctype html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="ru"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="ru"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->

<head>
	<?php echo smarty_function_hook(array('run'=>'html_head_begin'),$_smarty_tpl);?>

	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title><?php echo $_smarty_tpl->tpl_vars['sHtmlTitle']->value;?>
</title>
	
	<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['sHtmlDescription']->value;?>
">
	<meta name="keywords" content="<?php echo $_smarty_tpl->tpl_vars['sHtmlKeywords']->value;?>
">


	<!-- Mobile viewport optimization h5bp.com/ad -->
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes" name="viewport">

	<!-- Mobile IE allows us to activate ClearType technology for smoothing fonts for easy reading -->
	<meta http-equiv="cleartype" content="on">

<script async src="<?php echo smarty_function_cfg(array('name'=>"path.static.skin"),$_smarty_tpl);?>
/js/spoiler.js"></script>
	<?php echo $_smarty_tpl->tpl_vars['aHtmlHeadFiles']->value['css'];?>

	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

	<link href="<?php echo smarty_function_cfg(array('name'=>'path.static.skin'),$_smarty_tpl);?>
/images/favicon.ico?v1" rel="shortcut icon" />
	<link rel="search" type="application/opensearchdescription+xml" href="<?php echo smarty_function_router(array('page'=>'search'),$_smarty_tpl);?>
opensearch/" title="<?php echo smarty_function_cfg(array('name'=>'view.name'),$_smarty_tpl);?>
" />

	<?php if ($_smarty_tpl->tpl_vars['aHtmlRssAlternate']->value){?>
		<link rel="alternate" type="application/rss+xml" href="<?php echo $_smarty_tpl->tpl_vars['aHtmlRssAlternate']->value['url'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['aHtmlRssAlternate']->value['title'];?>
">
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['sHtmlCanonical']->value){?>
		<link rel="canonical" href="<?php echo $_smarty_tpl->tpl_vars['sHtmlCanonical']->value;?>
" />
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['bRefreshToHome']->value){?>
		<meta  HTTP-EQUIV="Refresh" CONTENT="3; URL=<?php echo smarty_function_cfg(array('name'=>'path.root.web'),$_smarty_tpl);?>
/">
	<?php }?>
	
	
	<script type="text/javascript">
		var DIR_WEB_ROOT 			= '<?php echo smarty_function_cfg(array('name'=>"path.root.web"),$_smarty_tpl);?>
';
		var DIR_STATIC_SKIN 		= '<?php echo smarty_function_cfg(array('name'=>"path.static.skin"),$_smarty_tpl);?>
';
		var DIR_ROOT_ENGINE_LIB 	= '<?php echo smarty_function_cfg(array('name'=>"path.root.engine_lib"),$_smarty_tpl);?>
';
		var LIVESTREET_SECURITY_KEY = '<?php echo $_smarty_tpl->tpl_vars['LIVESTREET_SECURITY_KEY']->value;?>
';
		var SESSION_ID				= '<?php echo $_smarty_tpl->tpl_vars['_sPhpSessionId']->value;?>
';
		var BLOG_USE_TINYMCE		= '<?php echo smarty_function_cfg(array('name'=>"view.tinymce"),$_smarty_tpl);?>
';
		
		var TINYMCE_LANG = 'en';
		<?php if ($_smarty_tpl->tpl_vars['oConfig']->value->GetValue('lang.current')=='russian'){?>
			TINYMCE_LANG = 'ru';
		<?php }?>

		var aRouter = new Array();
		<?php  $_smarty_tpl->tpl_vars['sPath'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sPath']->_loop = false;
 $_smarty_tpl->tpl_vars['sPage'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['aRouter']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['sPath']->key => $_smarty_tpl->tpl_vars['sPath']->value){
$_smarty_tpl->tpl_vars['sPath']->_loop = true;
 $_smarty_tpl->tpl_vars['sPage']->value = $_smarty_tpl->tpl_vars['sPath']->key;
?>
			aRouter['<?php echo $_smarty_tpl->tpl_vars['sPage']->value;?>
'] = '<?php echo $_smarty_tpl->tpl_vars['sPath']->value;?>
';
		<?php } ?>
	</script>
	
	
	<?php echo $_smarty_tpl->tpl_vars['aHtmlHeadFiles']->value['js'];?>


	
	<script type="text/javascript">
		var tinyMCE = false;
		ls.lang.load(<?php echo smarty_function_json(array('var'=>$_smarty_tpl->tpl_vars['aLangJs']->value),$_smarty_tpl);?>
);
		ls.registry.set('comment_max_tree','<?php echo smarty_function_cfg(array('name'=>"module.comment.max_tree"),$_smarty_tpl);?>
');
		ls.lang.load(<?php echo smarty_function_lang_load(array('name'=>"nav_select_item"),$_smarty_tpl);?>
);
	</script>
	<?php echo smarty_function_hook(array('run'=>'html_head_end'),$_smarty_tpl);?>

	<style>
	    #header { position: fixed; min-width: 320px; max-width: 640px; width: 100%; z-index: 200; background: #5c6bc0; }
	    #write, #search { position: fixed; top: 44px; z-index: 200; }
	</style>
</head>



<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
	<?php $_smarty_tpl->tpl_vars['body_classes'] = new Smarty_variable(($_smarty_tpl->tpl_vars['body_classes']->value).(' ls-user-role-user'), null, 0);?>
	
	<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator()){?>
		<?php $_smarty_tpl->tpl_vars['body_classes'] = new Smarty_variable(($_smarty_tpl->tpl_vars['body_classes']->value).(' ls-user-role-admin'), null, 0);?>
	<?php }?>
<?php }else{ ?>
	<?php $_smarty_tpl->tpl_vars['body_classes'] = new Smarty_variable(($_smarty_tpl->tpl_vars['body_classes']->value).(' ls-user-role-guest'), null, 0);?>
<?php }?>

<?php if (!$_smarty_tpl->tpl_vars['oUserCurrent']->value||($_smarty_tpl->tpl_vars['oUserCurrent']->value&&!$_smarty_tpl->tpl_vars['oUserCurrent']->value->isAdministrator())){?>
	<?php $_smarty_tpl->tpl_vars['body_classes'] = new Smarty_variable(($_smarty_tpl->tpl_vars['body_classes']->value).(' ls-user-role-not-admin'), null, 0);?>
<?php }?>


<body class="<?php echo $_smarty_tpl->tpl_vars['body_classes']->value;?>
">
	<?php echo smarty_function_hook(array('run'=>'body_begin'),$_smarty_tpl);?>

	
	<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
		<?php echo $_smarty_tpl->getSubTemplate ('window_favourite_form_tags.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<?php }?>
	

	<div id="container" class="<?php echo smarty_function_hook(array('run'=>'container_class'),$_smarty_tpl);?>
">
		<?php if ($_smarty_tpl->tpl_vars['oUserCurrent']->value){?>
			<?php echo $_smarty_tpl->getSubTemplate ('userbar_menu.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php }?>
        <?php echo $_smarty_tpl->getSubTemplate ('header_top.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<div id="wrapper">
			<?php echo $_smarty_tpl->getSubTemplate ('nav.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


			<div id="content" role="main" 
				class="<?php if ($_smarty_tpl->tpl_vars['noBg']->value){?>no-bg<?php }?> 
				       <?php if ($_smarty_tpl->tpl_vars['aPaging']->value&&$_smarty_tpl->tpl_vars['aPaging']->value['iCountPage']>1&&!$_smarty_tpl->tpl_vars['noBg']->value){?>multipage<?php }?>
					   <?php if ($_smarty_tpl->tpl_vars['sidebarPosition']->value=='left'){?>content-right<?php }?> <?php echo smarty_function_hook(array('run'=>'wrapper_class'),$_smarty_tpl);?>
"
				<?php if ($_smarty_tpl->tpl_vars['sMenuItemSelect']->value=='profile'){?>itemscope itemtype="http://data-vocabulary.org/Person"<?php }?>>
				
				
				<?php echo $_smarty_tpl->getSubTemplate ('system_message.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

				
				<?php echo smarty_function_hook(array('run'=>'content_begin'),$_smarty_tpl);?>
<?php }} ?>