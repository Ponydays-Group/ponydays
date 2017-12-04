{include file='header.tpl'}
{include file='menu.blog_edit.tpl'}



{*{if $aBlogUsers}*}
	{*<form method="post" enctype="multipart/form-data" class="mb-20">*}
		{*<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />*}
		{**}
		{*{include file="actions/ActionBlog/admin_users_table.tpl"}*}

		{*<button type="submit" name="submit_blog_admin" class="button button-primary">{$aLang.blog_admin_users_submit}</button>*}
	{*</form>*}

	{*{include file='paging.tpl' aPaging=$aPaging}*}
{*{else}*}
	{*{$aLang.blog_admin_users_empty}*}
{*{/if}*}

<div id="list_wrapper">
	<form action="" method="POST" id="form-users-search" onsubmit="return false;" class="search search-item">
		<input name="blog_id" type="hidden" value="{$oBlog->getId()}" />
		<input id="search-user-login" type="text" placeholder="{$aLang.user_search_title_hint}" autocomplete="off"
			   name="user_login" value="" class="input-text"
			   onkeyup="ls.timer.run(ls.user.searchBlogUsers, 'users_search',['form-users-search'],1000);">
	</form>

	<ul id="user-prefix-filter" class="search-abc">
		<li class="active"><a href="#" class="link-dotted"
							  onclick="return ls.user.searchBlogUsersByPrefix('',this);">{$aLang.user_search_filter_all}</a>
		</li>
        {foreach from=$aPrefixUser item=sPrefixUser}
			<li><a href="#" class="link-dotted"
				   onclick="return ls.user.searchBlogUsersByPrefix('{$sPrefixUser}',this);">{$sPrefixUser}</a></li>
        {/foreach}
	</ul>

		<div id="users-list-search" class="hidden"></div>

	<div id="users-list-original">{include file="actions/ActionBlog/admin_users_table.tpl"}</div>
</div>



{include file='footer.tpl'}