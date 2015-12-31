
    <!-- Topicinfo plugin -->
    {if $oTopic}
      <div class="block Topicinfo">
        <header class="block-header sep">
          <h3>{$aLang.plugin.topicinfo.Block_Title}</h3>
        </header>
        {assign var="oTopicUser" value=$oTopic->getUser()}
        <div class="AvatarHolder">
          <a href="{$oTopicUser->getUserWebPath()}" class="avatar"><img src="{$oTopicUser->getProfileAvatarPath(100)}" alt="avatar" itemprop="photo" /></a>
          <a href="{$oTopicUser->getUserWebPath()}" class="user">{$oTopicUser->getLogin()}</a>
          <div class="Status {if $oTopicUser->isOnline()}online{else}offline{/if}" title="{if $oTopicUser->isOnline()}{$aLang.user_status_online}{else}{$aLang.user_status_offline}{/if}"></div>
        </div>
        <div class="MoreInfo">
          <h2 class="header-table">
            {$oTopicUser->getProfileName()|escape:'html'}
          </h2>
          <div class="OneDescription">
            <p title="{$aLang.user_rating}">
              {$aLang.user_rating}: <b class="r">{if $oTopicUser->getRating() > 0}+{/if}{$oTopicUser->getRating()}</b>
            </p>
            <p title="{$aLang.user_skill}">
              {$aLang.user_skill}: <b class="s">{$oTopicUser->getSkill()}</b>
            </p>
          </div>
          {assign var="aUserFieldContactValues" value=$oTopicUser->getUserFieldValues(true,array('contact'))}
          {if $aUserFieldContactValues}
            <h2 class="header-table">{$aLang.profile_contacts}</h2>
            
            <ul class="profile-contact-list">
              {foreach from=$aUserFieldContactValues item=oField}
                <li><i class="icon-contact icon-contact-{$oField->getName()}" title="{$oField->getName()}"></i> {$oField->getValue(true,true)}</li>
              {/foreach}
            </ul>
          {/if}

          {assign var="aUserFieldContactValues" value=$oTopicUser->getUserFieldValues(true,array('social'))}
          {if $aUserFieldContactValues}
            <h2 class="header-table">{$aLang.profile_social}</h2>
            
            <ul class="profile-contact-list">
              {foreach from=$aUserFieldContactValues item=oField}
                <li><i class="icon-contact icon-contact-{$oField->getName()}" title="{$oField->getName()}"></i> {$oField->getValue(true,true)}</li>
              {/foreach}
            </ul>
          {/if}
        </div>
        
        <div class="TopicList">
          {assign var="aUsersTopic" value=$LS->Topic_GetTopicsPersonalByUser($oTopicUser->getId(),1,1,$oConfig->GetValue("plugin.topicinfo.Topics_Count"))}
          {if $aUsersTopic}
            <h2 class="header-table">{$aLang.plugin.topicinfo.User_Topics}</h2>
            <ul>
              {foreach from=$aUsersTopic.collection item=oCurTopic}
                {if $oTopic->getId()!=$oCurTopic->getId()}
                  <li>
                    {if $oCurTopic->getType()=='photoset'}
                      {assign var=oMainPhoto value=$oCurTopic->getPhotosetMainPhoto()}
                      {if $oMainPhoto}
                        <img src="{$oMainPhoto->getWebPath(500)}" alt="image" />
                      {/if}
                    {/if}
                    <a href="{$oCurTopic->getUrl()}">{$oCurTopic->getTitle()}</a>
                  </li>
                {/if}
              {/foreach}
            </ul>
          {/if}
        </div>
        <a class="sponsor" href="http://mf7.me/">Sponsored by mf7.me</a>
        {* if you want to delete this link - please - donate to author at http://livestreetcms.com/profile/PSNet/donate/ *}
      </div>
    {/if}
    <!-- /Topicinfo plugin -->
