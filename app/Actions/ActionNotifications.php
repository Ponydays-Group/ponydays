<?php

namespace App\Actions;

use App\Modules\Notification\ModuleNotification;
use App\Modules\User\ModuleUser;
use Engine\Action;
use Engine\LS;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Viewer\ModuleViewer;
use Engine\Router;

class ActionNotifications extends Action {
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
        $sPath	= Router::GetPath('notifications');
        if(!empty($sEvent)) $sPath = $sPath.$sEvent.'/';
        if(!empty($sParam)) $sPath = $sPath.$sParam.'/';

        if(!empty($sMessage))	LS::Make(ModuleMessage::class)->AddNotice($sMessage,'',true);
        if(!empty($sError))		LS::Make(ModuleMessage::class)->AddErrorSingle($sError,'',true);

        return Router::Location($sPath);
    }

    //***************************************************************************************
    protected function CheckAdmin(){
        if($this->oUserCurrent){
            if(!$this->oUserCurrent->isAdministrator())
                Router::Location(Router::GetPath('error'));
        } else {
            Router::Location(Router::GetPath('error'));
        }
    }

    //***************************************************************************************
    protected function CheckUserLogin(){
        if(!$this->oUserCurrent)
            Router::Location(Router::GetPath('error'));
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

        $aNotifications = LS::Make(ModuleNotification::class)->getNotification($this->oUserCurrent->getId(), 1, 20, null);
        $aUsers = array();
        foreach ($aNotifications as $oNotification) {
            array_push($aUsers, LS::Make(ModuleUser::class)->GetUserById($oNotification->getSenderUserId()));
        }

        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('notifications.header'));
        LS::Make(ModuleViewer::class)->Assign('aNotifications', $aNotifications);
        LS::Make(ModuleViewer::class)->Assign('aUsers', $aUsers);
        LS::Make(ModuleViewer::class)->Assign('iPage', 2);

    }

    //***************************************************************************************
    protected function EventLoadMoreActions(){

        $aResult = array('Errors' => array(), 'Text' => '', 'Stats' => '');
        $this->CheckUserLogin();

        $iPage	= getRequest('Page');
        $aNotifications = LS::Make(ModuleNotification::class)->getNotification($this->oUserCurrent->getId(), $iPage, 20, null);
        $aUsers = array();
        foreach ($aNotifications as $oNotification) {
            array_push($aUsers, LS::Make(ModuleUser::class)->GetUserById($oNotification->getSenderUserId()));
        }

        $oViewerLocal = LS::Make(ModuleViewer::class)->GetLocalViewer();
        $oViewerLocal->Assign('aNotifications', $aNotifications);
        $oViewerLocal->Assign('iPage', $iPage + 1);
        $oViewerLocal->Assign('aUsers', $aUsers);

        $aResult['Text']	= $oViewerLocal->Fetch('notifications.tpl');

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
