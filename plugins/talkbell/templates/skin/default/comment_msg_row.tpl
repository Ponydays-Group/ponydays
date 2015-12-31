{if $sCountComment}
    {$aLang.plugin.talkbell.new_comments} (<b><href="{router page='talk'}">{$sCountComment}</a></b>)
{else}
    {$aLang.plugin.talkbell.new_comment} <a href="{router page='talk'}read/{$oComment->getId()}/">{$oComment->getTitle()}</a>
{/if}
