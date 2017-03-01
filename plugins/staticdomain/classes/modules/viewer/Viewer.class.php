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

class PluginStaticdomain_ModuleViewer extends PluginStaticdomain_Inherit_ModuleViewer {
	/**
	 * Сжимает все переданные файлы в один,
	 * использует файловое кеширование
	 *
	 * @param  array  $aFiles	Список файлов
	 * @param  string $sType	Тип файла - js, css
	 * @return array
	 */
	protected function Compress($aFiles,$sType) {
        if (Config::Get('plugin.staticdomain.use_static_cache')) {
            $sCacheName = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('path.smarty.cache')),'/');
            $sServer = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('path.root.server')),'/');
            $sStatic = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('plugin.staticdomain.static_server')),'/');
            $bServer = ($sStatic !== $sServer) && (
                (strpos($sStatic . '/', $sServer . '/') === 0 && strpos($sCacheName, $sStatic . '/') !== 0) ||
                (strpos($sServer . '/', $sStatic . '/') === 0 && strpos($sCacheName, $sServer . '/') === 0) ||
                (strpos($sCacheName, $sServer . '/') === 0 && strpos($sCacheName, $sStatic . '/') !== 0));
            if ($bServer) {
                $sCacheName = str_replace($sServer . '/', $sStatic . '/', $sCacheName);
            }
            $sCacheName .= '/' . Config::Get('view.skin') . '/' . md5(serialize($aFiles) . '_head') . ".{$sType}";
            if(!file_exists($sCacheName)) {
                $sCacheName = parent::Compress($aFiles,$sType);
                $sFilePathOld = $this->GetServerPath($sCacheName);
                $sFilePathNew = str_replace('/', DIRECTORY_SEPARATOR, str_replace($sServer . '/', $sStatic . '/', $sFilePathOld));
                @mkdir(dirname($sFilePathNew), 0755, true);
                @rename(str_replace('/', DIRECTORY_SEPARATOR, $sFilePathOld), str_replace('/', DIRECTORY_SEPARATOR, $sFilePathNew));
                return $this->GetWebPath($sFilePathNew);
            } else {
                return $this->GetWebPath($sCacheName);
            }
        } else {
            return parent::Compress($aFiles,$sType);
        }
	}
	/**
	 * Преобразует абсолютный путь к файлу в WEB-вариант
	 *
	 * @param  string $sFile	Серверный путь до файла
	 * @return string
	 */
	protected function GetWebPath($sFile) {
        if (Config::Get('plugin.staticdomain.use_static_cache')) {
            $sServer = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('path.root.server')),'/');
            $sStatic = rtrim(str_replace(DIRECTORY_SEPARATOR,'/',Config::Get('plugin.staticdomain.static_server')),'/');
            $bServer = ($sStatic !== $sServer) && (
                (strpos($sStatic . '/', $sServer . '/') === 0 && strpos($sFile, $sStatic . '/') !== 0) ||
                (strpos($sServer . '/', $sStatic . '/') === 0 && strpos($sFile, $sServer . '/') === 0) ||
                (strpos($sFile, $sServer . '/') === 0 && strpos($sFile, $sStatic . '/') !== 0));
            if ($bServer) {
                $sServerPath = $sServer;
                $sWebPath    = rtrim(Config::Get('path.root.web'), '/');
            } else {
                $sServerPath = $sStatic;
                $sWebPath    = rtrim(Config::Get('plugin.staticdomain.static_web'), '/');
            }
            return str_replace($sServerPath, $sWebPath, str_replace(DIRECTORY_SEPARATOR,'/',$sFile));
        } else {
            return parent::GetWebPath($sFile);
        }
	}
	/**
	 * Преобразует WEB-путь файла в серверный вариант
	 *
	 * @param  string $sFile	Web путь до файла
	 * @return string
	 */
	protected function GetServerPath($sFile) {
        if (Config::Get('plugin.staticdomain.use_static_cache')) {
            $sFile = str_replace('//www.','//',$sFile);
            $sServer  = str_replace('//www.','//',Config::Get('path.root.web'));
            $sStatic  = str_replace('//www.','//',Config::Get('plugin.staticdomain.static_web'));
            $bServer = (parse_url($sFile,PHP_URL_HOST) === parse_url($sServer,PHP_URL_HOST));
            $bStatic = (parse_url($sFile,PHP_URL_HOST) === parse_url($sStatic,PHP_URL_HOST));
            if (!$bServer && !$bStatic) {
                return $sFile;
            }
            $sFile = ltrim(parse_url($sFile,PHP_URL_PATH),'/');
            if ($bStatic) {
                return rtrim(Config::Get('plugin.staticdomain.static_server'),'/').'/'.$sFile;
            } else {
                return rtrim(Config::Get('path.root.server'),'/').'/'.$sFile;
            }
        } else {
            return parent::GetServerPath($sFile);
        }
	}
}
?>