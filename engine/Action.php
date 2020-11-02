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
use Engine\Routing\Controller;

/**
 * Абстрактный класс экшена.
 *
 * От этого класса наследуются все экшены в движке.
 * Предоставляет базовые метода для работы с параметрами и шаблоном при запросе страницы в браузере.
 *
 * @package engine
 * @since   1.0
 *
 * @deprecated Use Routing\Controller
 */
abstract class Action extends Controller
{
    /**
     * Список зарегистрированных евентов
     *
     * @var array
     */
    protected $aRegisterEvent = [];
    /**
     * Список параметров из URL
     * <pre>/action/event/param0/param1/../paramN/</pre>
     *
     * @var array
     */
    protected $aParams = [];
    /**
     * Список совпадений по регулярному выражению для евента
     *
     * @var array
     */
    protected $aParamsEventMatch = ['event' => [], 'params' => []];
    /**
     * Объект ядра
     *
     * @var Engine|null
     */
    protected $oEngine = null;
    /**
     * Шаблон экшена
     *
     * @see SetTemplate
     * @see SetTemplateAction
     *
     * @var string|null
     */
    protected $sActionTemplate = null;
    /**
     * Дефолтный евент
     *
     * @see SetDefaultEvent
     *
     * @var string|null
     */
    protected $sDefaultEvent = null;
    /**
     * Текущий евент
     *
     * @var string|null
     */
    protected $sCurrentEvent = null;
    /**
     * Имя текущий евента
     * Позволяет именовать экшены на основе регулярных выражений
     *
     * @var string|null
     */
    protected $sCurrentEventName = null;
    /**
     * Текущий экшен
     *
     * @var null|string
     */
    protected $sCurrentAction = null;

    /**
     * Конструктор
     *
     * @param Engine $oEngine Объект ядра
     * @param string $sAction Название экшена
     *
     * @deprecated
     */
    public function __construct(Engine $oEngine, $sAction)
    {
        $this->RegisterEvent();
        $this->oEngine = $oEngine;
        $this->sCurrentAction = $sAction;
        $this->aParams = Router::GetParams();
    }

    /**
     * Добавляет евент в экшен
     * По сути является оберткой для AddEventPreg(), оставлен для простоты и
     * совместимости с прошлыми версиями ядра
     *
     * @see AddEventPreg
     *
     * @param string $sEventName     Название евента
     * @param callable $sEventFunction Какой метод ему соответствует
     *
     * @throws \Exception
     *
     * @deprecated
     */
    protected function AddEvent($sEventName, $sEventFunction)
    {
        $this->AddEventPreg("/^{$sEventName}$/i", $sEventFunction);
    }

    /**
     * Добавляет евент в экшен, используя регулярное выражение для евента и
     * параметров
     *
     * @throws \Exception
     *
     * @deprecated
     */
    protected function AddEventPreg()
    {
        $iCountArgs = func_num_args();
        if ($iCountArgs < 2) {
            throw new \Exception("Incorrect number of arguments when adding events");
        }
        $aEvent = [];
        /**
         * Последний параметр может быть массивом - содержать имя метода и имя евента(именованный евент)
         * Если указан только метод, то имя будет равным названию метода
         */
        $aNames = (array)func_get_arg($iCountArgs - 1);
        $aEvent['method'] = $aNames[0];
        if (isset($aNames[1])) {
            $aEvent['name'] = $aNames[1];
        } else {
            $aEvent['name'] = $aEvent['method'];
        }
        if (!method_exists($this, $aEvent['method'])) {
            throw new \Exception("Method of the event not found: ".$aEvent['method']);
        }
        $aEvent['preg'] = func_get_arg(0);
        $aEvent['params_preg'] = [];
        for ($i = 1; $i < $iCountArgs - 1; $i++) {
            $aEvent['params_preg'][] = func_get_arg($i);
        }
        $this->aRegisterEvent[] = $aEvent;
    }

    /**
     * Запускает евент на выполнение
     * Если текущий евент не определен то  запускается тот которые определен по умолчанию(default event)
     *
     * @return mixed
     *
     * @deprecated
     */
    public function ExecEvent()
    {
        $this->sCurrentEvent = Router::GetActionEvent();
        if ($this->sCurrentEvent == null) {
            $this->sCurrentEvent = $this->GetDefaultEvent();
            Router::SetActionEvent($this->sCurrentEvent);
        }
        foreach ($this->aRegisterEvent as $aEvent) {
            if (preg_match($aEvent['preg'], $this->sCurrentEvent, $aMatch)) {
                $this->aParamsEventMatch['event'] = $aMatch;
                $this->aParamsEventMatch['params'] = [];
                foreach ($aEvent['params_preg'] as $iKey => $sParamPreg) {
                    if (preg_match($sParamPreg, $this->GetParam($iKey, ''), $aMatch)) {
                        $this->aParamsEventMatch['params'][$iKey] = $aMatch;
                    } else {
                        continue 2;
                    }
                }
                $this->sCurrentEventName = $aEvent['name'];
                /** @var \Engine\Modules\ModuleHook $hook */
                $hook = LS::Make(ModuleHook::class);
                $pars = ['event' => $this->sCurrentEvent, 'params' => $this->GetParams()];
                $hook->Run("action_event_".strtolower($this->sCurrentAction)."_before", $pars);
                $this->oEngine->order(\Closure::fromCallable([$this, $aEvent['method']]));
                $hook->Run("action_event_".strtolower($this->sCurrentAction)."_after", $pars);

                return;
            }
        }
        $this->EventNotFound();

        return;
    }

    /**
     * Устанавливает евент по умолчанию
     *
     * @param string $sEvent Имя евента
     * @deprecated
     */
    public function SetDefaultEvent($sEvent)
    {
        $this->sDefaultEvent = $sEvent;
    }

    /**
     * Получает евент по умолчанию
     *
     * @return string
     * @deprecated
     */
    public function GetDefaultEvent()
    {
        return $this->sDefaultEvent;
    }

    /**
     * Возвращает элементы совпадения по регулярному выражению для евента
     *
     * @param int|null $iItem Номер совпадения
     *
     * @return string|null
     * @deprecated
     */
    protected function GetEventMatch($iItem = null)
    {
        if ($iItem) {
            if (isset($this->aParamsEventMatch['event'][$iItem])) {
                return $this->aParamsEventMatch['event'][$iItem];
            } else {
                return null;
            }
        } else {
            return $this->aParamsEventMatch['event'];
        }
    }

    /**
     * Возвращает элементы совпадения по регулярному выражению для параметров евента
     *
     * @param int      $iParamNum Номер параметра, начинается с нуля
     * @param int|null $iItem     Номер совпадения, начинается с нуля
     *
     * @return string|null
     * @deprecated
     */
    protected function GetParamEventMatch($iParamNum, $iItem = null)
    {
        if (!is_null($iItem)) {
            if (isset($this->aParamsEventMatch['params'][$iParamNum][$iItem])) {
                return $this->aParamsEventMatch['params'][$iParamNum][$iItem];
            } else {
                return null;
            }
        } else {
            if (isset($this->aParamsEventMatch['event'][$iParamNum])) {
                return $this->aParamsEventMatch['event'][$iParamNum];
            } else {
                return null;
            }
        }
    }

    /**
     * Получает параметр из URL по его номеру, если его нет то null
     *
     * @param int $iOffset Номер параметра, начинается с нуля
     *
     * @return mixed
     * @deprecated
     */
    public function GetParam($iOffset, $default = null)
    {
        $iOffset = (int)$iOffset;

        return isset($this->aParams[$iOffset]) ? $this->aParams[$iOffset] : $default;
    }

    /**
     * Получает список параметров из УРЛ
     *
     * @return array
     * @deprecated
     */
    public function GetParams()
    {
        return $this->aParams;
    }


    /**
     * Установить значение параметра(эмуляция параметра в URL).
     * После установки занова считывает параметры из роутера - для корректной работы
     *
     * @param int    $iOffset Номер параметра, но по идеи может быть не только числом
     * @param string $value
     * @deprecated
     */
    public function SetParam($iOffset, $value)
    {
        Router::SetParam($iOffset, $value);
        $this->aParams = Router::GetParams();
    }

    /**
     * Устанавливает какой шаблон выводить
     *
     * @param string $sTemplate Путь до шаблона относительно общего каталога шаблонов
     * @deprecated
     */
    protected function SetTemplate($sTemplate)
    {
        $this->sActionTemplate = $sTemplate;
    }

    /**
     * Устанавливает какой шаблон выводить
     *
     * @param string $sTemplate Путь до шаблона относительно каталога шаблонов экшена
     * @deprecated
     */
    protected function SetTemplateAction($sTemplate)
    {
        $aDelegates = [$this->GetActionClass()];
        $sActionTemplatePath = $sTemplate.'.tpl';
        foreach ($aDelegates as $sAction) {
            if (preg_match('/^Action([\w]+)$/i', $sAction, $aMatches)) {
                $sTemplatePath = 'actions/Action'.ucfirst($aMatches[1]).'/'.$sTemplate.'.tpl';
                $sActionTemplatePath = $sTemplatePath;
            }
        }
        $this->sActionTemplate = $sActionTemplatePath;
    }

    /**
     * Получить шаблон
     * Если шаблон не определен то возвращаем дефолтный шаблон евента: action/{Action}.{event}.tpl
     *
     * @return string
     * @deprecated
     */
    public function GetTemplate()
    {
        if (is_null($this->sActionTemplate)) {
            $this->SetTemplateAction($this->sCurrentEvent);
        }

        return $this->sActionTemplate;
    }

    /**
     * Получить каталог с шаблонами экшена(совпадает с именем класса)
     *
     * @see Router::GetActionClass
     *
     * @return string
     * @deprecated
     */
    public function GetActionClass()
    {
        return get_class_name(get_class($this));
    }

    /**
     * Возвращает имя евента
     *
     * @return null|string
     * @deprecated
     */
    public function GetCurrentEventName()
    {
        return $this->sCurrentEventName;
    }

    /**
     * Вызывается в том случаи если не найден евент который запросили через URL
     * По дефолту происходит перекидывание на страницу ошибки, это можно переопределить в наследнике
     *
     * @see Router::Action
     * @deprecated
     */
    protected function EventNotFound()
    {
        Router::Action('error', '404');
    }

    /**
     * Выполняется при завершение экшена, после вызова основного евента
     *
     * @deprecated
     */
    public function EventShutdown()
    {

    }

    public function shutdown()
    {
        $this->EventShutdown();
    }

    /**
     * Абстрактный метод инициализации экшена
     *
     * @deprecated
     */
    abstract public function Init();

    public function boot() {
        $this->Init();
    }

    /**
     * Абстрактный метод регистрации евентов.
     * В нём необходимо вызывать метод AddEvent($sEventName,$sEventFunction)
     *
     * @deprecated
     */
    abstract protected function RegisterEvent();

}
