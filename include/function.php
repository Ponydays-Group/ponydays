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

use Engine\Entity;

/**
 * функция доступа к GET POST параметрам
 *
 * @param  string $sName
 * @param  mixed  $default
 * @param  string $sType
 *
 * @return mixed
 */
function getRequest($sName, $default = null, $sType = null)
{
    /**
     * Выбираем в каком из суперглобальных искать указанный ключ
     */
    switch (strtolower($sType)) {
        default:
        case null:
            $aStorage = $_REQUEST;
            break;
        case 'get':
            $aStorage = $_GET;
            break;
        case 'post':
            $aStorage = $_POST;
            break;
    }

    if (isset($aStorage[$sName])) {
        if (is_string($aStorage[$sName])) {
            return trim($aStorage[$sName]);
        } else {
            return $aStorage[$sName];
        }
    }

    return $default;
}

/**
 * функция доступа к GET POST параметрам, которая значение принудительно приводит к строке
 *
 * @param string $sName
 * @param mixed  $default
 * @param string $sType
 *
 * @return string
 */
function getRequestStr($sName, $default = null, $sType = null)
{
    return (string)getRequest($sName, $default, $sType);
}

/**
 * Определяет был ли передан указанный параметр методом POST
 *
 * @param  string $sName
 *
 * @return bool
 */
function isPost($sName)
{
    return (getRequest($sName, null, 'post') !== null);
}

/**
 * генерирует случайную последовательность символов
 *
 * @param int $iLength
 *
 * @return string
 */
function func_generator($iLength = 10)
{
    if ($iLength > 32) {
        $iLength = 32;
    }

    return substr(md5(uniqid(mt_rand(), true)), 0, $iLength);
}

/**
 * htmlspecialchars умеющая обрабатывать массивы
 *
 * @param mixed $data
 * @param int %walkIndex - represents the key/index of the array being recursively htmlspecialchars'ed
 *
 * @return void
 */
function func_htmlspecialchars(&$data, $walkIndex = null)
{
    if (is_string($data)) {
        $data = htmlspecialchars($data);
    } elseif (is_array($data)) {
        array_walk($data, __FUNCTION__);
    }
}

/**
 * stripslashes умеющая обрабатывать массивы
 *
 * @param array|string $data
 */
function func_stripslashes(&$data)
{
    if (is_array($data)) {
        foreach ($data as $sKey => $value) {
            if (is_array($value)) {
                func_stripslashes($data[$sKey]);
            } else {
                $data[$sKey] = stripslashes($value);
            }
        }
    } else {
        $data = stripslashes($data);
    }
}

/**
 * Проверяет на корректность значение соглавно правилу
 *
 * @param string $sValue
 * @param string $sParam
 * @param int    $iMin
 * @param int    $iMax
 *
 * @return bool
 */
function func_check($sValue, $sParam, $iMin = 1, $iMax = 100)
{
    if (is_array($sValue)) {
        return false;
    }
    switch ($sParam) {
        case 'id':
            if (preg_match("/^\d{".$iMin.','.$iMax."}$/", $sValue)) {
                return true;
            }
            break;
        case 'float':
            if (preg_match("/^[\-]?\d+[\.]?\d*$/", $sValue)) {
                return true;
            }
            break;
        case 'mail':
            if (preg_match("/^[\da-z\_\-\.\+]+@[\da-z_\-\.]+\.[a-z]{2,5}$/i", $sValue)) {
                return true;
            }
            break;
        case 'login':
            if (preg_match("/^[\da-z\_\-]{".$iMin.','.$iMax."}$/i", $sValue)) {
                return true;
            }
            break;
        case 'md5':
            if (preg_match("/^[\da-z]{32}$/i", $sValue)) {
                return true;
            }
            break;
        case 'password':
            if (mb_strlen($sValue, 'UTF-8') >= $iMin) {
                return true;
            }
            break;
        case 'text':
            if (mb_strlen($sValue, 'UTF-8') >= $iMin and mb_strlen($sValue, 'UTF-8') <= $iMax) {
                return true;
            }
            break;
        default:
            return false;
    }

    return false;
}

/**
 * Определяет IP адрес
 *
 * @return string
 */
function func_getIp()
{
    // Если запускаем через консоль, то REMOTE_ADDR не определен
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
}


/**
 * Заменяет стандартную header('Location: *');
 *
 * @param string $sLocation
 */
function func_header_location($sLocation)
{
    $sProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
    header("{$sProtocol} 301 Moved Permanently");
    header('Location: '.$sLocation);
    exit();
}

/**
 * Создаёт каталог по полному пути
 *
 * @param string $sBasePath
 * @param string $sNewDir
 */
function func_mkdir($sBasePath, $sNewDir)
{
    $sDirToCheck = rtrim($sBasePath, '/').'/'.$sNewDir;
    if (!is_dir($sDirToCheck)) {
        @mkdir($sDirToCheck, 0755, true);
    }
}

/**
 * Возвращает обрезанный текст по заданное число слов
 *
 * @param string $sText
 * @param int    $iCountWords
 *
 * @return string
 */
function func_text_words($sText, $iCountWords)
{
    $aWords = preg_split('#[\s\r\n]+#um', $sText);
    if ($iCountWords < count($aWords)) {
        $aWords = array_slice($aWords, 0, $iCountWords);
    }

    return join(' ', $aWords);
}

/**
 * Меняет числовые ключи массива на их значения
 *
 * @param array  $arr
 * @param string $sDefValue
 */
function func_array_simpleflip(&$arr, $sDefValue = 1)
{
    foreach ($arr as $key => $value) {
        if (is_int($key) and is_string($value)) {
            unset($arr[$key]);
            $arr[$value] = $sDefValue;
        }
    }
}

function func_build_cache_keys($array, $sBefore = '', $sAfter = '')
{
    $aRes = [];
    foreach ($array as $key => $value) {
        $aRes[$value] = $sBefore.$value.$sAfter;
    }

    return $aRes;
}

function func_array_sort_by_keys($array, $aKeys)
{
    $aResult = [];
    foreach ($aKeys as $iKey) {
        if (isset($array[$iKey])) {
            $aResult[$iKey] = $array[$iKey];
        }
    }

    return $aResult;
}

/**
 * Сливает два ассоциативных массива
 *
 * @param array $aArr1
 * @param array $aArr2
 *
 * @return array
 */
function func_array_merge_assoc($aArr1, $aArr2)
{
    $aRes = $aArr1;
    foreach ($aArr2 as $k2 => $v2) {
        $bIsKeyInt = false;
        if (is_array($v2)) {
            foreach ($v2 as $k => $v) {
                if (is_int($k)) {
                    $bIsKeyInt = true;
                    break;
                }
            }
        }
        if (is_array($v2) and !$bIsKeyInt and isset($aArr1[$k2])) {
            $aRes[$k2] = func_array_merge_assoc($aArr1[$k2], $v2);
        } else {
            $aRes[$k2] = $v2;
        }
    }

    return $aRes;
}

function func_underscore($sStr)
{
    return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $sStr));
}

function func_camelize($sStr)
{
    $aParts = explode('_', $sStr);
    $sCamelized = '';
    foreach ($aParts as $sPart) {
        $sCamelized .= ucfirst($sPart);
    }

    return $sCamelized;
}


function func_convert_entity_to_array(Entity $oEntity, $aMethods = null, $sPrefix = '')
{
    if (!is_array($aMethods)) {
        $aMethods = get_class_methods($oEntity);
    }
    $aEntity = [];
    foreach ($aMethods as $sMethod) {
        if (!preg_match('#^get([a-z][a-z\d]*)$#i', $sMethod, $aMatch)) {
            continue;
        }
        $sProp = strtolower(preg_replace('#([a-z])([A-Z])#', '$1_$2', $aMatch[1]));
        $mValue = call_user_func([$oEntity, $sMethod]);
        $aEntity[$sPrefix.$sProp] = $mValue;
    }

    return $aEntity;
}

if (PHP_VERSION_ID < 70300) {
    function setcookie_s(
        $name,
        $value = "",
        $expire = 0,
        $path = "",
        $domain = "",
        $secure = false,
        $httponly = false,
        $samesite = ""
    ) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
} else {
    function setcookie_s(
        $name,
        $value = "",
        $expire = 0,
        $path = "",
        $domain = "",
        $secure = false,
        $httponly = false,
        $samesite = ""
    ) {
        setcookie(
            $name,
            $value,
            [
                'expires'  => $expire,
                'path'     => $path,
                'domain'   => $domain,
                'secure'   => $secure,
                'httponly' => $httponly,
                'samesite' => $samesite
            ]
        );
    }
}

function get_maximum_upload_size()
{
    return min(
        convert_config_size_to_bytes(ini_get('post_max_size')),
        convert_config_size_to_bytes(ini_get('upload_max_filesize'))
    );
}

function convert_config_size_to_bytes($sSize)
{
    $sSuffix = strtoupper(substr($sSize, -1));
    if (!in_array($sSuffix, ['P', 'T', 'G', 'M', 'K'])) {
        return (int)$sSize;
    }
    $iValue = substr($sSize, 0, -1);
    switch ($sSuffix) {
        /** @noinspection PhpMissingBreakStatementInspection */
        case 'P':
            $iValue *= 1024;
        // Fallthrough intended
        /** @noinspection PhpMissingBreakStatementInspection */
        case 'T':
            $iValue *= 1024;
        // Fallthrough intended
        /** @noinspection PhpMissingBreakStatementInspection */
        case 'G':
            $iValue *= 1024;
        // Fallthrough intended
        /** @noinspection PhpMissingBreakStatementInspection */
        case 'M':
            $iValue *= 1024;
        // Fallthrough intended
        case 'K':
            $iValue *= 1024;
            break;
    }

    return (int)$iValue;
}

function get_class_name(string $class)
{
    $arr = explode('\\', $class);

    return array_pop($arr);
}

function _gov_s_date_asc($a, $b): bool
{
    $a_time = strtotime($a['date']);
    $b_time = strtotime($b['date']);
    if ($a_time > $b_time) {
        return 1;
    }
    if ($a_time < $b_time) {
        return -1;
    }

    return 0;
}

function _gov_s_date_desc($a, $b): bool
{
    return -_gov_s_date_asc($a, $b);
}
