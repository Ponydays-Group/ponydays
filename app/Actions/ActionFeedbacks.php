<?php

namespace App\Actions;

use App\Modules\Feedbacks\ModuleFeedbacks;
use App\Modules\User\ModuleUser;
use Engine\Action;
use Engine\LS;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Viewer\ModuleViewer;
use Engine\Router;

class ActionFeedbacks extends Action{
    protected $oUserCurrent 		= null;
    protected $iCurrentUserId		= null;
    protected $bIsCurrentUserAdmin	= null;

    //***************************************************************************************
    public function Init(){
        if(LS::Make(ModuleUser::class)->IsAuthorization()){
            $this->oUserCurrent 		= LS::Make(ModuleUser::class)->GetUserCurrent();
            $this->iCurrentUserId		= $this->oUserCurrent->getId();
            $this->bIsCurrentUserAdmin	= $this->oUserCurrent->isAdministrator();
        }

        $this->SetDefaultEvent('main');

    }

    //***************************************************************************************
    protected function RegisterEvent(){
        $this->AddEvent('main',					'EventMain');
        $this->AddEvent('LoadMoreActions',		'EventLoadMoreActions');
    }

    //***************************************************************************************
    protected function Redirect($sEvent = null, $sParam = null, $sMessage = null, $sError = null){
        $sPath	= Router::GetPath('feedbacks');
        if(!empty($sEvent)) $sPath = $sPath.$sEvent.'/';
        if(!empty($sParam)) $sPath = $sPath.$sParam.'/';

        if(!empty($sMessage))	LS::Make(ModuleMessage::class)->AddNotice($sMessage,'',true);
        if(!empty($sError))		LS::Make(ModuleMessage::class)->AddErrorSingle($sError,'',true);

        return Router::Location($sPath);
    }

    //***************************************************************************************
    protected function CheckAdmin(){
        if($this->oUserCurrent){
            if(!$this->oUserCurrent->isAdministrator()) {
                Router::Location(Router::GetPath('error'));
            }
        } else {
            Router::Location(Router::GetPath('error'));
        }
    }

    //***************************************************************************************
    protected function CheckUserLogin(){
        if(!$this->oUserCurrent) Router::Location(Router::GetPath('error'));
    }

    //***************************************************************************************
    protected function Error(){
        return Router::Location(Router::GetPath('error'));
    }

    //***************************************************************************************
    protected function ReturnToReferer(){
        return Router::Location($_SERVER['HTTP_REFERER']);
    }

    //***************************************************************************************
    //***************************************************************************************

    //***************************************************************************************
    protected function EventMain(){

        $this->CheckUserLogin();
        LS::Make(ModuleFeedbacks::class)->UpdateViewDatetimeByUserId($this->oUserCurrent->getId());

        $aActions	= LS::Make(ModuleFeedbacks::class)->GetActionsByUserId($this->oUserCurrent->getId(), 20);

        LS::Make(ModuleViewer::class)->Assign('aActions', $aActions);
        LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect', 'feedbacks');

    }

    //***************************************************************************************
    protected function EventLoadMoreActions(){

        $aResult = array('Errors' => array(), 'Text' => '', 'Stats' => '');
        $this->CheckUserLogin();

        $iLastActionId	= getRequest('LastActionId');
        $aActions		= LS::Make(ModuleFeedbacks::class)->GetActionsByUserIdLastActionId($this->oUserCurrent->getId(), $iLastActionId, 20);

        $oViewerLocal = LS::Make(ModuleViewer::class)->GetLocalViewer();
        $oViewerLocal->Assign('aActions', $aActions);

        $aResult['Text']	= $oViewerLocal->Fetch('actions.tpl');

        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        LS::Make(ModuleViewer::class)->AssignAjax('aResult', $aResult);

    }

    //***************************************************************************************
    protected function EventDebug(){
    }

    //***************************************************************************************
    public function EventShutdown(){
    }
}
