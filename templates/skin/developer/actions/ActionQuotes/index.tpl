{include file='header.tpl'}

<h2 class="page-header">{$aLang.quotes_header}</h2>

<div class="modal modal-write" id="quotes_form">
    <header class="modal-header">
        <h3>{$aLang.quotes_title_add}</h3>
        <a href="#" class="close jqmClose"></a>
    </header>

    <div class="comment-preview text" id="quotes_preview"></div>

    <form class="modal-content">
        <div>
            <label for="quotes_form_data">{$aLang.quotes_data}:</label>
            {include file='editor.tpl'}
            <textarea id="quotes_form_data" class="mce-editor markitup-editor input-width-full markItUpEditor"></textarea>

            <input type="hidden" id="quotes_form_action" />
            <input type="hidden" id="quotes_form_id" />

            <button type="button" onclick="ls.quotes.applyForm(); return false;" class="button button-primary">{$aLang.quotes_add}</button>
            <button type="button" onclick="ls.quotes.quotesPreview(); return false;" class="button">{$aLang.quotes_prev}</button>
        </div>
    </form>
</div>

<h4 class="table-header" style="font-size: 18px; float: left;">
    Всего цитат: <i id="quotes_count">{$iCountQuotes}</i>
    <p><a href="#" onclick="ls.quotes.showAddForm(); return false;" class="link-dotted" id="quotes_form_show">{$aLang.quotes_add}</a></p>
</h4>


<div style="float: right;">
    {include file='paging.tpl' aPaging=$aPaging}
</div>

<table class="table table-hover">
    <thead>
    <tr>
        <th class="col-sm-11">Цитата</th>
        <th class="col-sm-1" style="text-align: center">Опции</th>
    </tr>
    </thead>

    <tbody id="quotes_list">
    {foreach from=$aQuotes item=aQuote}
    <tr id="field_{$aQuote['id']}" class="quote_element">
        <td class="quotes_data">{$aQuote['data']}</td>
        <td>
            <div class="quotes-actions">
                <a href="#" onclick="ls.quotes.showEditForm({$aQuote['id']}); return false;" title="{$aLang.quotes_update}"><i class="fa fa-pencil" style="float:left;" aria-hidden="true"></i></a>
                <a href="#" onclick="ls.quotes.deleteQuotes({$aQuote['id']}); return false;" title="{$aLang.quotes_delete}"><i class="fa fa-trash" style="float:right;" aria-hidden="true"></i></a>
            </div>
        </td>
    </tr>
    {/foreach}
    </tbody>
</table>

<script>
	if (location.hash.startsWith('#field_')) {
		setTimeout(ls.quotes.scrollToQuote(location.hash.replace('#field_', '')), 2000);
	}
</script>

<h4 class="table-header" style="font-size: 18px; float: left;">
    <p><a href="#" onclick="ls.quotes.showAddForm(); return false;" class="link-dotted" id="quotes_form_show">{$aLang.quotes_add}</a></p>
</h4>
<div style="float: right;">
    {include file='paging.tpl' aPaging=$aPaging}
</div>

{include file='footer.tpl'}