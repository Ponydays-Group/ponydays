<div id="rightbar">
    <div id="rightbar_menu_top">

        <div class="rightbar-item with_dropd user_item">
            <a href="{if $oUserCurrent}{$oUserCurrent->getUserWebPath()}{/if}">
                <img
                        src="{if $oUserCurrent}{$oUserCurrent->getProfileAvatarPath(48)}{else}https://chenhan1218.github.io/img/profile.png{/if}"
                        alt="avatar" class="avatar"/>
            </a>
            {if $oUserCurrent}
                <div class="dropd">
                    <div class="dropd-item">
                        <a href="{router page='topic'}add/" id="modal_write_show"><i class="material-icons">mode_edit</i></a>
                    </div>
                    <div class="dropd-item">
                        <a href="{router page='feedbacks'}"><i class="material-icons">question_answer</i></a>
                    </div>
                    <div class="dropd-item">
                        <a href="{$oUserCurrent->getUserWebPath()}favourites/topics/"><i class="material-icons">favorite</i></a>
                    </div>
                    <div class="dropd-item">
                        <a href="{router page='settings'}profile/"><i class="material-icons">settings</i></a>
                    </div>
                    <div class="dropd-item">
                        <a href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}"><i class="material-icons">exit_to_app</i></a>
                    </div>
                </div>
            {/if}
        </div>

        {if $oUserCurrent}
            <div class="rightbar-item">
                <a href="{router page='talk'}" id="new_messages"><i class="material-icons">mail_outline</i><span class="new-comments"
                    {if !$iUserCurrentCountTalkNew}style="display: none;"{/if}
                    title="{$aLang.comment_count_new}">{$iUserCurrentCountTalkNew}</span></a></a>
            </div>
        {/if}

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
    </div>

    <div id="rightbar_menu_bottom">

        <div class="rightbar-item keyboard_shortcuts_trigger">
            <a href="#" title="Shortcuts" onclick="return false;">
                <i class="material-icons">keyboard</i>
            </a>
        </div>

    </div>
</div>

{include file="comment_shortcuts.tpl"}