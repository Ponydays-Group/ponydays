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
define('LS_VERSION', '1.0.3');

require 'vendor/autoload.php';

use Engine\Config;

function walk_directory(string $dir_name, callable $func)
{
    if ($dir = opendir($dir_name)) {
        while (false !== ($sFileInclude = readdir($dir))) {
            $sFileIncludePathFull = $dir_name.$sFileInclude;
            if ($sFileInclude != '.' and $sFileInclude != '..' and is_file($sFileIncludePathFull)) {
                $aPathInfo = pathinfo($sFileIncludePathFull);
                $func($aPathInfo, $dir_name, $sFileInclude);
            }
        }
        closedir($dir);
    }
}

walk_directory(
    dirname(__FILE__).'/engine_config/',
    function ($aPathInfo, $sDirConf, $sFileInclude) {
        if (isset($aPathInfo['extension']) and strtolower($aPathInfo['extension']) == 'json') {
            Config::LoadFromFile($sDirConf.$sFileInclude, false);
        }
    }
);

/**
 * Инклудим все *.php файлы из каталога {path.root.engine}/include/ - это файлы ядра
 */
walk_directory(
    './include/',
    function ($aPathInfo, $sDirInclude, $sFileInclude) {
        if (isset($aPathInfo['extension']) and strtolower($aPathInfo['extension']) == 'php') {
            require_once($sDirInclude.$sFileInclude);
        }
    }
);

/**
 * Подгружаем файлы локального и продакшн-конфига
 */
if (file_exists(Config::Get('path.root.server').'/config/local.config.json')) {
    Config::LoadFromFile(Config::Get('path.root.server').'/config/local.config.json', false);
}
