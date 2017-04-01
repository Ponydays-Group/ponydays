{assign var="oSession" value=$oUserProfile->getSession()}
{assign var="oVote" value=$oUserProfile->getVote()}
{assign var="oGeoTarget" value=$oUserProfile->getGeoTarget()}

<section class="block">
	<div class="profile-main">
		<div class="user-avatar-wrapper"><img class="user-avatar" src="{$oUserProfile->getProfileAvatarPath(100)}" /></div>
		<div class="user-info">
			<span class="user-login">{$oUserProfile->getLogin()}</span>
			<div class="user-rating">
				<div id="vote_area_user_{$oUserProfile->getId()}" class="vote {if $oUserProfile->getRating()>=0}vote-count-positive{else}vote-count-negative{/if} {if $oVote} voted {if $oVote->getDirection()>0}voted-up{elseif $oVote->getDirection()<0}voted-down{/if}{/if}">
		<a href="#" class="vote-up" onclick="return ls.vote.vote({$oUserProfile->getId()},this,1,'user');"><i class="fa fa-plus-square-o"></i></a>
		<div id="vote_total_user_{$oUserProfile->getId()}" class="vote-count count" title="{$aLang.user_vote_count}: {$oUserProfile->getCountVote()}">{if $oUserProfile->getRating() > 0}+{/if}{$oUserProfile->getRating()}</div>
		<a href="#" class="vote-down" onclick="return ls.vote.vote({$oUserProfile->getId()},this,-1,'user');"><i class="fa fa-minus-square-o"></i></a>
	</div>

	<div class="strength">
		<div class="count" id="user_skill_{$oUserProfile->getId()}">{$oUserProfile->getSkill()}</div>
	</div>
</div>
		</div>
	</div>
</section>
{if $oUserProfile->getProfileAbout()}
<section class="block">
	<header class="block-header">
		<h3>О себе</h3>
	</header>
	<div class="block-content colorful-links">
		{$oUserProfile->getProfileAbout()}
	</div>
</section>
{/if}

{if $oUserCurrent && $oUserCurrent->getId()!=$oUserProfile->getId()}
	<script type="text/javascript">
		jQuery(function($){
			ls.lang.load({lang_load name="profile_user_unfollow,profile_user_follow"});
		});
	</script>

	<section class="block block-type-profile-actions">
		<div class="block-content">
			<ul class="profile-actions" id="profile_actions">
				{include file='actions/ActionProfile/friend_item.tpl' oUserFriend=$oUserProfile->getUserFriend()}
				<li><a href="{router page='talk'}add/?talk_users={$oUserProfile->getLogin()}">{$aLang.user_write_prvmsg}</a></li>						
				<li>
					<a href="#" onclick="ls.user.followToggle(this, {$oUserProfile->getId()}); return false;" class="{if $oUserProfile->isFollow()}followed{/if}">
						{if $oUserProfile->isFollow()}{$aLang.profile_user_unfollow}{else}{$aLang.profile_user_follow}{/if}
					</a>
				</li>
				{hook run='profile_sidebar_show' oUserProfile=$oUserProfile}
			</ul>
		</div>
	</section>
{/if}	
<section class="block">
	<header class="block-header">
		<h3>Личное</h3>
	</header>
	<div class="block-content colorful-links">
		<table class="table table-profile-info">
			{if $oUserProfile->getProfileSex()!='other'}
				<tr>
					<td class="cell-label">{$aLang.profile_sex}:</td>
					<td>
						{if $oUserProfile->getProfileSex()=='man'}
							{$aLang.profile_sex_man}
						{else}
							{$aLang.profile_sex_woman}
						{/if}
					</td>
				</tr>
			{/if}


			{if $oUserProfile->getProfileBirthday()}
				<tr>
					<td class="cell-label">{$aLang.profile_birthday}:</td>
					<td>{date_format date=$oUserProfile->getProfileBirthday() format="j F Y"}</td>
				</tr>
			{/if}


			{if $oGeoTarget}
				<tr>
					<td class="cell-label">{$aLang.profile_place}:</td>
					<td itemprop="address" itemscope itemtype="http://data-vocabulary.org/Address">
						{if $oGeoTarget->getCountryId()}
							<a href="{router page='people'}country/{$oGeoTarget->getCountryId()}/" itemprop="country-name">{$oUserProfile->getProfileCountry()|escape:'html'}</a>{if $oGeoTarget->getCityId()},{/if}
						{/if}

						{if $oGeoTarget->getCityId()}
							<a href="{router page='people'}city/{$oGeoTarget->getCityId()}/" itemprop="locality">{$oUserProfile->getProfileCity()|escape:'html'}</a>
						{/if}
					</td>
				</tr>
			{/if}

			{if $aUserFieldValues}
				{foreach from=$aUserFieldValues item=oField}
					<tr>
						<td class="cell-label"><i class="icon-contact icon-contact-{$oField->getName()}"></i> {$oField->getTitle()|escape:'html'}:</td>
						<td>{$oField->getValue(true,true)}</td>
					</tr>
				{/foreach}
			{/if}

			{hook run='profile_whois_privat_item' oUserProfile=$oUserProfile}
		</table>
	</div>
</section>

{assign var="aUserFieldContactValues" value=$oUserProfile->getUserFieldValues(true,array('social'))}
{if $aUserFieldContactValues}
<section class="block">
	<header class="block-header">
		<h2>{$aLang.profile_contacts}</h2>
	</header>
	<div class="block-content colorful-links">
	<table class="table table-profile-info">
		{foreach from=$aUserFieldContactValues item=oField}
			<tr>
				<td class="cell-label"><i class="icon-contact icon-contact-{$oField->getName()}"></i> {$oField->getTitle()|escape:'html'}:</td>
				<td>{$oField->getValue(true,true)}</td>
			</tr>
		{/foreach}
	</table>
</div>
</section>
{/if}

<section class="block">
	<header class="block-header">
		<h3>{$aLang.profile_activity}</h3>
	</header>
	<div class="block-content colorful-links">
<table class="table table-profile-info">

	{if $oConfig->GetValue('general.reg.invite') and $oUserInviteFrom}
		<tr>
			<td class="cell-label">{$aLang.profile_invite_from}:</td>
			<td>
				<a href="{$oUserInviteFrom->getUserWebPath()}">{$oUserInviteFrom->getLogin()}</a>&nbsp;
			</td>
		</tr>
	{/if}


	{if $oConfig->GetValue('general.reg.invite') and $aUsersInvite}
		<tr>
			<td class="cell-label">{$aLang.profile_invite_to}:</td>
			<td>
				{foreach from=$aUsersInvite item=oUserInvite}
					<a href="{$oUserInvite->getUserWebPath()}">{$oUserInvite->getLogin()}</a>&nbsp;
				{/foreach}
			</td>
		</tr>
	{/if}


	{if $aBlogsOwner}
		<tr>
			<td class="cell-label">{$aLang.profile_blogs_self}:</td>
			<td>
				{foreach from=$aBlogsOwner item=oBlog name=blog_owner}
					<a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_owner.last}, {/if}
				{/foreach}
			</td>
		</tr>
	{/if}


	{if $aBlogAdministrators}
		<tr>
			<td class="cell-label">{$aLang.profile_blogs_administration}:</td>
			<td>
				{foreach from=$aBlogAdministrators item=oBlogUser name=blog_user}
					{assign var="oBlog" value=$oBlogUser->getBlog()}
					<a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
				{/foreach}
			</td>
		</tr>
	{/if}


	{if $aBlogModerators}
		<tr>
			<td class="cell-label">{$aLang.profile_blogs_moderation}:</td>
			<td>
				{foreach from=$aBlogModerators item=oBlogUser name=blog_user}
					{assign var="oBlog" value=$oBlogUser->getBlog()}
					<a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
				{/foreach}
			</td>
		</tr>
	{/if}


	{if $aBlogUsers}
		<tr>
			<td class="cell-label">{$aLang.profile_blogs_join}:</td>
			<td>
				{foreach from=$aBlogUsers item=oBlogUser name=blog_user}
					{assign var="oBlog" value=$oBlogUser->getBlog()}
					<a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
				{/foreach}
			</td>
		</tr>
	{/if}


	{hook run='profile_whois_activity_item' oUserProfile=$oUserProfile}


	<tr>
		<td class="cell-label">{$aLang.profile_date_registration}:</td>
		<td>{date_format date=$oUserProfile->getDateRegister()}</td>
	</tr>


	{if $oSession}
		<tr>
			<td class="cell-label">{$aLang.profile_date_last}:</td>
			<td>{date_format date=$oSession->getDateLast()}</td>
		</tr>
	{/if}
</table>
</div>
</section>
