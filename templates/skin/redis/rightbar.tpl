{*<div id="rightbar">*}
    {*<div class="rightbar-item">*}
        {*<a href="#"><img src="{if $oUserCurrent}{$oUserCurrent->getProfileAvatarPath(48)}{else}https://chenhan1218.github.io/img/profile.png{/if}" alt="avatar" class="avatar" /></a>*}
    {*</div>*}
    {*<div class="rightbar-item">*}
        {*<a href="#"><i class="material-icons">mail_outline</i></a>*}
    {*</div>*}
    {*<div class="rightbar-item">*}
        {*<a href="#"><i class="material-icons">mode_edit</i></a>*}
    {*</div>*}
    {*<div class="rightbar-item">*}
        {*<a href="#"><i class="material-icons">question_answer</i></a>*}
    {*</div>*}
    {*<div class="rightbar-item">*}
        {*<a href="#" title="spoil/despoil" onclick="despoil(); return false;">*}
            {*<i class="material-icons">visibility_off</i>*}
        {*</a>*}
    {*</div>*}
    {*<div class="rightbar-item">*}
        {*<a href="#" title="widemode" onclick="widemode(); return false;">*}
            {*<i class="material-icons">code</i>*}
        {*</a>*}
    {*</div>*}
    {*{include file='blocks.tpl' group='toolbar'}*}
    {*{if $oUserCurrent}*}
        {*{assign var=aPagingCmt value=$params.aPagingCmt}*}
        {*<div class="rightbar-item" id="prevcomment">*}
            {*<a href="#" class="update-comments" onclick="ls.comments.goToPrevComment(); return false;" title="Прошлый новый комментарий"><i id="go-back" class="fa fa-arrow-left"></i></a>*}
        {*</div>*}
        {*<div class="rightbar-item" id="update" style="{if $aPagingCmt and $aPagingCmt.iCountPage > 1}display: none;{/if}">*}
            {*<a href="#" class="update-comments" onclick="ls.comments.load({$params.iTargetId},'{$params.sTargetType}'); return false;"><i id="update-comments" class="fa fa-refresh"></i></a>*}
            {*<a href="#" class="new-comments" id="new_comments_counter" style="display: none;" title="{$aLang.comment_count_new}" onclick="ls.comments.goToNextComment(); return false;"></a>*}

            {*<input type="hidden" id="comment_last_id" value="{$params.iMaxIdComment}" />*}
            {*<input type="hidden" id="comment_use_paging" value="{if $aPagingCmt and $aPagingCmt.iCountPage>1}1{/if}" />*}
        {*</div>*}
        {*<script>*}
            {*function autoload(){*}
                {*if (document.getElementById('autoload').checked) {*}
                    {*ls.comments.load({$params.iTargetId}, '{$params.sTargetType}', null, true);*}
                {*}*}
            {*}*}

            {*console.log(setInterval(autoload, 10000));*}
        {*</script>*}
    {*{/if}*}
    {*<div class="rightbar-item" {if $iUserCurrentCountTalkNew}style="display: block;"{/if}>*}
        {*<a href="{router page='talk'}" title="+{$iUserCurrentCountTalkNew}">*}
            {*<i class="fa fa-envelope"></i>*}
        {*</a>*}
    {*</div>*}
{*</div>*}