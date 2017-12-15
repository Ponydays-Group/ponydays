{foreach from=$aComments item=oComment name=rublist}
    {assign var="cmtlevel" value=$oComment->getLevel()}

    {if $cmtlevel>$oConfig->GetValue('module.comment.max_tree')}
        {assign var="cmtlevel" value=$oConfig->GetValue('module.comment.max_tree')}
    {/if}

    {if $nesting < $cmtlevel}
    {elseif $nesting > $cmtlevel}
        {section name=closelist1  loop=$nesting-$cmtlevel+1}{*</div>*}{/section}
    {elseif not $smarty.foreach.rublist.first}
        {*</div>*}
    {/if}

{*<div class="comment-wrapper" id="comment_wrapper_id_{$oComment->getId()}">*}

    {include file='comment.tpl'}
    {assign var="nesting" value=$cmtlevel}
    {if $smarty.foreach.rublist.last}
        {section name=closelist2 loop=$nesting+1}</div>{/section}
    {/if}
{/foreach}