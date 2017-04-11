<?php
/*-------------------------------------------------------
*
*   kEditComment.
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/


class ModuleEditcomment extends ModuleORM
{

    protected $oMapper;

    /**
     * Инициализация модуля
     */
    public function Init()
    {
        parent::Init();
        $this->oMapper=Engine::GetMapper(__CLASS__);
    }
    
    public function GetLastEditData($iCommentId)
    {
        $arr=$this->Editcomment_GetDataItemsByFilter(array('comment_id'=>$iCommentId, '#order'=>array('date_add'=>'desc'), '#limit'=>array(0, 1)));
        return array_pop($arr);
    }    
    
    public function HasAnswers($sId)
    {
        return $this->oMapper->HasAnswers($sId);
    }

    public function GetTemplateFilePath($sPluginClass,$sFileName)
    {
        $sPP=Plugin::GetTemplatePath($sPluginClass);
        $fName=$sPP . $sFileName;
        if (file_exists($fName))
            return $fName;
        
        $aa=explode("/", $sPP);
        array_pop($aa);
        array_pop($aa);
        $aa[]='default';
        $aa[]='';
        return join("/", $aa) . $sFileName;
    }

    public function GetTemplateFileWebPath($sPluginClass,$sFileName)
    {
        return str_replace(Config::Get('path.root.server'), Config::Get('path.root.web'), $this->GetTemplateFilePath($sPluginClass, $sFileName));
    }

}
?>
