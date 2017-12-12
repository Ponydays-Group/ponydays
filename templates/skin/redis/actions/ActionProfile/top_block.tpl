<div id="top_block_users">
    <div id="user_background" style="background: url({$oUserCurrent->getProfileFoto()});">
        <p class="upload-photo">
            <a href="#" onclick="ls.user.uploadFoto(); return false;"><i class="material-icons">file_upload</i></a>&nbsp;&nbsp;&nbsp;
            <a href="#" id="foto-remove" onclick="return ls.user.removeFoto();" style="{if !$oUserCurrent->getProfileFoto()}display:none;{/if}"><i class="material-icons">delete</i></a>
        </p>
        <img src="{$oUserProfile->getProfileAvatar(100)}" class="avatar {if $oUserProfile->isOnline()}online{/if}" />
        <div id="user_background_grad">{$oUserProfile->getLogin()}</div>
    </div>

    <ul class="profile-tabs">
        <li {if $sAction=='profile' && $aParams[0]=='created'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}created/topics/">{$aLang.user_menu_publication}{if ($iCountCreated)>0} ({$iCountCreated}){/if}</a></li>
        <li {if $sAction=='profile' && $aParams[0]=='wall'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}wall/">{$aLang.user_menu_profile_wall}{if ($iCountWallUser)>0} ({$iCountWallUser}){/if}</a></li>
        <li {if $sAction=='profile' && $aParams[0]=='favourites'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}favourites/topics/">{$aLang.user_menu_profile_favourites}{if ($iCountFavourite)>0} ({$iCountFavourite}){/if}</a></li>
        <li {if $sAction=='profile' && $aParams[0]=='friends'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}friends/">{$aLang.user_menu_profile_friends}{if ($iCountFriendsUser)>0} ({$iCountFriendsUser}){/if}</a></li>
        <li {if $sAction=='profile' && $aParams[0]=='stream'}class="active"{/if}><a href="{$oUserProfile->getUserWebPath()}stream/">{$aLang.user_menu_profile_stream}</a></li>
    </ul>

    <div class="modal modal-foto" id="foto-resize">
        <header class="modal-header">
            <h3>{$aLang.uploadimg}</h3>
        </header>

        <div class="modal-content">
            <img src="" alt="" id="foto-resize-original-img"><br />
            <button type="submit" class="button button-primary" onclick="return ls.user.resizeFoto();">{$aLang.settings_profile_avatar_resize_apply}</button>
            <button type="submit" class="button" onclick="return ls.user.cancelFoto();">{$aLang.settings_profile_avatar_resize_cancel}</button>
        </div>
    </div>
</div>