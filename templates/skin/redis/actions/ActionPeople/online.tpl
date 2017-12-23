{include file='header.tpl' menu='people'}
<ul class="nav nav-pills">
    <li {if $sMenuItemSelect=='all'}class="active"{/if}><a href="{router page='people'}">{$aLang.people_menu_users_all}</a></li>
    <li {if $sMenuItemSelect=='online'}class="active"{/if}><a href="{router page='people'}online/">{$aLang.people_menu_users_online}</a></li>
</ul>
<div id="list_wrapper">
    {include file='user_list.tpl' aUsersList=$aUsersLast}
</div>
{include file='footer.tpl'}