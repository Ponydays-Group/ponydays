{include file='header.tpl'}
<div id="list_wrapper">
    <h2 class="page-header">{$aLang.quotes_header}</h2>

    {if $bIsAdmin}
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
                    <textarea id="quotes_form_data"
                              class="mce-editor markitup-editor input-width-full markItUpEditor"></textarea>

                    <input type="hidden" id="quotes_form_action"/>
                    <input type="hidden" id="quotes_form_id"/>

                    <button type="button" onclick="ls.quotes.applyForm(); return false;"
                            class="button button-primary">{$aLang.quotes_add}</button>
                    <button type="button" onclick="ls.quotes.quotesPreview(); return false;"
                            class="button">{$aLang.quotes_prev}</button>
                </div>
            </form>
        </div>
    {/if}

    <h4 class="table-header" style="font-size: 18px; float: left;">
        Всего цитат: <i id="quotes_count">{$iCountQuotes}</i>
        {if $bIsAdmin}
            <p><a href="#" onclick="ls.quotes.showAddForm(); return false;" class="link-dotted"
                  id="quotes_form_show">{$aLang.quotes_add}</a>&nbsp;
                <a href="{cfg name="path.root.web"}/quotes/deleted/" class="link-dotted"
                   id="quotes_form_show">{$aLang.quotes_trash}</a></p>
        {/if}
    </h4>


    {*<div style="float: right;">*}
    {*{include file='paging.tpl' aPaging=$aPaging}*}
    {*</div>*}
    <div class="table-wrapper">
        <table class="table table-hover">
            <thead>
            <tr>
                <th class="col-sm-11">Цитата</th>
                <th class="col-sm-1" {if $bIsAdmin} style="text-align: center" {else} style="text-align: right" {/if}>
                    Опции
                </th>
            </tr>
            </thead>

            <tbody id="quotes_list">
            {foreach from=$aQuotes key=iKey item=sData}
                <tr id="field_{$iKey}" class="quote_element">
                    <td class="quotes_data">{$sData}</td>
                    <td>
                        <div class="quotes-actions">
                            {if $bIsAdmin}
                                <span style="float: left">
                        <a href="#" onclick="ls.quotes.showEditForm({$iKey}); return false;"
                           title="{$aLang.quotes_update}"><i class="fa fa-pencil" style="float:left;"
                                                             aria-hidden="true"></i></a>
                        &nbsp;
                        <a href="#"
                           onclick="prompt('{$aLang.quotes_link}', '{cfg name='path.root.web'}/quotes/{$iKey}'); return false;"
                           title="{$aLang.quotes_link}"><i class="fa fa-hashtag" aria-hidden="true"></i></a>
                    </span>
                                <a href="#" onclick="ls.quotes.deleteQuotes({$iKey}); return false;"
                                   title="{$aLang.quotes_delete}"><i class="fa fa-trash" style="float:right;"
                                                                     aria-hidden="true"></i></a>
                            {else}
                                <a href="#"
                                   onclick="prompt('{$aLang.quotes_link}', '{cfg name='path.root.web'}/quotes/{$iKey}'); return false;"
                                   title="{$aLang.quotes_link}"><i class="fa fa-hashtag" style="float:right;"
                                                                   aria-hidden="true"></i></a>
                            {/if}
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <script>
        if (location.hash.startsWith('#field_')) {
            setTimeout(ls.quotes.scrollToQuote(location.hash.replace('#field_', '')), 2000);
        }
    </script>

    {if $bIsAdmin}
        <h4 class="table-header" style="font-size: 18px; float: left; margin-top: 20px;">
            <p><a href="#" onclick="ls.quotes.showAddForm(); return false;" class="link-dotted"
                  id="quotes_form_show">{$aLang.quotes_add}</a></p>
        </h4>
    {/if}

    <div style="float: right;">
        {include file='paging.tpl' aPaging=$aPaging}
    </div>
</div>
{include file='footer.tpl'}