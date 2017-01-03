{assign var="noSidebar" value=true}
{include file='header.tpl' menu='stream'}


<div class="page-header">
	<h1>{$aLang.plugin.feedbacks.header}</h1>
</div>



<div id="ActionsContainer">
	<ul class="list-unstyled stream-list" id="stream-list">
		{include file="$sTemplatePath/actions.tpl"}
	</ul>

</div>

{include file='footer.tpl'}