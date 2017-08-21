{include file='header.tpl'}

<h2 class="page-header">{$aLang.quotes_trash}</h2>

<h4 class="table-header" style="font-size: 18px; float: left;">
    <a href="{cfg name="path.root.web"}/quotes/" class="link-dotted" id="quotes_form_show">{$aLang.quotes_header}</a>
</h4>

{if $aQuotes != []}
<table class="table table-hover">
    <thead>
    <tr>
        <th class="col-sm-11">Цитата</th>
        <th class="col-sm-1" style="text-align: right">Опции</th>
    </tr>
    </thead>

    <tbody id="quotes_list">
    {foreach from=$aQuotes key=iKey item=sData}
    <tr id="field_{$iKey}" class="quote_element">
        <td class="quotes_data">{$sData}</td>
        <td>
            <div class="quotes-actions">
                <a href="#" onclick="ls.quotes.restoreQuotes({$iKey}); return false;" title="{$aLang.quotes_restore}"><i class="fa fa-plus" style="float:right;" aria-hidden="true"></i></a>
            </div>
        </td>
    </tr>
    {/foreach}
    </tbody>
</table>
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
{include file='footer.tpl'}