{include file='header.tpl' menu='deleted'}
    <ul class="nav nav-pills">
        <li {if $sMenuSubItemSelect=='topics'}class="active"{/if}><a href="{router page='deleted'}topics/">{$aLang.topic_title}</a></li>
        <li {if $sMenuSubItemSelect=='blogs'}class="active"{/if}><a href="{router page='deleted'}blogs/">{$aLang.blogs}</a></li>
    </ul>
    <div id="list_wrapper">
        {*<form action="" method="POST" id="form-blogs-search" onsubmit="return false;" class="search search-item">*}
            {*<input type="text" placeholder="{$aLang.blogs_search_title_hint}" autocomplete="off" name="blog_title"*}
                   {*class="input-text" value=""*}
                   {*onkeyup="ls.timer.run(ls.blog.searchBlogs,'blogs_search',['form-blogs-search'],1000);">*}
        {*</form>*}

        <div id="blogs-list-search" style="display:none;"></div>

        <div id="blogs-list-original">
            {router page='blogs' assign=sBlogsRootPage}
            {include file='deleted_blog_list.tpl' bBlogsUseOrder=true sBlogsRootPage=$sBlogsRootPage}
            {include file='paging.tpl' aPaging=$aPaging}
        </div>
    </div>
{include file='footer.tpl'}

