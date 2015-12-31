{if $oUserCurrent->isAdministrator()}
    <a href="#" onclick="forbidIgnoreUser({$oUserProfile->getId()}, this); return false;">{if $bForbidIgnore}{$aLang.plugin.ignore.allow_ignore_user}{else}{$aLang.forbid_ignore_user}{/if}</a><br/>
{/if}

{if $oUserCurrent->getId() != $oUserProfile->getId()}
    {if !$bForbidIgnore}
        <a href="#" onclick="ignoreUser({$oUserProfile->getId()}, 'topics',this); return false;">{if $bIgnoredTopics}{$aLang.plugin.ignore.disignore_user_topics}{else}{$aLang.plugin.ignore.ignore_user_topics}{/if}</a><br/>
        <a href="#" onclick="ignoreUser({$oUserProfile->getId()}, 'comments',this); return false;">{if $bIgnoredComments}{$aLang.plugin.ignore.disignore_user_comments}{else}{$aLang.plugin.ignore.ignore_user_comments}{/if}</a><br/>
    {/if}
    <a href="#" onclick="ignoreTalkUser('{$oUserProfile->getLogin()}', {$oUserProfile->getId()},this); return false;">{if $bIgnoredTalks}{$aLang.plugin.ignore.disignore_user_talks}{else}{$aLang.plugin.ignore.ignore_user_talks}{/if}</a><br/>

{/if}

<script>
    var bIgnoreState = {$bIgnoredTalks};
    {literal}
    function forbidIgnoreUser(idUser, a) {
        ls.ajax(aRouter['ajax']+'forbid-ignore', {idUser: idUser}, function(result){
            if (!result) {
                ls.msg.error('Error','Please try again later');
            }
            if (result.bStateError) {
                ls.msg.error(result.sMsgTitle,result.sMsg);
            } else {
                jQuery(a).html(result.sText);
                ls.msg.notice(result.sMsgTitle,result.sMsg);
            }
        });
    }
    function ignoreUser(idUser, type, a) {
        ls.ajax(aRouter['ajax']+'ignore', {idUser: idUser, type:type}, function(result){
            if (!result) {
                ls.msg.error('Error','Please try again later');
            }
            if (result.bStateError) {
                ls.msg.error(result.sMsgTitle,result.sMsg);
            } else {
                jQuery(a).html(result.sText);
                ls.msg.notice(result.sMsgTitle,result.sMsg);
            }
        });
    }
    function ignoreTalkUser(loginUser, idUser, a) {
        var error = false;
        if (bIgnoreState) {
            ls.ajax(aRouter['talk']+'ajaxdeletefromblacklist/', {idTarget: idUser}, function(result) {
                if (!result) {
                    ls.msg.error('Error','Please try again later');
                    var error = true;
                }
                if (result.bStateError) {
                    ls.msg.error(null, result.sMsg);
                    var error = true;
                }
                    
                if (!error) {
                    jQuery(a).html(ls.lang.get('plugin.ignore.ignore_user_talks'));
                    ls.msg.notice(null,ls.lang.get('plugin.ignore.disignore_user_ok_talk'));
                    bIgnoreState = 0;
                }
            });
        } else {
            ls.ajax(aRouter['talk']+'ajaxaddtoblacklist/', {users: loginUser}, function(result) {
                if (result.bStateError) {
                    ls.msg.error(null, result.sMsg);
                    error = true;
                } else {
                    $.each(result.aUsers, function(index, item) {
                        if(item.bStateError){
                            ls.msg.notice(null, item.sMsg);
                            error = true;
                        }
                    });
                }

                if (!error) {
                    jQuery(a).html(ls.lang.get('plugin.ignore.disignore_user_talks'));
                    ls.msg.notice(null,ls.lang.get('plugin.ignore.ignore_user_ok_talk'));
                    bIgnoreState = 1;
                }
            });
        }
                
    }
</script>
{/literal}
