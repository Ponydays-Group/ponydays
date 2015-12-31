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

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin'))
{
    die('Hacking attempt!');
}

class PluginEditcomment extends Plugin
{

    protected $aInherits=array('module'=>array('ModuleComment','ModuleACL'),'action'=>array('ActionAjax'),'mapper'=>array('ModuleComment_MapperComment'));


    /**
	 * Активация плагина "Слежение за комментами".
	 * Создание таблицы в базе данных при ее отсутствии.
	 */
    public function Activate()
    {
        if (!$this->isTableExists('prefix_editcomment_data'))
        {
            $this->ExportSQL(dirname(__FILE__) . '/install.sql');
        }
        if (!$this->isFieldExists('prefix_comment','comment_edit_count'))
        {
            $this->ExportSQL(dirname(__FILE__) . '/install_comment.sql');
        }
        return true;
    }

    /**
	 * Деактивация плагина "Слежение за комментами".
	 * Проверка на присутсвие модов
	 */
    public function Deactivate()
    {
        return true;
    }

    /**
	 * Инициализация плагина
	 */
    public function Init()
    {
        $this->Viewer_AppendScript($this->GetTemplateFilePath(__CLASS__,'js/comments.js'));
        $this->Viewer_AppendStyle($this->GetTemplateFilePath(__CLASS__,'css/style.css'));
        if (Config::Get('plugin.editcomment.max_history_depth')<0)
            Config::Set('plugin.editcomment.max_history_depth',0);
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