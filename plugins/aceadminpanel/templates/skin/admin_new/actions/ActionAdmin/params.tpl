{extends file='index.tpl'}

{block name="content"}

<div id="content">
<h3>{$oLang->adm_params_title}</h3>
<div class=topic>

    <form action="" method="POST">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <p>
            <label for="param_reserved_urls">{$oLang->adm_page_options_urls}:</label>
            <input type="text" id="param_reserved_urls" name="param_reserved_urls" value="{$sParamPageUrlReserved}"  class="w100p" /><br />
            <span class="help-block">{$oLang->adm_page_options_urls_notice}</span>
        </p>

        <p>
            <label for="param_items_per_page">{$oLang->adm_param_items_per_page}:</label>
            <input type="text" id="param_items_per_page" name="param_items_per_page" value="{$sParamItemsPerPage}"  class="w50" /><br />
            <span class="help-block">{$oLang->adm_param_items_per_page_notice}</span>
        </p>

        <p>
            <label for="param_vote_value">{$oLang->adm_param_vote_value}:</label>
            <input type="text" id="param_vote_value" name="param_vote_value" value="{$nParamVoteValue}"  class="w50" /><br />
            <span class="help-block">{$oLang->adm_param_vote_value_notice}</span>
        </p>

				<div class="modal-2">
					<div class="checkbox inline" style="padding: 0;">
						<input type="checkbox" id="param_check_password" name="param_check_password" value="1" {if ($bParamCheckPassword)}checked{/if}>
						<label>{$oLang->adm_param_check_password}</label>
					</div>
				</div>            
            <span class="help-block">{$oLang->adm_param_check_password_notice}</span>

        <input type="submit" name="submit_options_save" value="{$oLang->adm_save}" class="btn btn-primary pull-right" />&nbsp;

    </form>

</div>
</div>
{/block}