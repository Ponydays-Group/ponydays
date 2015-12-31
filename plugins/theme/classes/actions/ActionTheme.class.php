<?php

class PluginTheme_ActionTheme extends ActionPlugin {
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
        if (isPost('submit_book_save')) {
            $this->Security_ValidateSendForm();
            $post = $GLOBALS['_POST'];
            if ($post['theme']){
                $this->Message_AddNotice($post['theme'],'Выбрана тема');
            }
        }
    }
    public function EventShutdown() {

    }
}
?>
