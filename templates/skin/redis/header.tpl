<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="ru"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="ru"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="ru"> <!--<![endif]-->

<head>
    <meta name="viewport" content="width=device-width; initial-scale=1">

    {hook run='html_head_begin'}

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>{$sHtmlTitle}</title>

    <meta name="description" content="{$sHtmlDescription}">
    <meta name="keywords" content="{$sHtmlKeywords}">
    <meta name="referrer" content="{cfg name="view.referrer_policy"}">

    <link href="{cfg name='path.static.skin'}/images/favicon.ico?v1" rel="shortcut icon"/>
    <link rel="search" type="application/opensearchdescription+xml" href="{router page='search'}opensearch/"
          title="{cfg name='view.name'}"/>

    {if $aHtmlRssAlternate}
        <link rel="alternate" type="application/rss+xml" href="{$aHtmlRssAlternate.url}"
              title="{$aHtmlRssAlternate.title}">
    {/if}

    {if $sHtmlCanonical}
        <link rel="canonical" href="{$sHtmlCanonical}"/>
    {/if}

    {if $bRefreshToHome}
        <meta HTTP-EQUIV="Refresh" CONTENT="3; URL={cfg name='path.root.web'}/">
    {/if}

    <script src="/static/{cfg name="frontend.version"}/vendor.bundle.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">

    <script type="text/javascript">
        var DIR_WEB_ROOT = '{cfg name="path.root.web"}';
        var DIR_STATIC_SKIN = '{cfg name="path.static.skin"}';
        var DIR_ROOT_ENGINE_LIB = '{cfg name="path.root.engine_lib"}';
        var LIVESTREET_SECURITY_KEY = '{$LIVESTREET_SECURITY_KEY}';
        var SESSION_ID = '{$_sPhpSessionId}';
        var BLOG_USE_TINYMCE = '{cfg name="view.tinymce"}';
        var TITLE = document.title;

        var LOGGED_IN = {if $oUserCurrent}true{else}false{/if};

        var USERNAME = {if $oUserCurrent}"{$oUserCurrent->getLogin()}"
        {else}null{/if};
        var USER_ID = {if $oUserCurrent}{$oUserCurrent->getId()}{else}0{/if};
        var IS_ADMIN = {if $oUserCurrent}{$oUserCurrent->getIsAdministrator()}{else}false{/if};

        var SOCKET_URL = "{$oConfig->GetValue("sockets_url")}"

        var TINYMCE_LANG = 'en';
        {if $oConfig->GetValue('lang.current') == 'russian'}
        TINYMCE_LANG = 'ru';
        {/if}

        var aRouter = new Array();
        {foreach from=$aRouter key=sPage item=sPath}
        aRouter['{$sPage}'] = '{$sPath}';
        {/foreach}
    </script>
    <script src="/static/{cfg name="frontend.version"}/main.bundle.js"></script>

    <script type="text/javascript">
        var tinyMCE = false;
        ls.lang.load({json var = $aLangJs});
        ls.registry.set('comment_max_tree',{json var=$oConfig->Get('module.comment.max_tree')});
        ls.registry.set('block_stream_show_tip',{json var=$oConfig->Get('block.stream.show_tip')});
    </script>

    <script src='https://www.google.com/recaptcha/api.js'></script>


    <link rel="stylesheet" href="/static/{cfg name="frontend.version"}/{cfg name="theme"}.css" type="text/css"/>
    {literal}
        <script>
            if (parseInt(localStorage.getItem('square_avatars'))) {
                document.write(`
	<style>
		.comment .comment-avatar {
			border-radius: 0px !important;
		}
		.item-list li .avatar {
			border-radius: 0px !important;
		}
		.user-avatar, .avatar {
			border-radius: 0px !important;
		}
		.topic-author-avatar {
			border-radius: 0px !important;
		}
		.topic.topic-type-talk .topic-header .topic-info .avatar {
			border-radius: 0px !important;
		}
		.topic .topic-header .topic-data-wrapper .topic-author-avatar {
			margin-left: 10px;
		}
	</style>
	`)
            }

            if (parseInt(localStorage.getItem('grey_panel'))) {
                document.write(`
	<style>
		#rightbar {
			background: #303030;
		}
		.navbar-collapse {
			background: #303030;
			border: 1px solid #303030 !important;
		}
	</style>
	`)
            }
        </script>
    {/literal}




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
{hook run='body_begin'}


{if $oUserCurrent}
    {include file='window_write.tpl'}
    {include file='window_favourite_form_tags.tpl'}
{else}
    {include file='window_login.tpl'}
{/if}

{include file='header_top.tpl'}
<div id="container" class="{hook run='container_class'}">
    <div id="progressbar"></div>

    <div id="wrapper" class="row container-fluid {hook run='wrapper_class'}">

        {if $sTopBlock}
            <div class="col-md-12" id="top_block">
                {include file=$sTopBlock}
            </div>
        {/if}
        {if !$noSidebar && $sidebarPosition == 'left'}
            {include file='sidebar.tpl'}
        {/if}

        <div id="content" role="main"
             class="col-md-9 {if $noSidebar}content-full-width{/if}
					   {if $sidebarPosition == 'left'}content-right{/if}"
             {if $sMenuItemSelect=='profile'}itemscope itemtype="http://data-vocabulary.org/Person"{/if}>

            {include file='nav_content.tpl'}
            {include file='system_message.tpl'}

            {hook run='content_begin'}
