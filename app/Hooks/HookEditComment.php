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

namespace App\Hooks;

use App\Modules\ModuleACL;
use App\Modules\ModuleUser;
use Engine\Config;
use Engine\Hook;
use Engine\LS;
use Engine\Modules\ModuleViewer;

class HookEditComment extends Hook
{

    protected $oUserCurrent;

    public function RegisterHook()
    {
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        if (!$this->oUserCurrent)
            return;
        
        $this->AddHook('template_comment_action', 'InjectEditLink');
        $this->AddHook('template_comment_tree_end', 'InjectEditButtonCode');
    }

    public function InjectEditLink($aParam)
    {
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        
        if (Config::Get('module.editcomment.template_check_edit_rights'))
            if (LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $aParam['comment'],Config::Get('module.editcomment.template_check_edit_rights'))!==true)
                return;
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->Assign('iCommentId', $aParam['comment']->getId());
        return $viewer->Fetch('inject_editcomment_command.tpl');
    }
    
    public function InjectEditButtonCode($aParam)
    {
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->Assign('iTargetId', $aParam['iTargetId']);
        $viewer->Assign('sTargetType', $aParam['sTargetType']);
        $viewer->Assign('oUserCurrent', LS::Make(ModuleUser::class)->GetUserCurrent());
        $sText=$viewer->Fetch('inject_edit_button_code.tpl');
        return $sText.$viewer->Fetch('window_history.tpl');
    }

}
