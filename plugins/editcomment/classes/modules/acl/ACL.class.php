<?php
/*-------------------------------------------------------
 *
*   kEditComment.
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

class PluginEditcomment_ModuleACL extends PluginEditcomment_Inherit_ModuleACL
{
    // Ничего не проверять
    const ACL_EC_NO_CHECK=0;
    
    // Минисальная проверка на то, что пользователь является автором коммента 
    const ACL_EC_BASIC=1;
    
    // Проверка на максимальное количество редактирований
    const ACL_EC_CHECK_MAX_EDIT_COUNT=2;
    
    // Проверка на максимальное прошедшее время
    const ACL_EC_CHECK_MAX_EDIT_PERIOD=4;
    
    // Проверка на запрет редактирования комментов с ответами
    const ACL_EC_CHECK_DENY_WITH_ANSWERS=8;
    
    // Проверка на достаточный уровень рейтинга пользователя при редактировании
    const ACL_EC_CHECK_USER_RATING=16;

    function UserCanEditComment($oUser,$oComment,$iCheckMask=0)
    {

        if (!$iCheckMask)
            return true;
        
        if (!$oUser || !$oComment)
            return $this->Lang_Get('not_access');
        
        if ($oUser->isAdministrator())
            return true;
        
        $aUsers=Config::Get('plugin.editcomment.comment_editors');
        if (is_array($aUsers) && in_array($oUser->getId(),$aUsers))
            return true;
        if ($oComment->getTargetType() != 'talk') {
            if($oComment->getTarget()->getBlog()->getType() != 'personal') {
                $oBlog = $oComment->getTarget()->getBlog()->Blog_GetBlogUserByBlogIdAndUserId($oComment->getTarget()->getBlog()->getId(), $oUser->getId());
                if ($oBlog) {
                    if ($oBlog->getIsModerator() or $oBlog->getIsAdministrator())
                        return true;
                }
                if ($oComment->getTarget()->getBlog()->getOwnerId() == $oUser->getId()){
                    return true;
                }
            }
        }

        if ($oUser->getUserIsAdministrator())
            return true;
        if ($oComment->getTargetType() != "talk"){
	        if ($oUser->isGlobalModerator() and $oComment->getTarget()->getBlog()->getType() == "open") {
	            return true;
	        }
        }
        if ($oUser->getUserId() != $oComment->getUserId() && !$oUser->isAdministrator())
            return $this->Lang_Get('not_access');
        
        if ($iCheckMask & PluginEditcomment_ModuleACL::ACL_EC_CHECK_MAX_EDIT_COUNT != 0 && Config::Get('plugin.editcomment.max_edit_count'))
        {
            if ($oComment->getEditCount() > Config::Get('plugin.editcomment.max_edit_count'))
                return $this->Lang_Get('plugin.editcomment.err_max_edit_count');
        }
        
        if ($iCheckMask & PluginEditcomment_ModuleACL::ACL_EC_CHECK_MAX_EDIT_PERIOD != 0 && Config::Get('plugin.editcomment.max_edit_period'))
        {
            if (strtotime('+' . Config::Get('plugin.editcomment.max_edit_period') . ' second', strtotime($oComment->getEditDate())) < time())
                return $this->Lang_Get('plugin.editcomment.err_max_edit_period');
        }
        
        if ($iCheckMask & PluginEditcomment_ModuleACL::ACL_EC_CHECK_DENY_WITH_ANSWERS != 0 && Config::Get('plugin.editcomment.deny_with_answers'))
        {
            if ($this->PluginEditcomment_Editcomment_HasAnswers($oComment->getId()))
                return $this->Lang_Get('plugin.editcomment.err_deny_with_asnwers');
        }
        
        if ($iCheckMask & PluginEditcomment_ModuleACL::ACL_EC_CHECK_USER_RATING != 0)
        {
            if ($oUser->getRating() < Config::Get('plugin.editcomment.min_user_rating'))
                return $this->Lang_Get('plugin.editcomment.err_min_user_rating');
        }
        
        return true;
    }
}
?>
