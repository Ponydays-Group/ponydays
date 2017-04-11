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

class ActionAjax extends Inherit_ActionAjax
{

    protected function RegisterEvent()
    {
        $this->AddEvent('editcomment-gethistory', 'EventGetHistory');
        $this->AddEvent('editcomment-getsource', 'EventGetSource');
        $this->AddEvent('editcomment-edit', 'EventEdit');
        parent::RegisterEvent();
    }

    protected function EventGetHistory()
    {
        /**
		 * Устанавливаем формат Ajax ответа
		 */
        $this->Viewer_SetResponseAjax('json');
        
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $oComment=$this->Comment_GetCommentById(getRequest('reply'));
        
        if (!$oComment)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $sCheckResult=$this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true)
        {
            $this->Message_AddErrorSingle($sCheckResult);
            return;
        }
        
        $aData=$this->Editcomment_GetDataItemsByCommentId($oComment->getId(), array('#order'=>array('date_add'=>'desc')));
        
        foreach ($aData as $oData)
            $oData->setText($this->Text_Parser($oData->getCommentTextSource()));
        
        $oViewerLocal=$this->Viewer_GetLocalViewer();
        $oViewerLocal->Assign('aHistory', $aData);
        $this->Viewer_AssignAjax('sContent', $oViewerLocal->Fetch($this->Editcomment_GetTemplateFilePath(__CLASS__, 'history.tpl')));
    }

    protected function EventGetSource()
    {
        /**
		 * Устанавливаем формат Ajax ответа
		 */
        $this->Viewer_SetResponseAjax('json');
        
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $oComment=$this->Comment_GetCommentById(getRequest('idComment'));
        
        if (!$oComment)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $sCheckResult=$this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true)
        {
            $this->Message_AddErrorSingle($sCheckResult);
            return;
        }
        
        $oEditData=$this->Editcomment_GetLastEditData($oComment->getId());
        
        if ($oEditData)
            $sCommentSource=$oEditData->getCommentTextSource();
        else 
            if (!Config::Get('view.tinymce'))
                $sCommentSource=str_replace(array("<br>", "<br/>"), array(""), $oComment->getText());
            else
                $sCommentSource=$oComment->getText();
        
        $this->Viewer_AssignAjax('sCommentSource', $sCommentSource);
        $this->Viewer_AssignAjax('bHasHistory', !is_null($oEditData));
    }

    protected function EventEdit()
    {
        $ip=$_SERVER['REMOTE_ADDR'];
        /**
		 * Устанавливаем формат Ajax ответа
		 */
        $this->Viewer_SetResponseAjax('json');
        
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $oComment=$this->Comment_GetCommentById(getRequest('reply'));
        
        if (!$oComment)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $sCheckResult=$this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true)
        {
            $this->Message_AddErrorSingle($sCheckResult);
            return;
        }
        
        $sText=$this->Text_Parser(getRequest('comment_text'));
        
        if (mb_strlen($sText, 'utf-8') > Config::Get('module.comment.max_length'))
        {
            $this->Message_AddErrorSingle($this->Lang_Get('editcomment.err_max_comment_length', array('maxlength'=>Config::Get('editcomment.max_comment_length'))));
            return;
        }
        
        $sDE=date("Y-m-d H:i:s");
        
        $oOldData=$this->Editcomment_GetLastEditData($oComment->getId());
        
        if ($oOldData && $oOldData->getCommentTextSource() == getRequest('comment_text'))
        {
            $this->Message_AddNoticeSingle($this->Lang_Get('editcomment.notice_nothing_changed'));
            $this->Viewer_AssignAjax('bEdited', false);
        }
        else
        {
            if (Config::Get('editcomment.change_online'))
                $oComment->setDate($sDE);
            $oComment->setEditCount($oComment->getEditCount() + 1);
            $oComment->setEditDate($sDE);
            $oViewerLocal=$this->Viewer_GetLocalViewer();
            $oViewerLocal->Assign('oComment', $oComment);
            $oViewerLocal->Assign('oUserCurrent', $this->oUserCurrent);
            
            if (Config::Get('editcomment.add_edit_date'))
                $oComment->setText($sText . $oViewerLocal->Fetch($this->Editcomment_GetTemplateFilePath(__CLASS__, 'inject_comment_edited.tpl')));
            else
                $oComment->setText($sText);
            $oComment->setTextHash(md5($sText));
            
            if ($this->Comment_UpdateComment($oComment))
            {
                if (Config::Get('editcomment.change_online'))
                {
                    $oCommentOnline=Engine::GetEntity('Comment_CommentOnline');
                    $oCommentOnline->setTargetId($oComment->getTargetId());
                    $oCommentOnline->setTargetType($oComment->getTargetType());
                    $oCommentOnline->setTargetParentId($oComment->getTargetParentId());
                    $oCommentOnline->setCommentId($oComment->getId());
                    
                    $this->Comment_AddCommentOnline($oCommentOnline);
                }
                
                $this->oUserCurrent->setDateCommentLast($sDE);
                $this->User_Update($this->oUserCurrent);
                
                $oData=Engine::GetEntity('ModuleEditcomment_EntityData');
                $oData->setCommentTextSource(getRequest('comment_text'));
                $oData->setCommentId($oComment->getId());
                $oData->setUserId($this->oUserCurrent->getId());
                $oData->setDateAdd($sDE);
                
                if (!$oData->save())
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('error'));
                    return;
                }
                elseif (Config::Get('editcomment.max_history_depth') > 0)
                {
                    $aTemp=$this->Editcomment_GetDataItemsByFilter(array('comment_id'=>$oComment->getId(), '#page'=>array(1, 0)));
                    if ($aTemp['count'] > Config::Get('editcomment.max_history_depth'))
                    {
                        $aOldData=$this->Editcomment_GetDataItemsByFilter(array('comment_id'=>$oComment->getId(), '#order'=>array('date_add'=>'asc'), '#limit'=>array(0, $aTemp['count'] - Config::Get('editcomment.max_history_depth'))));
                        foreach ($aOldData as $oOldData)
                            $oOldData->delete();
                    }
                }
                
                $this->Viewer_AssignAjax('bEdited', true);
                $this->Viewer_AssignAjax('bCanEditMore', $this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX) === true);
                $this->Viewer_AssignAjax('sCommentText', $oComment->getText());
            }
            else
                $this->Message_AddErrorSingle($this->Lang_Get('error'));
        }
        $sLogText = $this->oUserCurrent->getLogin()." редактировал коммент ".$oComment->getId()." ".$ip;
        $this->Logger_Notice($sLogText);
        $this->Viewer_AssignAjax('bCanEditMore', $this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX) === true);
    }
}
?>
