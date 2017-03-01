{assign var="noSidebar" value=true}
{include file='header.tpl' menu="blogs"}

<div class="page-header">
	<h1>{$aLang.plugin.feedbacks.header}</h1>
</div>

{include file='menu.stream.tpl'}

<div id="ActionsContainer">
	<ul class="list-unstyled stream-list" id="stream-list">
		{include file="$sTemplatePath/actions.tpl"}
	</ul>

</div>

{include file='footer.tpl'}