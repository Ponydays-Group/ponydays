{include file='header.tpl'}
<div id="list_wrapper">
<h2 class="page-header">{$aLang.quotes_trash}</h2>

<h4 class="table-header" style="font-size: 18px; float: left;">
    Цитат в корзине: <i id="quotes_count">{$iCountQuotes}</i>
    <p>
        <a href="{cfg name="path.root.web"}/quotes/" class="link-dotted" id="quotes_form_show">{$aLang.quotes_header}</a>
    </p>
</h4>

<br />
<br />

{if $aQuotes != []}
<div class="table-wrapper" style="overflow-x: unset;">
<table class="table table-hover">
    <thead>
    <tr>
        <th class="col-sm-11">Цитата</th>
        <th class="col-sm-1" style="text-align: right">Опции</th>
    </tr>
    </thead>

    <tbody id="quotes_list">
    {foreach from=$aQuotes key=iKey item=sData}
        {include file='quote.tpl' bRestore=true}
    {/foreach}
    </tbody>
</table>
</div>
{else}
    <p>
        <br /><br /><br />
        <h4 class="table-header" style="font-size: 18px; clear: both">Удалённых цитат нет</h4>
    </p>
{/if}

<script>
	if (location.hash.startsWith('#field_')) {
		setTimeout(ls.quotes.scrollToQuote(location.hash.replace('#field_', '')), 2000);
	}
</script>
</div>
{include file='footer.tpl'}