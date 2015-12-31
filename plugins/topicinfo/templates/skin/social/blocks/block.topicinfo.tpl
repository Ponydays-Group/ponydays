
  <!-- Topicinfo plugin -->
  {if $oTopic}
      <div class="block Topicinfo">
          <header class="block-header sep">
              <h3>{$aLang.plugin.topicinfo.Block_Title}</h3>
          </header>

          <div class="block-content">
              {assign var="oTopicUser" value=$oTopic->getUser()}

              <div class="MoreInfo">
                  <div class="AvatarHolder">
                      <a href="{$oTopicUser->getUserWebPath()}" class="avatar"><img src="{$oTopicUser->getProfileAvatarPath(64)}" alt="avatar" itemprop="photo" /></a>
                      <div class="Status {if $oTopicUser->isOnline()}online{else}offline{/if}" title="{if $oTopicUser->isOnline()}{$aLang.user_status_online}{else}{$aLang.user_status_offline}{/if}"></div>
                  </div>

                  <h2 class="user-login"><a href="{$oTopicUser->getUserWebPath()}">{$oTopicUser->getLogin()}</a></h2>
                  <h2 class="user-name">{$oTopicUser->getProfileName()|escape:'html'}</h2>

                  <div class="user-rating">
                      {$aLang.user_rating}: <strong>{if $oTopicUser->getRating() > 0}+{/if}{$oTopicUser->getRating()}</strong>
                  </div>

                  {* 
                  <div class="user-rating">
                       {$aLang.user_skill}: <strong>{$oTopicUser->getSkill()}</strong>
                  </div>
                  *}
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


              {assign var="aUsersTopic" value=$LS->Topic_GetTopicsPersonalByUser($oTopicUser->getId(),1,1,$oConfig->GetValue("plugin.topicinfo.Topics_Count"))}
              {if $aUsersTopic}
                  <h2 class="header-table"><a href="{$oTopicUser->getUserWebPath()}created/topics/">{$aLang.plugin.topicinfo.User_Topics}</a></h2>
                  
                  <ul class="TopicList">
                      {foreach from=$aUsersTopic.collection item=oCurTopic}
                          {if $oTopic->getId()!=$oCurTopic->getId()}
                              <li>
                                  {*{if $oCurTopic->getType()=='photoset'}
                                      {assign var=oMainPhoto value=$oCurTopic->getPhotosetMainPhoto()}
                                      
                                      {if $oMainPhoto}
                                          <img src="{$oMainPhoto->getWebPath(500)}" alt="image" />
                                      {/if}
                                  {/if}*}

                                  <a href="{$oCurTopic->getUrl()}">{$oCurTopic->getTitle()}</a>
                              </li>
                          {/if}
                      {/foreach}
                  </ul>
              {/if}
          </div>


          <footer class="block-footer">
              <a class="sponsor" href="http://mf7.me/">Sponsored by mf7.me</a>
              {* if you want to delete this link - please - donate to author at http://livestreetcms.com/profile/PSNet/donate/ *}
          </footer>
      </div>
  {/if}
  <!-- /Topicinfo plugin -->
