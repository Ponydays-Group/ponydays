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

use App\Entities\EntityUser;
use App\Modules\ModuleUser;
use DbSimple_Mysql;
use Engine\Modules\ModuleCache;
use Engine\Modules\ModuleDatabase;
use Engine\Modules\ModuleHook;
use ReflectionFunction;

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__));

/**
 * Основной класс движка. Ядро.
 *
 * Производит инициализацию плагинов, модулей, хуков.
 * Через этот класс происходит выполнение методов всех модулей, которые вызываются как
 * <pre>$this->Module_Method();</pre> Также отвечает за автозагрузку остальных классов движка.
 *
 * В произвольном месте (не в классах движка у которых нет обработки метода __call() на выполнение модулей) метод
 * модуля можно вызвать так:
 * <pre>
 * Engine::getInstance()->Module_Method();
 * </pre>
 *
 * @package engine
 * @since   1.0
 */
class Engine extends LsObject
{
    /**
     * Текущий экземпляр движка, используется для синглтона.
     *
     * @see getInstance использование синглтона
     *
     * @var Engine
     */
    static protected $oInstance = null;
    /**
     * Список загруженных модулей
     *
     * @var array
     */
    protected $aModules = [];
    /**
     * Время загрузки модулей в микросекундах
     *
     * @var int
     */
    public $iTimeLoadModule = 0;
    /**
     * Текущее время в микросекундах на момент инициализации ядра(движка).
     * Определается так:
     * <pre>
     * $this->iTimeInit=microtime(true);
     * </pre>
     *
     * @var int|null
     */
    protected $iTimeInit = null;


    /**
     * Вызывается при создании объекта ядра.
     * Устанавливает время старта инициализации и обрабатывает входные параметры PHP
     *
     */
    protected function __construct()
    {
        $this->iTimeInit = microtime(true);
        if (get_magic_quotes_gpc()) {
            func_stripslashes($_REQUEST);
            func_stripslashes($_GET);
            func_stripslashes($_POST);
            func_stripslashes($_COOKIE);
        }
    }

    /**
     * Ограничиваем объект только одним экземпляром.
     * Функционал синглтона.
     *
     * Используется так:
     * <pre>
     * Engine::getInstance()->Module_Method();
     * </pre>
     *
     * @return Engine
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
     * Инициализация ядра движка
     *
     */
    public function Init()
    {
        /**
         * Инициализируем хуки
         */
        $this->InitHooks();
        /**
         * Загружаем модули автозагрузки
         */
        $this->LoadModules();
        /**
         * Запускаем хуки для события завершения инициализации Engine
         */
        /** @var \Engine\Modules\ModuleHook $hook */
        $hook = $this->make(ModuleHook::class);
        $hook->Run('engine_init_complete');
    }

    public function Start()
    {
        $router = \Engine\Routing\Router::getInstance();
        $router->init();
        $this->Init();

        $router->route();

        Router::getInstance()->Shutdown(false);
    }

    /**
     * Завершение работы движка
     * Завершает все модули.
     *
     */
    public function Shutdown()
    {
        $this->ShutdownModules();
    }

    /**
     * Инициализирует модуль
     *
     * @param Module $oModule Объект модуля
     */
    protected function InitModule($oModule)
    {
        $oModule->Init();
        $oModule->SetInit();
    }

    /**
     * Проверяет модуль на инициализацию
     *
     * @param string $sModuleClass Класс модуля
     *
     * @return bool
     */
    public function isInitModule($sModuleClass)
    {
        if (isset($this->aModules[$sModuleClass]) and $this->aModules[$sModuleClass]->isInit()) {
            return true;
        }

        return false;
    }

    /**
     * Завершаем работу всех модулей
     *
     */
    protected function ShutdownModules()
    {
        foreach ($this->aModules as $sKey => $oModule) {
            /**
             * Замеряем время shutdown`a модуля
             */
            $oProfiler = ProfilerSimple::getInstance();
            $iTimeId = $oProfiler->Start('ShutdownModule', get_class($oModule));

            $oModule->Shutdown();

            $oProfiler->Stop($iTimeId);
        }
    }

    /**
     * Загружает модули из авто-загрузки и передает им в конструктор ядро
     *
     */
    protected function LoadModules()
    {
        foreach (Config::Get('module.autoload') as $sModuleClass) {
            $this->make($sModuleClass);
        }
    }

    /**
     * Регистрирует хуки из /classes/hooks/
     *
     */
    protected function InitHooks()
    {
        $hookList = Config::Get('sys.hooks');

        foreach ($hookList as $hook) {
            /** @var Hook $oHook */
            $oHook = new $hook();
            $oHook->RegisterHook();
        }
    }

    /**
     * Проверяет файл на существование, если используется кеширование memcache то кеширует результат работы
     *
     * @param  string $sFile Полный путь до файла
     * @param  int    $iTime Время жизни кеша
     *
     * @return bool
     */
    public function isFileExists($sFile, $iTime = 3600)
    {
        //FIXME: пока так
        return file_exists($sFile);

        /** @noinspection PhpUnreachableStatementInspection */
        if (
            !$this->isInit('cache')
            || !Config::Get('sys.cache.use')
            || Config::Get('sys.cache.type') != 'memory'
        ) {
            return file_exists($sFile);
        }

        /** @var ModuleCache $cache */
        $cache = $this->make(ModuleCache::class);
        if (false === ($data = $cache->Get("file_exists_{$sFile}"))) {
            $data = file_exists($sFile);
            $cache->Set((int)$data, "file_exists_{$sFile}", [], $iTime);
        }

        return $data;
    }

    /**
     * Возвращает время старта выполнения движка в микросекундах
     *
     * @return int
     */
    public function GetTimeInit()
    {
        return $this->iTimeInit;
    }

    /**
     * Блокируем копирование/клонирование объекта ядра
     *
     */
    protected function __clone() { }

    public static function MakeMapper($class, $connect = null)
    {
        if (!class_exists($class)) {
            throw new \RuntimeException(sprintf('Class "%s" not found!', $class));
        }
        if (!$connect) {
            /** @var ModuleDatabase $db */
            $db = LS::Make(ModuleDatabase::class);
            $connect = $db->GetConnect();
        }

        return new $class($connect);
    }

    public function resolve(string $type, string $name = ''): array
    {
        if (isset($this->aModules[$type])) {
            return [$this->aModules[$type], true];
        } else {
            if (! class_exists($type)) {
                return [null, false];
            }
            $module = new $type();
            $this->aModules[$type] = $module;
            $this->InitModule($module);

            return [$module, true];
        }
    }

    public function make(string $class): Module
    {
        [$module, $resolved] = $this->resolve($class);
        if (! $resolved) {
            throw new \RuntimeException(sprintf('Module "%s" not found!', $class));
        }
        return $module;
    }

    /**
     * @param callable $func
     *
     * @throws \ReflectionException
     */
    public function order(callable $func)
    {
        CallResolver::resolve($func)->with([$this, 'resolve'])->call();
    }
}

/**
 * Короткий алиас для вызова основных методов движка
 *
 * @package engine
 * @since   1.0
 */
class LS extends LsObject
{
    /**
     * Возвращает ядро
     *
     * @see Engine::GetInstance
     *
     * @return Engine
     */
    static public function E()
    {
        return Engine::GetInstance();
    }

    /**
     * Возвращает объект маппера
     *
     * @see Engine::MakeMapper
     *
     * @param string              $sClassName Класс модуля маппера
     * @param DbSimple_Mysql|null $oConnect   Объект коннекта к БД
     *
     * @return mixed
     */
    static public function Mpr($sClassName, $oConnect = null)
    {
        return Engine::MakeMapper($sClassName, $oConnect);
    }

    /**
     * Возвращает текущего авторизованного пользователя
     *
     * @see ModuleUser::GetUserCurrent
     *
     * @return \App\Entities\EntityUser
     */
    static public function CurUsr()
    {
        return self::Make(ModuleUser::class)->GetUserCurrent();
    }

    /**
     * Возвращает true если текущий пользователь администратор
     *
     * @see ModuleUser::GetUserCurrent
     * @see \App\Entities\EntityUser::isAdministrator
     *
     * @return bool
     */
    static public function Adm()
    {
        return self::CurUsr() && self::CurUsr()->isAdministrator();
    }

    static public function Make(string $class): Module
    {
        return self::E()->make($class);
    }

    static public function Order(callable $func)
    {
        self::E()->order($func);
    }
}