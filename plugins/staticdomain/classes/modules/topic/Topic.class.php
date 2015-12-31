<?php
/**
 * Domain for static - перенос статических файлов на отдельный домен
 *
 * Версия:	1.0.0
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_newsocialcomments
 *
 **/

class PluginStaticdomain_ModuleTopic extends PluginStaticdomain_Inherit_ModuleTopic {
	/**
	 * Загрузить изображение
	 *
	 * @param array $aFile	Массив $_FILES
	 * @return string|bool
	 */
	public function UploadTopicPhoto($aFile) {
        $sFile = parent::UploadTopicPhoto($aFile);
        $sFilePathOld = $this->Image_GetServerPath($sFile);
        $sServer = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('path.root.server')),'/');
        $sStatic = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('plugin.staticdomain.static_server')),'/');
        $sFilePathNew = str_replace($sServer . '/', $sStatic . '/', $sFilePathOld);
        @rename(str_replace('/', DIRECTORY_SEPARATOR, $sFilePathOld), str_replace('/', DIRECTORY_SEPARATOR, $sFilePathNew));
		return $this->Image_GetWebPath($sFilePathNew);
	}
}
?>