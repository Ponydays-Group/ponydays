<?php

class PluginEpisodes_ActionEpisodes extends ActionPlugin {

    /**
     * Инициализация экшена
     */
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
        $this->Viewer_Assign('aEpisodes', 'test');
    }

    /**
     * Завершение работы экшена
     */
    public function EventShutdown() {

    }
}
?>
