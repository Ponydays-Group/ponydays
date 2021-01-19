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

namespace Engine\Modules;

use Engine\Config;
use Engine\LS;
use Engine\Module;

/**
 * Модуль для работы с сессиями
 * Выступает в качестве врапера для стандартного механизма сессий
 *
 * @package engine.modules
 * @since   1.0
 */
class ModuleSession extends Module
{
    /**
     * ID  сессии
     *
     * @var null|string
     */
    protected $sId = null;
    /**
     * Данные сессии
     *
     * @var array
     */
    protected $aData = [];
    /**
     * Инициализация модуля
     *
     */
    public function Init()
    {
        /**
         * Стартуем сессию
         */
        $this->Start();
    }

    /**
     * Старт сессии
     *
     */
    protected function Start()
    {
        session_name(Config::Get('sys.session.name'));
        session_set_cookie_params(
            Config::Get('sys.session.timeout'),
            Config::Get('sys.session.path'),
            Config::Get('sys.session.host')
        );
        if (!session_id()) {
            /**
             * Попытка подменить идентификатор имени сессии через куку
             */
            if (isset($_COOKIE[Config::Get('sys.session.name')]) and !is_string($_COOKIE[Config::Get('sys.session.name')])) {
                unset($_COOKIE[Config::Get('sys.session.name')]);
                setcookie(
                    Config::Get('sys.session.name').'[]',
                    '',
                    1,
                    Config::Get('sys.cookie.path'),
                    Config::Get('sys.cookie.host')
                );
            }
            /**
             * Попытка подменить идентификатор имени сессии в реквесте
             */
            $aRequest = array_merge($_GET, $_POST); // Исключаем попадаение $_COOKIE в реквест
            if (
                @ini_get('session.use_only_cookies') === "0"
                and isset($aRequest[Config::Get('sys.session.name')])
                and !is_string($aRequest[Config::Get('sys.session.name')])
            ) {
                session_name($this->GenerateId());
            }

            // session_regenerate_id();
            session_start();
        }
    }

    /**
     * Получает идентификатор текущей сессии
     *
     */
    public function GetId()
    {
        return session_id();
    }

    /**
     * Гинерирует уникальный идентификатор
     *
     * @return string
     */
    protected function GenerateId()
    {
        return md5(func_generator().time());
    }

    /**
     * Получает значение из сессии
     *
     * @param string $sName Имя параметра
     *
     * @return mixed|null
     */
    public function Get($sName)
    {
        return isset($_SESSION[$sName]) ? $_SESSION[$sName] : null;
    }

    /**
     * Записывает значение в сессию
     *
     * @param string $sName Имя параметра
     * @param mixed  $data  Данные
     */
    public function Set($sName, $data)
    {
        $_SESSION[$sName] = $data;
    }

    /**
     * Удаляет значение из сессии
     *
     * @param string $sName Имя параметра
     */
    public function Drop($sName)
    {
        unset($_SESSION[$sName]);
    }

    /**
     * Получает разом все данные сессии
     *
     * @return array
     */
    public function GetData()
    {
        return $_SESSION;
    }

    /**
     * Завершает сессию, дропая все данные
     *
     */
    public function DropSession()
    {
        unset($_SESSION);
        session_destroy();
    }
}
