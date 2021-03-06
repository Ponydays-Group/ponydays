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

namespace Engine;

use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleViewer;

/**
 * Класс роутинга(контроллера)
 * Инициализирует ядро, определяет какой экшен запустить согласно URL'у и запускает его.
 *
 * @package engine
 * @since   1.0
 */
class Router extends LsObject
{
    /**
     * Конфигурация роутинга, получается из конфига
     *
     * @var array
     */
    protected $aConfigRoute = [];
    /**
     * Текущий экшен
     *
     * @var string|null
     */
    static protected $sAction = null;
    /**
     * Текущий евент
     *
     * @var string|null
     */
    static protected $sActionEvent = null;
    /**
     * Имя текущего евента
     *
     * @var string|null
     */
    static protected $sActionEventName = null;
    /**
     * Класс текущего экшена
     *
     * @var string|null
     */
    static protected $sActionClass = null;
    /**
     * Текущий полный ЧПУ url
     *
     * @var string|null
     */
    static protected $sPathWebCurrent = null;
    /**
     * Список параметров ЧПУ url
     * <pre>/action/event/param0/param1/../paramN/</pre>
     *
     * @var array
     */
    static protected $aParams = [];
    /**
     * Объект текущего экшена
     *
     * @var Action|null
     */
    protected $oAction = null;
    /**
     * Объект ядра
     *
     * @var Engine|null
     */
    protected $oEngine = null;
    /**
     * Объект роутинга
     *
     * @see getInstance
     *
     * @var Router|null
     */
    static protected $oInstance = null;

    /**
     * Определяет, требуется ли перейти к исполнению следующего экшена
     * после завершения текущего
     *
     * @see ExecAction
     *
     * @var bool
     */
    protected $doNext = false;

    /**
     * Делает возможным только один экземпляр этого класса
     *
     * @return Router
     */
    static public function getInstance()
    {
        if (isset(self::$oInstance) and (self::$oInstance instanceof self)) {
            return self::$oInstance;
        } else {
            self::$oInstance = new self();

            return self::$oInstance;
        }
    }

    /**
     * Загрузка конфига роутинга при создании объекта
     */
    protected function __construct()
    {
        $this->LoadConfig();
    }

    /**
     * Запускает весь процесс :)
     *
     */
    public function Exec()
    {
        $this->ParseUrl();
        $this->DefineActionClass(); // Для возможности ДО инициализации модулей определить какой action/event запрошен
        $this->oEngine = Engine::getInstance();
        $this->oEngine->Init();
        $this->ExecAction();
        $this->Shutdown(false);
    }

    /**
     * Завершение работы роутинга
     *
     * @param bool $bExit Принудительно завершить выполнение скрипта
     */
    public function Shutdown($bExit = true)
    {
        $this->AssignVars();
        $this->oEngine->Shutdown();
        LS::Make(ModuleViewer::class)->Display($this->oAction->GetTemplate());
        if ($bExit) {
            exit();
        }
    }

    /**
     * Парсим URL
     * Пример: http://site.ru/action/event/param1/param2/  на выходе получим:
     *  self::$sAction='action';
     *    self::$sActionEvent='event';
     *    self::$aParams=array('param1','param2');
     *
     */
    protected function ParseUrl()
    {
        $sReq = $this->GetRequestUri();
        $aRequestUrl = $this->GetRequestArray($sReq);
        $aRequestUrl = $this->RewriteRequest($aRequestUrl);

        self::$sAction = array_shift($aRequestUrl);
        self::$sActionEvent = array_shift($aRequestUrl);
        self::$aParams = $aRequestUrl;
    }

    /**
     * Метод выполняет первичную обработку $_SERVER['REQUEST_URI']
     *
     * @return string
     */
    protected function GetRequestUri()
    {
        $sReq = preg_replace("/\/+/", '/', $_SERVER['REQUEST_URI']);
        $sReq = preg_replace("/^\/(.*)\/?$/U", '\\1', $sReq);
        $sReq = preg_replace("/^(.*)\?.*$/U", '\\1', $sReq);
        /**
         * Формируем $sPathWebCurrent ДО применения реврайтов
         */
        self::$sPathWebCurrent = Config::Get('path.root.web')."/".join('/', $this->GetRequestArray($sReq));

        return $sReq;
    }

    /**
     * Возвращает массив реквеста
     *
     * @param string $sReq Строка реквеста
     *
     * @return array
     */
    protected function GetRequestArray($sReq)
    {
        $aRequestUrl = ($sReq == '') ? [] : explode('/', $sReq);
        for ($i = 0; $i < Config::Get('path.offset_request_url'); $i++) {
            array_shift($aRequestUrl);
        }
        $aRequestUrl = array_map('urldecode', $aRequestUrl);

        return $aRequestUrl;
    }

    /**
     * Применяет к реквесту правила реврайта из конфига Config::Get('router.uri')
     *
     * @param array $aRequestUrl Массив реквеста
     *
     * @return array
     */
    protected function RewriteRequest($aRequestUrl)
    {
        /**
         * Правила Rewrite для REQUEST_URI
         */
        $sReq = implode('/', $aRequestUrl);
        if ($aRewrite = Config::Get('router.uri')) {
            $sReq = preg_replace(array_keys($aRewrite), array_values($aRewrite), $sReq);
        }

        return ($sReq == '') ? [] : explode('/', $sReq);
    }

    /**
     * Выполняет загрузку конфигов роутинга
     *
     */
    protected function LoadConfig()
    {
        //Конфиг роутинга, содержит соответствия URL и классов экшенов
        $this->aConfigRoute = Config::Get('router');
        // Переписываем конфиг согласно правилу rewrite
        foreach ((array)$this->aConfigRoute['rewrite'] as $sPage => $sRewrite) {
            if (isset($this->aConfigRoute['page'][$sPage])) {
                $this->aConfigRoute['page'][$sRewrite] = $this->aConfigRoute['page'][$sPage];
                unset($this->aConfigRoute['page'][$sPage]);
            }
        }
    }

    /**
     * Загружает в шаблонизатор Smarty необходимые переменные
     *
     */
    protected function AssignVars()
    {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->Assign('sAction', $this->Standart(self::$sAction));
        $viewer->Assign('sEvent', self::$sActionEvent);
        $viewer->Assign('aParams', self::$aParams);
        $viewer->Assign('PATH_WEB_CURRENT', self::$sPathWebCurrent);
    }

    /**
     * Запускает на выполнение экшен
     * Может запускаться рекурсивно если в одном экшене стоит переадресация на
     * другой
     */
    public function ExecAction()
    {
        $this->doNext = false;
        $this->DefineActionClass();
        /** @var \Engine\Modules\ModuleHook $hook */
        $hook = LS::Make(ModuleHook::class);
        /**
         * Сначала запускаем инициализирующий евент
         */
        $hook->Run('init_action');

        $sActionClass = $this->DefineActionClass();

        self::$sActionClass = $sActionClass;

        $sClassName = $sActionClass;
        $this->oAction = new $sClassName($this->oEngine, self::$sAction);
        /**
         * Инициализируем экшен
         */
        $hook->Run("action_init_".strtolower($sActionClass)."_before");
        $this->oAction->Init();
        $hook->Run("action_init_".strtolower($sActionClass)."_after");

        if ($this->doNext) {
            $this->ExecAction();
        } else {
            /**
             * Замеряем время работы action`а
             */
            $oProfiler = ProfilerSimple::getInstance();
            $iTimeId = $oProfiler->Start('ExecAction', self::$sAction);

            $this->oAction->ExecEvent();
            self::$sActionEventName = $this->oAction->GetCurrentEventName();

            $hook->Run("action_shutdown_".strtolower($sActionClass)."_before");
            $this->oAction->EventShutdown();
            $hook->Run("action_shutdown_".strtolower($sActionClass)."_after");

            $oProfiler->Stop($iTimeId);

            if ($this->doNext) {
                $this->ExecAction();
            }
        }
    }

    /**
     * Определяет какой класс соответствует текущему экшену
     *
     * @return string
     */
    protected function DefineActionClass()
    {
        if (isset($this->aConfigRoute['page'][self::$sAction])) {

        } elseif (self::$sAction === null) {
            self::$sAction = $this->aConfigRoute['config']['action_default'];
        } else {
            //Если не находим нужного класса то отправляем на страницу ошибки
            self::$sAction = $this->aConfigRoute['config']['action_not_found'];
            self::$sActionEvent = '404';
        }
        self::$sActionClass = $this->aConfigRoute['page'][self::$sAction];

        return self::$sActionClass;
    }

    /**
     * Функция переадресации на другой экшен
     * Если ею завершить евент в экшене то запуститься новый экшен
     * Пример: <pre>Router::Action('error');</pre>
     *
     * @param string $sAction Экшен
     * @param string $sEvent  Евент
     * @param array  $aParams Список параметров
     */
    static public function Action($sAction, $sEvent = null, $aParams = null)
    {
        self::$sAction = self::getInstance()->Rewrite($sAction);
        self::$sActionEvent = $sEvent;
        if (is_array($aParams)) {
            self::$aParams = $aParams;
        }
        self::getInstance()->doNext = true;
    }

    /**
     * Возвращает текущий ЧПУ url
     *
     * @return string
     */
    static public function GetPathWebCurrent()
    {
        return self::$sPathWebCurrent;
    }

    /**
     * Возвращает текущий экшен
     *
     * @return string
     */
    static public function GetAction()
    {
        return self::getInstance()->Standart(self::$sAction);
    }

    /**
     * Возвращает текущий евент
     *
     * @return string
     */
    static public function GetActionEvent()
    {
        return self::$sActionEvent;
    }

    /**
     * Возвращает имя текущего евента
     *
     * @return string
     */
    static public function GetActionEventName()
    {
        return self::$sActionEventName;
    }

    /**
     * Возвращает класс текущего экшена
     *
     * @return string
     */
    static public function GetActionClass()
    {
        return self::$sActionClass;
    }

    /**
     * Устанавливает новый текущий евент
     *
     * @param string $sEvent Евент
     */
    static public function SetActionEvent($sEvent)
    {
        self::$sActionEvent = $sEvent;
    }

    /**
     * Возвращает параметры(те которые передаются в URL)
     *
     * @return array
     */
    static public function GetParams()
    {
        return self::$aParams;
    }

    /**
     * Возвращает параметр по номеру, если его нет то возвращается null
     * Нумерация параметров начинается нуля
     *
     * @param int        $iOffset
     * @param mixed|null $def
     *
     * @return string
     */
    static public function GetParam($iOffset, $def = null)
    {
        $iOffset = (int)$iOffset;

        return isset(self::$aParams[$iOffset]) ? self::$aParams[$iOffset] : $def;
    }

    /**
     * Устанавливает значение параметра
     *
     * @param int   $iOffset Номер параметра, по идеи может быть не только числом
     * @param mixed $value
     */
    static public function SetParam($iOffset, $value)
    {
        self::$aParams[$iOffset] = $value;
    }

    /**
     * Блокируем копирование/клонирование объекта роутинга
     *
     */
    protected function __clone()
    {

    }

    /**
     * Возвращает правильную адресацию по переданому названию страницы(экшену)
     *
     * @param  string $action Экшен
     *
     * @return string
     */
    static public function GetPath($action)
    {
        // Если пользователь запросил action по умолчанию
        $sPage = ($action == 'default')
            ? self::getInstance()->aConfigRoute['config']['action_default']
            : $action;

        // Смотрим, есть ли правило rewrite
        $sPage = self::getInstance()->Rewrite($sPage);

        return "/$sPage/";
    }

    /**
     * Try to find rewrite rule for given page.
     * On success return rigth page, else return given param.
     *
     * @param  string $sPage
     *
     * @return string
     */
    public function Rewrite($sPage)
    {
        return (isset($this->aConfigRoute['rewrite'][$sPage]))
            ? $this->aConfigRoute['rewrite'][$sPage]
            : $sPage;
    }

    /**
     * Стандартизирует определение внутренних ресурсов.
     *
     * Пытается по переданому экшену найти rewrite rule и
     * вернуть стандартное название ресусрса.
     *
     * @see    Rewrite
     *
     * @param  string $sPage
     *
     * @return string
     */
    public function Standart($sPage)
    {
        $aRewrite = array_flip($this->aConfigRoute['rewrite']);

        return (isset($aRewrite[$sPage]))
            ? $aRewrite[$sPage]
            : $sPage;
    }

    /**
     * Выполняет редирект, предварительно завершая работу Engine
     *
     * @param string $sLocation URL для редиректа
     */
    static public function Location($sLocation)
    {
        self::getInstance()->oEngine->Shutdown();
        func_header_location($sLocation);
    }
}
