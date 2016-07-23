<!doctype html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="ru"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="ru"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->

<head>
	{if $sAction!='login'}
	{if !$oUserCurrent}

	{/if}{/if}
	<script async src="{cfg name="path.static.skin"}/js/spoiler.js"></script>
	{hook run='html_head_begin'}
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title>{$sHtmlTitle}</title>
	
	<meta name="description" content="{$sHtmlDescription}">
	<meta name="keywords" content="{$sHtmlKeywords}">
 <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.css">
 <link rel="stylesheet" href="{cfg name="path.static.skin"}/css/menu.css">
	{$aHtmlHeadFiles.css}
	<link href="{cfg name='path.static.skin'}/images/favicon.ico?v1" rel="shortcut icon" />
	<link rel="search" type="application/opensearchdescription+xml" href="{router page='search'}opensearch/" title="{cfg name='view.name'}" />

	{if $aHtmlRssAlternate}
		<link rel="alternate" type="application/rss+xml" href="{$aHtmlRssAlternate.url}" title="{$aHtmlRssAlternate.title}">
	{/if}

	{if $sHtmlCanonical}
		<link rel="canonical" href="{$sHtmlCanonical}" />
	{/if}
	
	{if $bRefreshToHome}
		<meta  HTTP-EQUIV="Refresh" CONTENT="3; URL={cfg name='path.root.web'}/">
	{/if}
	
	<script type="text/javascript">
		var DIR_WEB_ROOT 			= '{cfg name="path.root.web"}';
		var DIR_STATIC_SKIN 		= '{cfg name="path.static.skin"}';
		var DIR_ROOT_ENGINE_LIB 	= '{cfg name="path.root.engine_lib"}';
		var LIVESTREET_SECURITY_KEY = '{$LIVESTREET_SECURITY_KEY}';
		var SESSION_ID				= '{$_sPhpSessionId}';
		var BLOG_USE_TINYMCE		= '{cfg name="view.tinymce"}';
		
		var TINYMCE_LANG = 'en';
		{if $oConfig->GetValue('lang.current') == 'russian'}
			TINYMCE_LANG = 'ru';
		{/if}

		var aRouter = new Array();
		{foreach from=$aRouter key=sPage item=sPath}
			aRouter['{$sPage}'] = '{$sPath}';
		{/foreach}
	</script>
	
<script>
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}	
</script>
	{$aHtmlHeadFiles.js}
	<script type="text/javascript">
		var tinyMCE = false;
		ls.lang.load({json var = $aLangJs});
		ls.registry.set('comment_max_tree',{json var=$oConfig->Get('module.comment.max_tree')});
		ls.registry.set('block_stream_show_tip',{json var=$oConfig->Get('block.stream.show_tip')});

	</script>
	
	
	{if {cfg name='view.grid.type'} == 'fluid'}
		<style>
			/* #container {
				min-width: {cfg name='view.grid.fluid_min_width'}px;
				max-width: {cfg name='view.grid.fluid_max_width'}px;
			} */
		</style>
	{else}

	{/if}
	
	
	{hook run='html_head_end'}
	
<style>
{literal}
@font-face {
 font-family:"Modernist One";
 src: url('/Modernist_One.ttf');
}
@font-face {
 font-family:"DS Goose";
 src: url('/DS_Goose.ttf');
}
{/literal}

</style>
	

</head>



{if $oUserCurrent}
	{assign var=body_classes value=$body_classes|cat:' ls-user-role-user'}
	
	{if $oUserCurrent->isAdministrator()}
		{assign var=body_classes value=$body_classes|cat:' ls-user-role-admin'}
	{/if}
	{if $oUserCurrent->isGlobalModerator()}
		{assign var=body_classes value=$body_classes|cat:' ls-user-role-moderator'}
	{/if}
{else}
	{assign var=body_classes value=$body_classes|cat:' ls-user-role-guest'}
{/if}

{if !$oUserCurrent or ($oUserCurrent and !$oUserCurrent->isAdministrator())}
	{assign var=body_classes value=$body_classes|cat:' ls-user-role-not-admin'}
{/if}

{add_block group='toolbar' name='toolbar_admin.tpl' priority=100}
{add_block group='toolbar' name='toolbar_scrollup.tpl' priority=-100}




<body class="{$body_classes} width-{cfg name='view.grid.type'}">
<img src="{cfg name="path.static.skin"}/images/woona-big.png" class="woona">
<script src="{cfg name="path.static.skin"}/js/woona.js"></script>
	{hook run='body_begin'}
	<nav id="userbar" class="clearfix">
	{hook run='userbar_nav'}

		{include file='userbar.tpl'}

	{if $oUserCurrent}
		{include file='window_write.tpl'}
		{include file='window_favourite_form_tags.tpl'}
	{else}
		{include file='window_login.tpl'}
	{/if}

	{include file='nav.tpl'}

	<div id="container" class="{hook run='container_class'}">
		{include file='header_top.tpl'}

		<div id="wrapper" class="{hook run='wrapper_class'}">
			{if !$noSidebar && $sidebarPosition == 'left'}
				{include file='sidebar.tpl'}
			{/if}

			{if !$noSidebar && $sidebarPosition != 'left'}
				{include file='sidebar.tpl'}
			{/if} <!-- Krivo... -->
		
			<div id="content" role="main" 
				class="{if $noSidebar}content-full-width{/if} 
					   {if $sidebarPosition == 'left'}content-right{/if}"
				{if $sMenuItemSelect=='profile'}itemscope itemtype="http://data-vocabulary.org/Person"{/if}>

				{include file='nav_content.tpl'}
				{include file='system_message.tpl'}
	
				{hook run='content_begin'}
