<?php
/*
  Smartyphptag plugin
  (P) PSNet, 2008 - 2012
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
*/

class PluginSmartyphptag_HookSmartyphptag extends Hook {

  public function RegisterHook () {
    $this -> AddHook ('engine_init_complete', 'EngineInitComplete');
    $this -> AddHook ('template_footer_end', 'FooterEnd');
  }

  // ---

  public function EngineInitComplete () {
    $this -> Viewer_GetSmartyObject () -> registerPlugin ('block', 'php', 'SmartyPhpTag');
  }
  
  // ---

  public function FooterEnd () {
    return $this -> Viewer_Fetch (Plugin::GetTemplatePath (__CLASS__) . 'footer_end.tpl');
  }

}

// ---

function SmartyPhpTag ($params, $content, $template, &$repeat) {
  eval ($content);
  return '';
}

?>