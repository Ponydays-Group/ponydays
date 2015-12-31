<?php

class PluginIgnore_ActionIgnore extends ActionPlugin {
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
    }

    protected function EventIndex() {
        PluginIgnore_ModuleUser_User_IgnoreBlogByUser('0', '1', 'blogs');
        $this->Viewer_Assign('aBooks', GetIgnoredBlogsByUser('0'));
    }

    protected function EventIgnoreBlog()
    {
        PluginIgnore_ModuleUser_User_IgnoreBlogByUser('0', '1', 'blogs');
        $this->Viewer_Assign('aBooks', GetIgnoredBlogsByUser('0'));
    }
    /**
     * Завершение работы экшена
     */
    public function EventShutdown() {

    }
}
?>
