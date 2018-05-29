{include file='header.tpl' menu='deleted'}
    <ul class="nav nav-pills">
        <li {if $sMenuSubItemSelect=='topics'}class="active"{/if}><a href="{router page='deleted'}topics/">{$aLang.topic_title}</a></li>
        <li {if $sMenuSubItemSelect=='blogs'}class="active"{/if}><a href="{router page='deleted'}blogs/">{$aLang.blogs}</a></li>
    </ul>
    {if count($aTopics)>0}
        {add_block group='toolbar' name='toolbar_topic.tpl' iCountTopic=count($aTopics)}
        {foreach from=$aTopics item=oTopic}
            {if $LS->Topic_IsAllowTopicType($oTopic->getType())}
                {assign var="sTopicTemplateName" value="topic_`$oTopic->getType()`.tpl"}
                {include file=$sTopicTemplateName bTopicList=true}
            {/if}
        {/foreach}

        {include file='paging.tpl' aPaging=$aPaging}
    {else}
        {$aLang.blog_no_topic}
{/if}{include file='footer.tpl'}

