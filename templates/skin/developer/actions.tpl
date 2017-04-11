{if $aActions}
{foreach from=$aActions item="oAction"}

	{assign 'oUser' $oAction->GetUserFrom()}

	<li class="stream-item">
		<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(48)}" alt="{$oUser->getLogin()}" class="avatar" /></a>

		<p class="text-muted info">
			{if {cfg name='view.user_name'} == 'true'}
				<a href="{$oUser->getUserWebPath()}">
					{if $oUser->getProfileName()}
						{$oUser->getProfileName()|escape:'html'}
					{else}
						{$oUser->getLogin()}
					{/if}
				</a>
			{else}
				<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
			{/if} ·
			<span class="date" title="{date_format date=$oAction->getAddDatetime()}">{date_format date=$oAction->getAddDatetime() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</span>
		</p>

		{if $oAction->getEventType() == 'TopicComment'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.ctop} {else} {$aLang.plugin.feedbacks.stop} {/if}
			<a href="{$oAction->getTarget()->getUrl()}">{$oAction->getTarget()->getTitle()|escape:'html'}</a>
			<div class="stream-comment-preview">{$oAction->getActionObject()->getText()|strip_tags|truncate:200}</div>
			<div class="doanswer"><a href="{$oAction->getTarget()->getUrl()}#comment{$oAction->getActionObject()->getId()}">{$aLang.plugin.feedbacks.doanswer}</a></div> <br />

		{elseif $oAction->getEventType() == 'TopicCommentTree'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.ctopt_m} {else} {$aLang.plugin.feedbacks.ctopt_f} {/if}
			<a href="{$oAction->getTarget()->getUrl()}">{$oAction->getTarget()->getTitle()|escape:'html'}</a>
			<div class="stream-comment-preview">{$oAction->getActionObject()->getText()|strip_tags|truncate:200}</div>
			<div class="doanswer"><a href="{$oAction->getTarget()->getUrl()}#comment{$oAction->getActionObject()->getId()}">{$aLang.plugin.feedbacks.doanswer}</a></div> <br />

		{elseif $oAction->getEventType() == 'QaReply'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.rq} {else} {$aLang.plugin.feedbacks.sq} {/if}
			<a href="{$oAction->getTarget()->getUrl()}">{$oAction->getTarget()->getTitle()|escape:'html'}</a>
			<div class="stream-comment-preview">{$oAction->getActionObject()->getText()|strip_tags|truncate:200}</div>

		{elseif $oAction->getEventType() == 'CommentReply'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.rcom} {else} {$aLang.plugin.feedbacks.scom} {/if}
			<a href="{$oAction->GetTargetCommentUrl()}">{$oAction->getTarget()->getText()|strip_tags|truncate:200}</a>
			<div class="stream-comment-preview">{$oAction->getActionObject()->getText()|strip_tags|truncate:200}</div>
			<div class="doanswer"><a href="{$oAction->getTarget()->getTarget()->getUrl()}#comment{$oAction->getActionObject()->getId()}">{$aLang.plugin.feedbacks.doanswer}</a></div><br />
			

		{elseif $oAction->getEventType() == 'VoteTopic'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.ltop} {else} {$aLang.plugin.feedbacks.lstop} {/if}
			<a href="{$oAction->getTarget()->getUrl()}">{$oAction->getTarget()->getTitle()|escape:'html'}</a>

		{elseif $oAction->getEventType() == 'VoteComment'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.lcom} {else} {$aLang.plugin.feedbacks.lscom} {/if}
			<a href="{$oAction->GetTargetCommentUrl()}">{$oAction->getTarget()->getText()|strip_tags|truncate:200}</a>

		{elseif $oAction->getEventType() == 'VoteDownTopic'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.dtop} {else} {$aLang.plugin.feedbacks.dstop} {/if}
			<a href="{$oAction->getTarget()->getUrl()}">{$oAction->getTarget()->getTitle()|escape:'html'}</a>		
		
		{elseif $oAction->getEventType() == 'VoteAbstainTopic'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.abs} {else} {$aLang.plugin.feedbacks.abss} {/if}
			<a href="{$oAction->getTarget()->getUrl()}">{$oAction->getTarget()->getTitle()|escape:'html'}</a>

		{elseif $oAction->getEventType() == 'VoteDownComment'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.dcom} {else} {$aLang.plugin.feedbacks.dscom} {/if}
			<a href="{$oAction->GetTargetCommentUrl()}">{$oAction->getTarget()->getText()|strip_tags|truncate:200}</a>

		{elseif $oAction->getEventType() == 'VoteDownUser'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.dprof} {else} {$aLang.plugin.feedbacks.dsprof} {/if}

		{elseif $oAction->getEventType() == 'VoteUser'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.plugin.feedbacks.lprof} {else} {$aLang.plugin.feedbacks.lsprof} {/if}


		{elseif $oAction->getEventType() == 'add_friend'}
			{if $oUser->getProfileSex() != 'woman'} {$aLang.stream_list_event_add_friend} {else} {$aLang.stream_list_event_add_friend_female} {/if}
			{if {cfg name='view.user_name'} == 'true'}
				<a href="{$oTarget->getUserWebPath()}">
					{if $oTarget->getProfileName()}
						{$oTarget->getProfileName()|escape:'html'}
					{else}
						{$oTarget->getLogin()}
					{/if}
				</a>
			{else}
				<a href="{$oTarget->getUserWebPath()}">{$oTarget->getLogin()}</a>
			{/if}

		{/if}
	</li>

{/foreach}

{if $oAction}
	<div style="clear:both; height: 20px;"></div>

	<div class="show-more-box">
		<div class="show-more loading">
			<a class="show-more-button" id="LoadMoreButton" href="javascript:void(0);" onclick="ls.ajax.LoadMoreActions('{$oAction->getId()}')">{$aLang.feedbacks.moreee}</a>
		</div>
	</div>
{/if}

{else}
	<div class="alert alert-info">
		{$aLang.plugin.feedbacks.noactiviti}
	</div>
{/if}