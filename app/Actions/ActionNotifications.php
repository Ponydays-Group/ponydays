<?php

use Engine\Action;
use Engine\Router;

class ActionNotifications extends Action {
    protected $oUserCurrent 		= null;
    protected $iCurrentUserId		= null;
    protected $bIsCurrentUserAdmin	= null;

    //***************************************************************************************
    public function Init(){
        if($this->User_IsAuthorization()){
            $this->oUserCurrent 		= $this->User_GetUserCurrent();
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

        if(!empty($sMessage))	$this->Message_AddNotice($sMessage,'',true);
        if(!empty($sError))		$this->Message_AddErrorSingle($sError,'',true);

        return Router::Location($sPath);
    }

    //***************************************************************************************
    protected function CheckAdmin(){
        if($this->oUserCurrent){
            if(!$this->oUserCurrent->isAdministrator()) return Router::Location(Router::GetPath('error'));
        }else return Router::Location(Router::GetPath('error'));
    }

    //***************************************************************************************
    protected function CheckUserLogin(){
        if(!$this->oUserCurrent) return Router::Location(Router::GetPath('error'));
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

        $aNotifications = $this->Notification_getNotification($this->oUserCurrent->getId(), 1, 20, null);
        $aUsers = array();
        foreach ($aNotifications as $oNotification) {
            array_push($aUsers, $this->User_GetUserById($oNotification->getSenderUserId()));
        }

        $this->Viewer_AddHtmlTitle($this->Lang_Get('notifications.header'));
        $this->Viewer_Assign('aNotifications', $aNotifications);
        $this->Viewer_Assign('aUsers', $aUsers);
        $this->Viewer_Assign('iPage', 2);

    }

    //***************************************************************************************
    protected function EventLoadMoreActions(){

        $aResult = array('Errors' => array(), 'Text' => '', 'Stats' => '');
        $this->CheckUserLogin();

        $iPage	= getRequest('Page');
        $aNotifications = $this->Notification_getNotification($this->oUserCurrent->getId(), $iPage, 20, null);
        $aUsers = array();
        foreach ($aNotifications as $oNotification) {
            array_push($aUsers, $this->User_GetUserById($oNotification->getSenderUserId()));
        }

        $oViewerLocal = $this->Viewer_GetLocalViewer();
        $oViewerLocal->Assign('aNotifications', $aNotifications);
        $oViewerLocal->Assign('iPage', $iPage + 1);
        $oViewerLocal->Assign('aUsers', $aUsers);

        $aResult['Text']	= $oViewerLocal->Fetch('notifications.tpl');

        $this->Viewer_SetResponseAjax('json');
        $this->Viewer_AssignAjax('aResult', $aResult);

    }

    //***************************************************************************************
    protected function EventDebug(){
    }

    //***************************************************************************************
    public function EventShutdown(){
    }
}
