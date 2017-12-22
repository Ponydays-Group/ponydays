{include file='header.tpl'}
{foreach from=$aConfig item=aParam key=sParamName name="aConfig"}
    {assign var="val" value=$oConfig->GetValue($sParamName)}
    {if $aParam['type']=="bool"}
        <div class="config-param" data-name="{$sParamName}" data-val="{if $val}1{else}0{/if}">
            <span class="checkbox"><span>
                <input type="checkbox" name="{$sParamName}" id="{$sParamName}" {if $val}checked{/if}>
                <label for="{$sParamName}">{$aParam['description']}</label>
            </span></span>
        </div>
    {elseif $aParam['type']=="string"}
        <div class="config-param" data-name="{$sParamName}" data-val="{$val}">
            <label for="{$sParamName}">{$aParam['description']}</label>
            <input type="text" name="{$sParamName}" id="{$sParamName}" value="{$val}" />
        </div>
    {elseif $aParam['type']=="list"}
        <div class="config-param" data-separator=", " data-name="{$sParamName}" data-val="{implode(", ",$val)}">
            <label for="{$sParamName}">{$aParam['description']}</label>
            <input type="text" name="{$sParamName}" id="{$sParamName}" value="{implode(", ",$val)}" />
        </div>
    {elseif $aParam['type']=="separator"}
        <div class="config_separator" {if $smarty.foreach.aConfig.first}style="margin-top: 0px;"{/if}>{$aParam['description']}:</div>
    {/if}
{/foreach}
    <button class="button button-primary" style="margin-top: 25px;" onclick="ls.ajax.saveConfig()" type="submit">Сохранить</button>
{include file='footer.tpl'}