{extends file='index.tpl'}

{block name="content"}
<div id=content>

    {if !$submit_cache_save}

    <form method="post" action="">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}"/>

        <h3>{$oLang->_adm_menu_reset_cache}</h3>

		<div class="modal-2 offset1">
			<div class="checkbox inline" style="padding: 0;">
				<input type="checkbox" id="adm_cache_clear_data" name="adm_cache_clear_data" checked>
				<label>{$oLang->_adm_cache_clear_data}</label>
			</div>
		</div>

		<div class="modal-2 offset1">
			<div class="checkbox inline" style="padding: 0;">
				<input type="checkbox" id="adm_cache_clear_headfiles" name="adm_cache_clear_headfiles" checked>
				<label>{$oLang->_adm_cache_clear_headfiles}</label>
			</div>
		</div>

		<div class="modal-2 offset1">
			<div class="checkbox inline" style="padding: 0;">
				<input type="checkbox" id="adm_cache_clear_smarty" name="adm_cache_clear_smarty" checked>
				<label>{$oLang->_adm_cache_clear_smarty}</label>
			</div>
		</div>		

        <h3>{$oLang->_adm_menu_reset_config}</h3>

		<div class="modal-2 offset1">
			<div class="checkbox inline" style="padding: 0;">
				<input type="checkbox" id="adm_reset_config_data" name="adm_reset_config_data">
				<label>{$oLang->_adm_reset_config_data}</label>
			</div>
			<br>(Осторожно! Сброс всех данных!)
		</div>		

        <input type="submit" name="adm_reset_submit" value="{$oLang->_adm_execute}" class="btn btn-primary pull-right"/>&nbsp;
    </form>

    {else}

    <form method="post" action="">
        <input type="submit" name="admin_continue" value="{$oLang->_adm_continue}"/>
    </form>

    {/if}
</div>
{/block}