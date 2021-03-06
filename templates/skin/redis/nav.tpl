<nav class="navbar navbar-default">
    <div class="container-fluid nav nav-main">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                    <li {if $sMenuHeadItemSelect=='blog'}class="active"{/if}><a href="/">{$aLang.topic_title}</a></li>
                    <li {if $sMenuHeadItemSelect=='blogs'}class="active"{/if}><a href="{router page='blogs'}">{$aLang.blogs}</a></li>
                    {if $oUserCurrent}
                        <li {if $sMenuHeadItemSelect=='deleted'}class="active"{/if}><a href="{router page='deleted'}">{$aLang.deleted_menu}</a></li>
                    {/if}
                    <li {if $sMenuHeadItemSelect=='people'}class="active"{/if}><a href="{router page='people'}">{$aLang.people}</a></li>
                    {*<li {if $sMenuHeadItemSelect=='stream'}class="active"{/if}><a href="{router page='stream'}">{$aLang.stream_menu}</a></li>*}
                    {if $oUserCurrent}
                        <li {if $sMenuHeadItemSelect=='feedbacks'}class="active"{/if}><a href="{router page='feedbacks'}">{$aLang.feedbacks.header}</a></li>
                    {/if}
                    <li {if $sMenuHeadItemSelect=='quotes'}class="active"{/if}><a href="{router page='quotes'}">{$aLang.quotes_title}</a></li>
                    <li><a href="https://ponyfiction.org/" target="_blank">{$aLang.library}</a></li>

                    {if $oConfig->GetValue("view.new_year_timer") }
                        {include "nav.new_year_timer.tpl"}
                    {/if}

                    {hook run='main_menu_item'}
            </ul>

            <ul id="navbar-right-big" class="nav navbar-nav navbar-right">
                <li class="iconic running"><a href="/page/filter/current-flud"><img src="/templates/skin/redis/images/bunkeryasha-left.gif" /></a></li>
                {if $oUserCurrent}
                    <li class="head_collapse" title="Свернуть/развернуть шапку">
                        <i class="material-icons">keyboard_arrow_up</i>
                    </li>
                    <li>
                        <a class="iconic" title="Написать пост" href="{router page='topic'}add/" id="modal_write_show"><i class="material-icons">mode_edit</i></a>
                    </li>
                    <li>
                        <a class="iconic" title="Ответы" href="{router page='feedbacks'}"><i class="material-icons">question_answer</i></a>
                    </li>
                    <li>
                        <a class="iconic" title="Избранное" href="{$oUserCurrent->getUserWebPath()}favourites/topics/"><i class="material-icons">favorite</i></a>
                    </li>
                    <li>
                        <a class="iconic" title="Настройки" href="{router page='settings'}profile/"><i class="material-icons">settings</i></a>
                    </li>
                    <li>
                        <a class="iconic" title="Выход" href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}"><i class="material-icons">exit_to_app</i></a>
                    </li>
                {else}
                    <li class="head_collapse"  title="Свернуть/развернуть шапку">
                        <i class="material-icons">keyboard_arrow_up</i>
                    </li>
                    <li><a title="Войти" href="{router page='login'}" class="js-login-form-show">{$aLang.user_login_submit}</a></li>
                    <li><a title="Регистрация" href="{router page='registration'}" class="js-registration-form-show">{$aLang.registration_submit}</a></li>
                {/if}
                <li>
                    <a href="{if $oUserCurrent}{$oUserCurrent->getUserWebPath()}{/if}"  title="{if $oUserCurrent}{$oUserCurrent->getLogin()}{/if}" class="user-wrapper {if $oUserCurrent}with-login{/if}">{if $oUserCurrent}{$oUserCurrent->getLogin()}{/if}
                        <span class="avatar-wrapper"><img src="{if $oUserCurrent}{$oUserCurrent->getProfileAvatarPath(48)}{else}https://chenhan1218.github.io/img/profile.png{/if}" alt="avatar" class="avatar"/></span>
                    </a>
                </li>
            </ul>

            <ul id="navbar-right-small" class="nav navbar-nav navbar-right">
                <li class="with_dropd">
                    <a {if $oUserCurrent}href="{$oUserCurrent->getUserWebPath()}"{/if} class="user-wrapper">{if $oUserCurrent}{/if}
                        <span class="avatar-wrapper"><img src="{if $oUserCurrent}{$oUserCurrent->getProfileAvatarPath(48)}{else}https://chenhan1218.github.io/img/profile.png{/if}" alt="avatar" class="avatar"/></span>
                    </a>
                    <ul class="dropd">
                        {if $oUserCurrent}
                            <li class="head_collapse" title="Свернуть/развернуть шапку">
                                <i class="material-icons">keyboard_arrow_up</i>
                            </li>
                            <li>
                                <a class="iconic" title="Написать пост" href="{router page='topic'}add/" id="modal_write_show"><i class="material-icons">mode_edit</i></a>
                            </li>
                            <li>
                                <a class="iconic" title="Ответы" href="{router page='feedbacks'}"><i class="material-icons">question_answer</i></a>
                            </li>
                            <li>
                                <a class="iconic" title="Избранное" href="{$oUserCurrent->getUserWebPath()}favourites/topics/"><i class="material-icons">favorite</i></a>
                            </li>
                            <li>
                                <a class="iconic" title="Настройки" href="{router page='settings'}profile/"><i class="material-icons">settings</i></a>
                            </li>
                            <li>
                                <a class="iconic" title="Выход" href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}"><i class="material-icons">exit_to_app</i></a>
                            </li>
                        {else}
                            <li class="head_collapse"  title="Свернуть/развернуть шапку">
                                <i class="material-icons">keyboard_arrow_up</i>
                            </li>
                            <li><a title="Войти" href="{router page='login'}" class="js-login-form-show">{$aLang.user_login_submit}</a></li>
                            <li><a title="Регистрация" href="{router page='registration'}" class="js-registration-form-show">{$aLang.registration_submit}</a></li>
                        {/if}
                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /.container-fluid -->
</nav>

{*
<div id="padorupadoru">
    <div class="counter">
        <span id="new-year-timer"></span>
        <span id="new-year-text">&nbsp;до нового года!</span>
    </div>
    <img src="/templates/skin/redis/images/bunkeryasha-right.gif"  alt=""/>
</div>

<script>
function untilNewYear() {
    let newYearDate = new Date('2022-01-01 00:00:00');
    if ((newYearDate - new Date()) > 0 ) {
        document.querySelector('#new-year-timer').innerText = window.formatTime(newYearDate - new Date()); 
    } else {
        document.querySelector('#new-year-timer').innerText = "";
        document.querySelector('#new-year-text').innerText = "С Новым Годом! ^~^";
        clearInterval(newYearIntervalID);
    }

}
const newYearIntervalID = setInterval(()=>untilNewYear(), 1000);
</script>
*}