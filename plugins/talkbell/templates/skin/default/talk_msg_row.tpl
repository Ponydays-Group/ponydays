{if $sCountTalk}
    {$aLang.plugin.talkbell.new_talks} (<b><href="{router page='talk'}">{$sCountTalk}</a></b>)
{else}
    <img src="{$oUser->getProfileAvatarPath(48)}" style="margin: 5px; float: left;" />
    {$oUser->getLogin()} {$aLang.plugin.talkbell.new_talk} <a href="{router page='talk'}read/{$oTalk->getId()}/">{$aLang.plugin.talkbell.talk}</a>.
    <br />{$aLang.plugin.talkbell.talk_title} <a href="{router page='talk'}read/{$oTalk->getId()}/">{$oTalk->getTitle()}</a>.
{/if}
&nbsp;
{if $oTalk->getCountComment()}
        {$oTalk->getCountComment()} {if $oTalkUserAuthor->getCommentCountNew()}<span style="color: #008000;">+{$oTalkUserAuthor->getCommentCountNew()}</span>{/if}
{/if}
<div style="clear: both;"></div>
