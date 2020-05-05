{if $E->make(App\Modules\Topic\ModuleTopic::class)->IsAllowTopicType($oTopic->getType())}
    {assign var="sTopicTemplateName" value="topic_`$oTopic->getType()`.tpl"}
    {include file=$sTopicTemplateName}
{/if}