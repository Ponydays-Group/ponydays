{*<aside class="toolbar">*}
{*<section class="toolbar-despoil">*}
{*<a href="#" title="spoil/despoil" onclick="despoil(); return false;">*}
{*<i class="material-icons">visibility_off</i>*}
{*</a>*}
{*</section>*}
{*<section class="toolbar-widemode">*}
{*<a href="#" title="widemode" onclick="widemode(); return false;">*}
{*<i class="material-icons">code</i>*}
{*</a>*}
{*</section>*}
{*{include file='blocks.tpl' group='toolbar'}*}
{*<section class="toolbar-talk" {if $iUserCurrentCountTalkNew}style="display: block;"{/if}>*}
{*<a href="{router page='talk'}" title="+{$iUserCurrentCountTalkNew}">*}
{*<i class="fa fa-envelope"></i>*}
{*</a>*}
{*</section>*}
{*</aside>*}

<div id="rightbar">
    <div id="sidebar_menu_top">
        <div class="rightbar-item">
            <a href="#"><img
                        src="{if $oUserCurrent}{$oUserCurrent->getProfileAvatarPath(48)}{else}https://chenhan1218.github.io/img/profile.png{/if}"
                        alt="avatar" class="avatar"/></a>
        </div>
        <div class="rightbar-item">
            <a href="#"><i class="material-icons">mail_outline</i></a>
        </div>
        {*<div class="rightbar-item">*}
        {*<a href="#"><i class="material-icons">mode_edit</i></a>*}
        {*</div>*}
        {*<div class="rightbar-item">*}
        {*<a href="#"><i class="material-icons">question_answer</i></a>*}
        {*</div>*}
        <div class="rightbar-item">
            <a href="#" title="spoil/despoil" onclick="despoil(); return false;">
                <i class="material-icons">visibility_off</i>
            </a>
        </div>
        <div class="rightbar-item">
            <a href="#" title="widemode" onclick="widemode(); return false;">
                <i class="material-icons">code</i>
            </a>
        </div>
        {include file='blocks.tpl' group='toolbar'}
        {*<div class="rightbar-item" {if $iUserCurrentCountTalkNew}style="display: block;"{/if}>*}
        {*<a href="{router page='talk'}" title="+{$iUserCurrentCountTalkNew}">*}
        {*<i class="fa fa-envelope"></i>*}
        {*</a>*}
        {*</div>*}

        {*{include file='comment_shortcuts.tpl'}*}
    </div>
    <div id="rightbar_menu_bottom">
        <div class="rightbar-item">
            <a href="#" title="Shortcuts">
                <i class="material-icons">keyboard</i>
            </a>
        </div>
    </div>
</div>