<?php
/*-------------------------------------------------------
*
*   k
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

class HookEditcomment extends Hook
{

    protected $oUserCurrent;

    public function RegisterHook()
    {
        $this->oUserCurrent=$this->User_GetUserCurrent();
        if (!$this->oUserCurrent)
            return;
        
        $this->AddHook('template_comment_action', 'InjectEditLink');
        $this->AddHook('template_comment_tree_end', 'InjectEditButtonCode');
    }

    public function InjectEditLink($aParam)
    {
        $this->oUserCurrent=$this->User_GetUserCurrent();
        
        if (Config::Get('template_check_edit_rights'))
            if ($this->ACL_UserCanEditComment($this->oUserCurrent, $aParam['comment'],Config::Get('template_check_edit_rights'))!==true)
                return;
        
        $this->Viewer_Assign('iCommentId', $aParam['comment']->getId());
        return $this->Viewer_Fetch('inject_editcomment_command.tpl');
    }
    
    public function InjectEditButtonCode($aParam)
    {
        $this->Viewer_Assign('iTargetId', $aParam['iTargetId']);
        $this->Viewer_Assign('sTargetType', $aParam['sTargetType']);
        $this->Viewer_Assign('oUserCurrent', $this->User_GetUserCurrent());
        $sText=$this->Viewer_Fetch('inject_edit_button_code.tpl');
        return $sText.$this->Viewer_Fetch('window_history.tpl');
    }

}
?>