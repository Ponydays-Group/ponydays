{foreach from=$aTalks item=oTalk}
    {assign var="oTalkUserAuthor" value=$oTalk->getTalkUser()}
    <div class="tb-row">
        {foreach from=$oTalk->getTalkUsers() item=oTalkUser name=users}
            {if $oTalkUser->getUserId()!=$oUserCurrent->getId()}
                {assign var="oUser" value=$oTalkUser->getUser()}
                <a href="{$oUser->getUserWebPath()}" class="author"><img width="24" height="24" src="{$oUser->getProfileAvatarPath(24)}" class="avatar" alt="" /></a>
                <a href="{$oUser->getUserWebPath()}" class="login">{$oUser->getLogin()}</a><br />
            {/if}
        {/foreach}
        <a href="{router page='talk'}read/{$oTalk->getId()}/">{$oTalk->getTitle()|escape:'html'}</a>
        &nbsp;	
        {if $oTalk->getCountComment()}
                {$oTalk->getCountComment()} {if $oTalkUserAuthor->getCommentCountNew()}<span style="color: #008000;">+{$oTalkUserAuthor->getCommentCountNew()}</span>{/if}
        {/if}                        
    </div>
{/foreach}
