<input type="checkbox" id="hmt" class="hidden-menu-ticker">
<label class="btn-menu" for="hmt">
    <span class="first"></span>
    <span class="second"></span>
    <span class="third"></span>
    <a href="/">
        <img src="{cfg name="path.static.skin"}/logo.png" class="logo">
    </a>
</label>

<div class="nav nav-userbar hidden-menu">
<ul>
    <a href="/">
    </a>
    {if $oUserCurrent}
    <li class="nav-userbar-username" title="{$oUserCurrent->getLogin()}">
        <a href="{$oUserCurrent->getUserWebPath()}" class="username">
            {$oUserCurrent->getLogin()}
            <img src="{$oUserCurrent->getProfileAvatarPath(24)}" alt="avatar" class="avatar" />
        </a>
    </li>
    <li title="{$aLang.block_create}">
        <a href="{router page='topic'}add/" class="write" id="modal_write_show">
            {$aLang.block_create}
            <i class="fa fa-pencil-square-o"></i>
        </a>
    </li>
    <li title="{$aLang.user_privat_messages}{if $iUserCurrentCountTalkNew} ({$iUserCurrentCountTalkNew}){/if}">
        <a href="{router page='talk'}" {if $iUserCurrentCountTalkNew}class="new-messages"{/if} id="new_messages">
            {$aLang.user_privat_messages}{if $iUserCurrentCountTalkNew} ({$iUserCurrentCountTalkNew}){/if}
            <i class="fa fa-envelope{if !$iUserCurrentCountTalkNew}-o{/if}"></i>
        </a>
    </li>
    {hook run='athead'}
                        {hook run='atmenu'}
    {hook run='userbar_item'}
    <li title="{$aLang.user_menu_profile_favourites}">
        <a href="{$oUserCurrent->getUserWebPath()}favourites/topics/">
            {$aLang.user_menu_profile_favourites}
            <i class="fa fa-star-half-o"></i>
        </a>
    </li>
    <li title="{$aLang.user_settings}">
        <a href="{router page='settings'}profile/">
            {$aLang.user_settings}
            <i class="fa fa-cogs"></i>
        </a>
    </li>
    <li title="Широкий режим">
        <a href="#" onclick="wide(); return false;" class="widemode" id="wide">
            Широкий режим
        </a>
    </li>
    <li title="Открыть спойлеры">
        <a href="#" onclick="despoil(); return false;" class="spoil" id="spoil">
            Открыть спойлеры
            <i class="fa fa-eye-slash"></i>
        </a>
    </li>
    </ul>
    <ul class="userbar_info">
    <li title="{$aLang.exit}">
        <a href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}">
            {$aLang.exit}
            <i class="fa fa-times-circle-o"></i>
        </a>
    </li>
    </ul>
    {else}
    {hook run='userbar_item'}
    <li>
        <a href="{router page='login'}" class="js-login-form-show">
            {$aLang.user_login_submit}
            <i class="fa fa-user"></i>
        </a>
    </li>
    <li>
        <a href="{router page='registration'}" class="js-registration-form-show">
            {$aLang.registration_submit}
            <i class="fa fa-user-plus"></i>
        </a>
    </li>
    <li title="Широкий режим">
        <a href="#" onclick="wide(); return false;" class="widemode" id="wide">
            Широкий режим
        </a>
    </li>
    <li title="Открыть спойлеры">
        <a href="#" onclick="despoil(); return false;" class="spoil" id="spoil">
            Открыть спойлеры
            <i class="fa fa-eye-slash"></i>
        </a>
    </li>
    </ul>
    {/if}
    </ul>
    <ul class="userbar_info">
    <li title="О версии {Config::Get('site_version')}">
        <a href="/page/about">
            О версии {Config::Get('site_version')}
            <i class="fa fa-info-circle"></i>
        </a>
    </li>
    <li title="Сообщить об ошибке">
        <a href="http://bug.lunavod.ru/">
            Сообщить об ошибке
            <i class="fa fa-exclamation-circle"></i>
        </a>
    </li>
{if $oUserCurrent}
    <li>
        <a href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}">
            {$aLang.exit}
            <i class="fa fa-times-circle-o"></i>
        </a>
    </li>
{/if}
    </ul>
</div>
</nav>
{literal}
<script>
    function wide(){
        var gifs = document.getElementById('sidebar');
        var element = document.getElementById('content');
        element.style.right = "30px";
        element.style.margin = "0px";
        gifs.style.display = "none";
        var el = document.getElementById("wide");
        el.className= "standartmode";
        el.innerHTML = 'Стандартный режим'
        el.parentNode.title = 'Стандартный режим'
        el.onclick=function(){dewide(); return false;}
    }
    function dewide(){
        var gifs = document.getElementById('sidebar');
        var element = document.getElementById('content');
        element.style.right = "";
        element.style.margin = "";
        gifs.style.display = "";
        var el = document.getElementById("wide");
        el.className = "widemode";
        el.innerHTML = 'Широкий режим'
        el.parentNode.title = 'Широкий режим'
        el.onclick=function(){wide(); return false;}
    }
</script>
{/literal}
