{include file='header.tpl'}

<link rel="stylesheet" type="text/css" href="{$aTemplateWebPathpage|cat:'css/style.css'}" media="all" />


<div>
	<h2 class="page-header">{$aLang.page.admin}</h2>
	
	
	{if $aParams.0=='new'}
		<h3 class="page-sub-header">{$aLang.page.create}</h3>
		{include file=$aTemplatePathpage|cat:'actions/ActionPage/add.tpl'}
	{elseif $aParams.0=='edit'}
		<h3 class="page-sub-header">{$aLang.page.edit} «{$oPageEdit->getTitle()}»</h3>
		{include file=$aTemplatePathpage|cat:'actions/ActionPage/add.tpl'}
	{else}
		<a href="{router page='page'}admin/new/" class="page-new">{$aLang.page.new}</a><br /><br />
	{/if}


	<table cellspacing="0" class="table">
		<thead>
			<tr>
				<th width="180px">{$aLang.page.admin_title}</th>
				<th align="center" >{$aLang.page.admin_url}</th>    	
				<th align="center" width="50px">{$aLang.page.admin_active}</th>    	   	
				<th align="center" width="70px">{$aLang.page.admin_main}</th>    	   	
				<th align="center" width="80px">{$aLang.page.admin_action}</th>
			</tr>
		</thead>
		
		<tbody>
			{foreach from=$aPages item=oPage name=el2} 	
				<tr>
					<td>
						<i class="fa fa-file-text-o" title="" border="0" style="margin-left: {$oPage->getLevel()*20}px;"></i>
						<a href="{router page='page'}{$oPage->getUrlFull()}/">{$oPage->getTitle()}</a>
					</td>
					<td>
						/{$oPage->getUrlFull()}/
					</td>   
					<td align="center">
						{if $oPage->getActive()}
							{$aLang.page.admin_active_yes}
						{else}
							{$aLang.page.admin_active_no}
						{/if}
					</td>
					<td align="center">
						{if $oPage->getMain()}
							{$aLang.page.admin_active_yes}
						{else}
							{$aLang.page.admin_active_no}
						{/if}
					</td>
					<td align="center">  
						<a href="{router page='page'}admin/edit/{$oPage->getId()}/"><i class="fa fa-edit" alt="{$aLang.page.admin_action_edit}" title="{$aLang.page.admin_action_edit}"></i></a>
						<a href="{router page='page'}admin/delete/{$oPage->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" onclick="return confirm('«{$oPage->getTitle()}»: {$aLang.page.admin_action_delete_confirm}');"><i class="fa fa-trash" title="{$aLang.page.admin_action_delete}"></i></a>
						<a href="{router page='page'}admin/sort/{$oPage->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}"><i class="fa fa-arrow-up" title="{$aLang.page.admin_sort_up} ({$oPage->getSort()})"></i></a>
						<a href="{router page='page'}admin/sort/{$oPage->getId()}/down/?security_ls_key={$LIVESTREET_SECURITY_KEY}"><i class="fa fa-arrow-down" title="{$aLang.page.admin_sort_down} ({$oPage->getSort()})"></i></a>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>


{include file='footer.tpl'}