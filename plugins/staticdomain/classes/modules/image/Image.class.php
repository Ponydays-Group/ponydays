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

class PluginStaticdomain_ModuleImage extends PluginStaticdomain_Inherit_ModuleImage {
    /**
     * Сохраняет(копирует) файл изображения на сервер
     * Если переопределить данный метод, то можно сохранять изображения, например, на Amazon S3
     *
     * @param string $sFileSource	Полный путь до исходного файла
     * @param string $sDirDest	Каталог для сохранения файла относительно корня сайта
     * @param string $sFileDest	Имя файла для сохранения
     * @param int|null $iMode	Права chmod для файла, например, 0777
     * @param bool $bRemoveSource	Удалять исходный файл или нет
     * @return bool | string
     */
    public function SaveFile($sFileSource,$sDirDest,$sFileDest,$iMode=null,$bRemoveSource=false) {
        $sFileDestFullPath=rtrim(Config::Get('plugin.staticdomain.static_server'),"/").'/'.trim($sDirDest,"/").'/'.$sFileDest;
        $this->CreateDirectory($sDirDest);

        $bResult=copy($sFileSource,$sFileDestFullPath);
        if ($bResult and !is_null($iMode)) {
            chmod($sFileDestFullPath,$iMode);
        }
        if ($bRemoveSource) {
            unlink($sFileSource);
        }
        /**
         * Если копирование прошло успешно, возвращаем новый серверный путь до файла
         */
        if ($bResult) {
            return $sFileDestFullPath;
        }
        return false;
    }
    /**
     * Создает каталог по указанному адресу (с учетом иерархии)
     *
     * @param string $sDirDest	Каталог относительно корня сайта
     */
    public function CreateDirectory($sDirDest) {
        @func_mkdir(Config::Get('plugin.staticdomain.static_server'),$sDirDest);
    }
    /**
     * Возвращает серверный адрес по переданному web-адресу
     *
     * @param  string $sPath	WEB адрес изображения
     * @return string
     */
    public function GetServerPath($sPath) {
        $bServer = (parse_url($sPath,PHP_URL_HOST) === parse_url(Config::Get('path.root.web'),PHP_URL_HOST));
        $bStatic = (parse_url($sPath,PHP_URL_HOST) === parse_url(Config::Get('plugin.staticdomain.static_web'),PHP_URL_HOST));
        if (!$bServer && !$bStatic) {
            return $sPath;
        }
        $sPath = ltrim(parse_url($sPath,PHP_URL_PATH),'/');
        if($iOffset = Config::Get('path.offset_request_url')){
            $sPath = preg_replace('#^([^/]+/*){'.$iOffset.'}#msi', '', $sPath);
        }
        if ($bStatic) {
            return rtrim(Config::Get('plugin.staticdomain.static_server'),'/').'/'.$sPath;
        } else {
            return rtrim(Config::Get('path.root.server'),'/').'/'.$sPath;
        }
    }
    /**
     * Возвращает WEB адрес по переданному серверному адресу
     *
     * @param  string $sPath	Серверный адрес(путь) изображения
     * @return string
     */
    public function GetWebPath($sPath) {
        $sServer = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('path.root.server')),'/');
        $sStatic = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('plugin.staticdomain.static_server')),'/');
        $bServer = ($sStatic !== $sServer) && (
            (strpos($sStatic . '/', $sServer . '/') === 0 && strpos($sPath, $sStatic . '/') !== 0) ||
            (strpos($sServer . '/', $sStatic . '/') === 0 && strpos($sPath, $sServer . '/') === 0) ||
            (strpos($sPath, $sServer . '/') === 0 && strpos($sPath, $sStatic . '/') !== 0));
        if ($bServer) {
            $sServerPath = $sServer;
            $sWebPath    = rtrim(Config::Get('path.root.web'), '/');
        } else {
            $sServerPath = $sStatic;
            $sWebPath    = rtrim(Config::Get('plugin.staticdomain.static_web'), '/');
        }
        return str_replace($sServerPath, $sWebPath, str_replace(DIRECTORY_SEPARATOR,'/',$sPath));
    }
}
?>