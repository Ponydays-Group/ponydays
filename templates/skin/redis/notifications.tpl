{if $aNotifications}

{foreach from=$aNotifications item="oNotification"}
	
	<li class="stream-item">
		<h3>{$oNotification->getTitle()}<span>
				{if $oNotification->getRating() > 0}
					<span class="vote vote-count-positive">
						<span class="vote-count">+{$oNotification->getRating()}</span>
					</span>
				{elseif $oNotification->getRating() < 0}
					<span class="vote vote-count-negative">
						<span class="vote-count">{$oNotification->getRating()}</span>
					</span>
				{/if}
			</span>
		</h3>
		<p>{$oNotification->getText()}</p>
		{if $oNotification->getLink()}
			<a id="notice-id-{$oNotification->getId()}" class="n-box n-notice" href="{$oNotification->getLink()}" style="display: inline;">
				Ссылка
			</a>
		{/if}
	</li>

{/foreach}

{if $oNotification}
	<div style="clear:both; height: 20px;"></div>

	<div class="show-more-box">
		<div class="show-more loading">
			<a class="show-more-button" id="LoadMoreButton" href="#" onclick="ls.ajax.LoadMoreNotifications('{$iPage}'); return false;">{$aLang.feedbacks.moreee}</a>
		</div>
	</div>
{/if}

{else}
	<div class="alert alert-info">
		{$aLang.plugin.feedbacks.noactiviti}
	</div>
{/if}