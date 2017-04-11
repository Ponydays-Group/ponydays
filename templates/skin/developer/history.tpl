	{foreach from=$aHistory item=oHistoryItem}
	    <section class="editcomment_history_item">
		<header>
		    {date_format|lower date=$oHistoryItem->getDateAdd() format="j F Y, H:i"}
		</header>
		<div class="body">
		    {$oHistoryItem->getText()}
		</div>
	    </section>
	{/foreach}
