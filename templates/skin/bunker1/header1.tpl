<!doctype html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="ru"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="ru"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->

<head>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" />
	<script type="text/javascript">
	window.onload = function(){

		Waves.init();
	};
	</script>
<link href="/waves.min.css" type="text/css" rel="stylesheet" />
<script src="/waves.min.js" type="text/javascript"></script>
	{hook run='html_head_begin'}
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title>{$sHtmlTitle}</title>
	<script type="text/javascript"
	src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<style type="text/css">
		.spoiler_body{
			display: none; 
		}
		.spoiler_title{
		cursor: pointer; 
		}
	</style>
	</script>
	<meta name="description" content="{$sHtmlDescription}">
	<meta name="keywords" content="{$sHtmlKeywords}">

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
	function spoilers() {
		$(document).ready(function(){
			$('.spoiler_title').click(function(){
				$(this).next('.spoiler_body').toggle('normal');
				return false;
			});
		});
	}
	spoilers()
	</script>
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
	
	
	{$aHtmlHeadFiles.js}
	
	<script type="text/javascript">
		var tinyMCE = false;
		ls.lang.load({json var = $aLangJs});
		ls.registry.set('comment_max_tree',{json var=$oConfig->Get('module.comment.max_tree')});
		ls.registry.set('block_stream_show_tip',{json var=$oConfig->Get('block.stream.show_tip')});
	</script>
	
	
	{if {cfg name='view.grid.type'} == 'fluid'}
		<style>
			#container {
				min-width: {cfg name='view.grid.fluid_min_width'}px;
				max-width: {cfg name='view.grid.fluid_max_width'}px;
			}
		</style>
	{else}
	{/if}
	
	
	{hook run='html_head_end'}
</head>



{if $oUserCurrent}
	{assign var=body_classes value=$body_classes|cat:' ls-user-role-user'}
	
	{if $oUserCurrent->isAdministrator()}
		{assign var=body_classes value=$body_classes|cat:' ls-user-role-admin'}
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
<nav id="userbar" class="clearfix" style="bottom: 0%; left; 0px;">
	<form action="{router page='search'}topics/" class="search">
		<input type="text" placeholder="{$aLang.search}" maxlength="255" name="q" class="input-text">
		<input type="submit" value="" title="{$aLang.search_submit}" class="input-submit icon icon-search">
	</form>
	
	<ul class="nav nav-userbar">
		{if $oUserCurrent}
		<li>
		<input type="button" value="Wide mode" class="spoiler_button"
    onclick=wide()></li>
	<li>
	<input type="button" value="Standart mode" class="spoiler_button"
    onclick=dewide()></li>
	<li><div style="" class="spoiler_button"><a href="#top">&uarr;</a></div></li><li>
	<input type="button" value="Close all" class="spoiler_button"
    onclick=$("div[class^='spoiler_body']").hide('normal')>
	</li>
	<li>
	<input type="button" value="Open all" class="spoiler_button"
    onclick=$("div[class^='spoiler_body']").show('normal')>

	</li>
	<li><div style="" class="spoiler_button"><a href="#footer">&darr;</a></div></li><li>
			<li class="nav-userbar-username">
				<a href="{$oUserCurrent->getUserWebPath()}" class="username">
					<img src="{$oUserCurrent->getProfileAvatarPath(24)}" alt="avatar" class="avatar" />
					{$oUserCurrent->getLogin()}
				</a>
			</li>
<script>
$('a[href=#]').click(function(e) {
    e.preventDefault();
});
</script>
<style>
.spoiler_button {
	height: 38px;
	background: white;
	border: none;
}
.spoiler_button2 {
	height: 38px;
	color: white;
	background: #222;
	border: none;
}
</style>
	{hook run='userbar_nav'}
			<li><a href="{router page='topic'}add/" class="write" id="modal_write_show">{$aLang.block_create}</a></li>
			<li><a href="{router page='talk'}" {if $iUserCurrentCountTalkNew}class="new-messages"{/if} id="new_messages" title="{if $iUserCurrentCountTalkNew}{$aLang.user_privat_messages_new}{/if}">{$aLang.user_privat_messages}{if $iUserCurrentCountTalkNew} ({$iUserCurrentCountTalkNew}){/if}</a></li>
			<li><a href="{router page='settings'}profile/">{$aLang.user_settings}</a></li>
			{hook run='userbar_item'}
			<li><a href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}">{$aLang.exit}</a></li>
		{else}
			{hook run='userbar_item'}
			<li><a href="{router page='login'}" class="js-login-form-show">{$aLang.user_login_submit}</a></li>
			<li><a href="{router page='registration'}" class="js-registration-form-show">{$aLang.registration_submit}</a></li>
		{/if}
	</ul>
</nav>
<a name="top">
	{hook run='body_begin'}
<script>
function wide(){
	var gifs = document.getElementById('sidebar');
	var element = document.getElementById('content');
	element.style.width = "100%";
	element.style.margin = "0px";
	gifs.style.display = "none";
}
function dewide(){
	var gifs = document.getElementById('sidebar');
	var element = document.getElementById('content');
	element.style.width = "70%";
	element.style.margin = "";
	gifs.style.display = "";
}
</script>	
<style type="text/css">
.primer {
  z-index: 999999999;
  position: fixed;
  bottom: 0%;
  right: 0%;
}
</style>
	{if $oUserCurrent}
		{include file='window_write.tpl'}
		{include file='window_favourite_form_tags.tpl'}
	{else}
		{include file='window_login.tpl'}
	{/if}
	

	<div id="container" class="{hook run='container_class'}">
		{include file='header_top.tpl'}
		
		{include file='nav.tpl'}
		<div id="wrapper" class="{hook run='wrapper_class'}">
			{if !$noSidebar && $sidebarPosition == 'left'}
				{include file='sidebar.tpl'}
			{/if}
		<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter31418953 = new Ya.Metrika({
                    id:31418953,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true,
                    trackHash:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/31418953" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
			<div id="content" role="main" 
				class="{if $noSidebar}content-full-width{/if} 
					   {if $sidebarPosition == 'left'}content-right{/if}"
				{if $sMenuItemSelect=='profile'}itemscope itemtype="http://data-vocabulary.org/Person"{/if}>
				
				{include file='nav_content.tpl'}
				{include file='system_message.tpl'}
				
				{hook run='content_begin'}