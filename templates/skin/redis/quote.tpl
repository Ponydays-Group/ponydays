<tr id="field_{$iKey}" class="quote_element">
    <td class="quotes_data">{$sData}</td>
    <td>
        <div class="quotes-actions">
            {if $bEdit or $bHash}
                {if $bDelete or $bRestore}
                <span style="float: left">
                {/if}
                    {if $bEdit}
                        <a href="#" onclick="ls.quotes.showEditForm({$iKey}); return false;" title="{$aLang.quotes_update}">
                            <i class="fa fa-pencil" style="float:left;" aria-hidden="true"></i>
                        </a>&nbsp;
                    {/if}
                    {if $bHash}
                        <a href="/quotes/{$iKey}" onclick="prompt('{$aLang.quotes_link}', '{cfg name='path.root.web'}/quotes/{$iKey}'); return false;" title="{$aLang.quotes_link}">
                            <i class="fa fa-hashtag" aria-hidden="true"></i>
                        </a>
                    {/if}
                {if $bDelete or $bRestore}
                </span>
                {/if}
            {/if}
            {if $bDelete or $bRestore}
                <span style="float: right;">
                    {if $bDelete}
                        <a href="#" onclick="ls.quotes.deleteQuotes({$iKey}); return false;" title="{$aLang.quotes_delete}">
                            <i class="fa fa-trash" style="float:right;" aria-hidden="true"></i>
                        </a>
                    {/if}
                    {if $bRestore}
                        <a href="#" onclick="ls.quotes.restoreQuotes({$iKey}); return false;" title="{$aLang.quotes_restore}">
                            <i class="fa fa-plus" style="float:right;" aria-hidden="true"></i>
                        </a>
                    {/if}
                </span>
            {/if}
        </div>
    </td>
</tr>