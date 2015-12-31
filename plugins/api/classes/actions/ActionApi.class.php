<?php

require_once(Plugin::GetPath('api') . 'src/VK/VK.php');
require_once(Plugin::GetPath('api') . 'src/VK/VKException.php');

class PluginApi_ActionApi extends ActionPlugin {
    /**
     * Инициализация экшена
     */
    protected $oUserCurrent=null;

    public function Init() {
        $this->SetDefaultEvent('index');
    }
    /**
     * Регистрируем евенты
     */
    protected function RegisterEvent() {
        $this->AddEvent('index','EventIndex');
        $this->AddEvent('dir','EventDir');
	$this->AddEvent('ban','EventUsersBan');
	$this->AddEvent('skill','EventSkill');
    }

    protected function EventUsersBan()
    {
	$this->Security_ValidateSendForm();
	$oUserCurrent = $this->ModuleUser_GetUserCurrent();
	if (!$oUserCurrent->IsGlobalModerator()){
	    return false;
	}
        $bOk = false;
        $sUserLogin = getRequest('ban_login');
        if ($sUserLogin == $oUserCurrent->GetLogin()) {
            return false;
        }
        if (getRequest('ban_period') == 'days') {
            $nDays = intval(getRequest('ban_days'));
        } else {
            $nDays = null;
        }
        $sComment = getRequest('ban_comment');
        $oUser = $this->ModuleUser_GetUserByLogin($sUserLogin);
	if(getRequest('clear')=="true"){
		$this->PluginAceadminpanel_Admin_ClearUserBan($oUser->getId());
		$this->Viewer_Assign('dir',"Бан с пользователя " . $sUserLogin . " снят.");
		return true;
	}
        if($this->PluginAceadminpanel_Admin_SetUserBan($oUser->GetId(), $nDays, $sComment)){
		$bOk = "Пользователь " . $sUserLogin . " забанен.";
	}

	$this->Viewer_Assign('dir',$bOk);
    }
    protected function EventIndex() {
	$a = $_POST["to"];
	$oUserCurrent = $this->ModuleUser_GetUserCurrent();
	$oBlog = $this->ModuleBlog_GetBlogById($_POST["blog"]);
	$this->ModuleTalk_SendTalk("Просьба об инвайте", "Пользователь <a href='" . "/profile/" . $oUserCurrent->getLogin() . "/' class='user'>" . "<i class='icon-user'></i>" . $oUserCurrent->getLogin() . "</a> просит пригласить его в блог <a href='" . $oBlog->getUrlFull() . "'>" . $oBlog->getTitle() . "</a>.", $oUserCurrent->getId(), $a);
    }
    protected function EventDir() {
        $dir = scandir("/var/www/static/uploads/images/");
        $files = '';
        foreach($dir as $file) {
            if ($file != '.' && $file != '..' && $file == end($dir)){
                $files = substr($file, 0, strrpos($file, '.'))+1;
            }
        }
//        while($file = readdir($dir)) {
           // if (true){
                $this->Viewer_Assign('dir',$files);
           //	 }
  //      }
    }
    protected function EventSkill() {
        $oUser = $this->ModuleUser_GetUserById(19);
        $oUserCurrent = $this->ModuleUser_GetUserCurrent();
	    $this->Vote_DeleteVote($oUser->getId(), 'user', $oUserCurrent->getId());
    }
    public function EventShutdown() {

    }
}
?>
