<?php

class PluginPostingingroups_ModuleTopic extends PluginPostingingroups_Inherit_ModuleTopic {

	/**
	 * Обновляем содержимое топика (ExtractData)
	 * @param $oTopic
	 * @return mixed
	 */
	public function UpdateTopicContent($oTopic){
        return $this->oMapperTopic->UpdateTopicContent($oTopic);
    }

}


