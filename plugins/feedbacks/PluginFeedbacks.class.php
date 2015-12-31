<?php

if (!class_exists('Plugin')){
	die('Quod licet Jovi, non licet bovi');
}

class PluginFeedbacks extends Plugin{

	//***************************************************************************************
	public function Init(){	
		$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath(__CLASS__) . 'css/main.css');
	}

	//***************************************************************************************
	public function Activate(){

		if (!$this->isTableExists('prefix_feedback_actions'))
			$this->ExportSQL(dirname(__FILE__).'/prefix_feedback_actions.sql');

		if (!$this->isTableExists('prefix_feedback_views'))
			$this->ExportSQL(dirname(__FILE__).'/prefix_feedback_views.sql');

		return true;
	}

	//***************************************************************************************
	public function  Deactivate(){
		return true;
	}
	
	//***************************************************************************************
	protected $aInherits = array(
		'module' => array('ModuleVote'),
	);
	
	//***************************************************************************************
	public $aDelegates = array(
    );

}
?>