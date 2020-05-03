<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * Основные константы
 */
define('LS_VERSION','1.0.3');

// /**
//  * Operations with Config object
//  */
require_once(dirname(dirname(__FILE__))."/engine/Config.php");


$sDirConf=dirname(__FILE__).'/engine_config/';
if ($hDirConf = opendir($sDirConf)) {
	while (false !== ($sFileInclude = readdir($hDirConf))) {
		$sFileIncludePathFull=$sDirConf.$sFileInclude;
		if ($sFileInclude !='.' and $sFileInclude !='..' and is_file($sFileIncludePathFull)) {
			$aPathInfo=pathinfo($sFileIncludePathFull);
			if (isset($aPathInfo['extension']) and strtolower($aPathInfo['extension'])=='json') {
				Config::LoadFromFile($sDirConf.$sFileInclude, false);
			}
		}
	}
	closedir($hDirConf);
}

/**
 * Инклудим все *.php файлы из каталога {path.root.engine}/include/ - это файлы ядра
 */
$sDirInclude='./include/';
if ($hDirInclude = opendir($sDirInclude)) {
	while (false !== ($sFileInclude = readdir($hDirInclude))) {
		$sFileIncludePathFull=$sDirInclude.$sFileInclude;
		if ($sFileInclude !='.' and $sFileInclude !='..' and is_file($sFileIncludePathFull)) {
			$aPathInfo=pathinfo($sFileIncludePathFull);
			if (isset($aPathInfo['extension']) and strtolower($aPathInfo['extension'])=='php') {
				require_once($sDirInclude.$sFileInclude);
			}
		}
	}
	closedir($hDirInclude);
}

/**
 * Инклудим все *.php файлы из каталога {path.root.server}/include/ - пользовательские файлы
 */
$sDirInclude=Config::get('path.root.server').'/include/';
if ($hDirInclude = opendir($sDirInclude)) {
	while (false !== ($sFileInclude = readdir($hDirInclude))) {
		$sFileIncludePathFull=$sDirInclude.$sFileInclude;
		if ($sFileInclude !='.' and $sFileInclude !='..' and is_file($sFileIncludePathFull)) {
			$aPathInfo=pathinfo($sFileIncludePathFull);
			if (isset($aPathInfo['extension']) and strtolower($aPathInfo['extension'])=='php') {
				require_once($sDirInclude.$sFileInclude);
			}
		}
	}
	closedir($hDirInclude);
}


/**
 * Подгружаем файлы локального и продакшн-конфига
 */
if(file_exists(Config::Get('path.root.server').'/config/local.config.json')) {
	Config::LoadFromFile(Config::Get('path.root.server').'/config/local.config.json',false);
}
