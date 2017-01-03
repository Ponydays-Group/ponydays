<?php
/*
  Topicinfo plugin
  (P) PSNet, 2008 - 2012
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
*/

class PluginTopicinfo_HookTopicinfo extends Hook {

  public function RegisterHook () {
    $this -> AddHook ('engine_init_complete', 'AddStylesAndJS');
  }

  // ---

  public function AddStylesAndJS () {
    $sTemplateWebPath = Plugin::GetTemplateWebPath (__CLASS__);
    $this -> Viewer_AppendStyle ($sTemplateWebPath . 'css/style.css');
  }

}

?>