{hook run='content_end'}
</div> <!-- /content -->


{if !$noSidebar && $sidebarPosition != 'left'}
    {include file='sidebar.tpl'}
{/if}
</div> <!-- /wrapper -->

<div id="image-modal"><img src="" id="image-modal-img"/></div>

<footer id="footer">
    {*<div class="copyright">*}
        {*{hook run='copyright'}*}
    {*</div>*}

    {*Версия фронтэнда: {cfg name='frontend.version'}<br>*}
    {*<a target="_blank" href="https://github.com/silvman/ponydays/issues">Сообщить об ошибке / Отправить предложение</a>*}

    {*{hook run='footer_end'}*}
    {*<section class="block">*}
        {*<header class="block-header">*}
            {*<h3>{$aLang.block_tags}</h3>*}
        {*</header>*}


        {*<div class="block-content">*}
            {*<div class="js-block-tags-content" data-type="all">*}
                {*{if $aTags}*}
                    {*<ul class="tag-cloud word-wrap">*}
                        {*{foreach from=$aTags item=oTag}*}
                            {*<li><a class="tag-size-{$oTag->getSize()}" href="{router page='tag'}{$oTag->getText()|escape:'url'}/">{$oTag->getText()|escape:'html'}</a></li>*}
                        {*{/foreach}*}
                    {*</ul>*}
                {*{else}*}
                    {*<div class="notice-empty">{$aLang.block_tags_empty}</div>*}
                {*{/if}*}
            {*</div>*}

            {*{if $oUserCurrent}*}
                {*<div class="js-block-tags-content" data-type="user" style="display: none;">*}
                    {*{if $aTagsUser}*}
                        {*<ul class="tag-cloud word-wrap">*}
                            {*{foreach from=$aTagsUser item=oTag}*}
                                {*<li><a class="tag-size-{$oTag->getSize()}" href="{router page='tag'}{$oTag->getText()|escape:'url'}/">{$oTag->getText()|escape:'html'}</a></li>*}
                            {*{/foreach}*}
                        {*</ul>*}
                    {*{else}*}
                        {*<div class="notice-empty">{$aLang.block_tags_empty}</div>*}
                    {*{/if}*}
                {*</div>*}
            {*{/if}*}
        {*</div>*}
    {*</section>*}
    {**}
    {*<section class="block block-type-blog">*}
        {*<header class="block-header">*}
            {*<h3>ВКонтакте</h3>*}
        {*</header>*}

        {*{literal}*}
            {*<script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>*}

            {*<!-- VK Widget -->*}
            {*<div id="vk_groups"></div>*}
            {*<script type="text/javascript">*}
                {*VK.Widgets.Group("vk_groups", {mode: 0, width: "auto", height: "200", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 105592235);*}
            {*</script>*}
        {*{/literal}*}
    {*</section>*}
    <div id=change_theme_wrapper">
        <img src="{cfg name='path.static.skin'}/images/dark-to-day.png" class="switch-theme" onclick="switchTheme()" id="change_theme">
    </div>
</footer>

</div> <!-- /container -->

{include file='toolbar.tpl'}


<div id="scroll_up"><i class="material-icons">keyboard_arrow_up</i></div>
<div id="scroll_down"><i class="material-icons">keyboard_arrow_down</i></div>

{hook run='body_end'}

{if $oConfig->getValue('frontend.webpack.vendor~sockets')}
<script src="/static/relevant/{cfg name="frontend.webpack.vendor~sockets.js"}"></script>
{/if}
{if $oConfig->getValue('frontend.webpack.sockets')}
<script src="/static/relevant/{cfg name="frontend.webpack.sockets.js"}"></script>
{/if}

</body>
</html>
