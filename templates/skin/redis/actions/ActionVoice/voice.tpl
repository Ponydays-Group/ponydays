{include file='header.tpl'}
<div id="voice">
    <div id="voice-users-wrapper">
        <div id="voice-users"></div>
        <div id="voice-actions">
            <span id="webrtc-status" class="inactive"></span>
            {*<a href="#" id="webrtc-join" style="margin-right: 30px;">*}
                {*<i class="material-icons">person_add</i>*}
            {*</a>*}
            {*<a href="#" id="webrtc-leave" style="display: none; margin-right: 30px;">*}
                {*<i class="material-icons">exit_to_app</i>*}
            </a>
            <a href="#" id="webrtc-disable-micro" style="display: none;">
                <i class="material-icons">mic</i>
            </a>
            <a href="#" id="webrtc-enable-micro" style="">
                <i class="material-icons">mic_off</i>
            </a>
            <a href="#" id="webrtc-disable-video" style="display: none;">
                <i class="material-icons">videocam</i>
            </a>
            <a href="#" id="webrtc-enable-video" style="">
                <i class="material-icons">videocam_off</i>
            </a>
            <a href="#" id="webrtc-mute-all" style="">
                <i class="material-icons">headset</i>
            </a>
            <a href="#" id="webrtc-unmute-all" style="display: none;">
                <i class="material-icons">headset_off</i>
            </a>
        </div>
    </div>
    <div id="voice-video">
        <video autoplay="true" id="localVideo"></video>
        <div id="remotesVideos"></div>
    </div>
</div>
{include file='footer.tpl'}