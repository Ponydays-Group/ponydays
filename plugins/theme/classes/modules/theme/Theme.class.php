<?php

class PluginTheme_ModuleTheme extends Module {
	protected $oMapper;
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__); 
	}
	public function createBook($post){
        //Проходимся по массиву $post с данными
        //будущего объекта и очищаем от html-сущностей
        array_walk($post,function($field){
            htmlspecialchars($field);
        });

        //создаем объект сущности Book
        $theme = Engine::GetEntity('PluginTheme_ModuleTheme_EntityTheme');
        $theme->setTheme($post['theme']);

        //Объект book готов, теперь можем передать его
        // Мапперу и записать в базу данных
        if ($iId=$this->oMapper->createTheme($book)) {
            return $theme;
        }
        // если что-то пошло не так, возвращаем false
        return false;
    }


}
?>
