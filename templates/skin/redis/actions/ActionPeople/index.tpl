{include file='header.tpl' menu='people'}
<ul class="nav nav-pills">
    <li {if $sMenuItemSelect=='all'}class="active"{/if}><a href="{router page='people'}">{$aLang.people_menu_users_all}</a></li>
    <li {if $sMenuItemSelect=='online'}class="active"{/if}><a href="{router page='people'}online/">{$aLang.people_menu_users_online}</a></li>
</ul>

{hook run='menu_people'}

<div id="list_wrapper">
    <form action="" method="POST" id="form-users-search" onsubmit="return false;" class="search search-item">
        <input id="search-user-login" type="text" placeholder="{$aLang.user_search_title_hint}" autocomplete="off"
               name="user_login" value="" class="input-text"
               onkeyup="ls.timer.run(ls.user.searchUsers,'users_search',['form-users-search'],1000);">
    </form>

    <ul id="user-prefix-filter" class="search-abc">
        <li class="active"><a href="#" class="link-dotted"
                              onclick="return ls.user.searchUsersByPrefix('',this);">{$aLang.user_search_filter_all}</a>
        </li>
        {foreach from=$aPrefixUser item=sPrefixUser}
            <li><a href="#" class="link-dotted"
                   onclick="return ls.user.searchUsersByPrefix('{$sPrefixUser}',this);">{$sPrefixUser}</a></li>
        {/foreach}
    </ul>

    <div id="users-list-search" style="display:none;"></div>

    <div id="users-list-original">
        {router page='people' assign=sUsersRootPage}
        {include file='user_list.tpl' aUsersList=$aUsersRating bUsersUseOrder=true sUsersRootPage=$sUsersRootPage}
    </div>
</div>

{include file='footer.tpl'}