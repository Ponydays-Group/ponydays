{if $aNotifications}

{for $index = 0; $index < sizeof($aNotifications); $index++ }
	{$oNotification = $aNotifications[$index]}
	{$oUser = $aUsers[$index]}
	{$oUser->getAvatar()}
	<li class="stream-item">
		<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(48)}" alt="avatar" class="avatar" /></a>
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
	</li>

{/for}

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