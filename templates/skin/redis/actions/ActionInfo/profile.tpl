<div id="info_block_user">
    <div id="user_background" style="background: url({$oUserProfile->getProfileFoto()});">
        <img src="{$oUserProfile->getProfileAvatar(100)}" class="avatar {if $oUserProfile->isOnline()}online{/if}" />
        <div id="user_background_grad">{$oUserProfile->getLogin()}</div>
    </div>
    <ul class="profile-tabs">
        <li><a href="#">Комментариев: {$iCountCommentUser}</a></li>
        <li><a href="#">Постов: {$iCountTopicUser}</a></li>
    </ul>
    <div id="user_info">
        {$oUserProfile->getProfileAbout()}
    </div>
</div>