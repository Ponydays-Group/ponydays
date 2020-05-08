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

namespace Engine\Modules\Viewer;

use App\Modules\Tools\ModuleTools;
use Engine\Engine;
use Engine\Config;
use Engine\LS;
use Engine\Module;
use Engine\Modules\Hook\ModuleHook;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Security\ModuleSecurity;
use Engine\Router;
use Smarty;

/**
 * Модуль обработки шаблонов используя шаблонизатор Smarty
 *
 * @package engine.modules
 * @since 1.0
 */
class ModuleViewer extends Module {
	/**
	 * Объект Smarty
	 *
	 * @var Smarty
	 */
	protected $oSmarty;
	/**
	 * Коллекция(массив) блоков
	 *
	 * @var array
	 */
	protected $aBlocks=array();
	/**
	 * Массив правил организации блоков
	 *
	 * @var array
	 */
	protected $aBlockRules = array();
	/**
	 * Заголовок HTML страницы
	 *
	 * @var string
	 */
	protected $sHtmlTitle;
	/**
	 * SEO ключевые слова страницы
	 *
	 * @var string
	 */
	protected $sHtmlKeywords;
	/**
	 * SEO описание страницы
	 *
	 * @var string
	 */
	protected $sHtmlDescription;
	/**
	 * Разделитель заголовка HTML страницы
	 *
	 * @var string
	 */
	protected $sHtmlTitleSeparation=' / ';
	/**
	 * Альтернативный адрес страницы по RSS
	 *
	 * @var array
	 */
	protected $aHtmlRssAlternate=null;
	/**
	 * Указание поисковику основного URL страницы, для борьбы с дублями
	 *
	 * @var string
	 */
	protected $sHtmlCanonical;
	/**
	 * Переменные для отдачи при ajax запросе
	 *
	 * @var array
	 */
	protected $aVarsAjax=array();
	/**
	 * Определяет тип ответа при ajax запросе
	 *
	 * @var string
	 */
	protected $sResponseAjax=null;
	/**
	 * Отправляет специфичный для ответа header
	 *
	 * @var bool
	 */
	protected $bResponseSpecificHeader=true;
	/**
	 * Список меню для рендеринга
	 *
	 * @var array
	 */
	protected $aMenu=array();
	/**
	 * Скомпилированные меню
	 *
	 * @var array
	 */
	protected $aMenuFetch=array();

	/**
	 * Инициализация модуля
	 *
	 */
	public function Init($bLocal=false) {
		LS::Make(ModuleHook::class)->Run('viewer_init_start',compact('bLocal'));
		/**
		 * Load template config
		 */
		if (!$bLocal) {
			if(file_exists($sFile = Config::Get('path.smarty.template').'/settings/config/config.php')) {
				Config::LoadFromFile($sFile,false);
			}
		}
		/**
		 * Заголовок HTML страницы
		 */
		$this->sHtmlTitle=Config::Get('view.name');
		/**
		 * SEO ключевые слова страницы
		 */
		$this->sHtmlKeywords=Config::Get('view.keywords');
		/**
		 * SEO описание страницы
		 */
		$this->sHtmlDescription=Config::Get('view.description');

		/**
		 * Создаём объект Smarty и устанавливаем необходимые параметры
		 */
		$this->oSmarty = $this->CreateSmartyObject();
		$this->oSmarty->error_reporting=error_reporting() & ~E_NOTICE; // подавляем NOTICE ошибки - в этом вся прелесть смарти )
		$this->oSmarty->setTemplateDir(array_merge((array)Config::Get('path.smarty.template'),array(Config::Get('path.root.server').'/plugins/')));
		$this->oSmarty->compile_check=Config::Get('smarty.compile_check');
		/**
		 * Для каждого скина устанавливаем свою директорию компиляции шаблонов
		 */
		$sCompilePath = Config::Get('path.smarty.compiled').'/'.Config::Get('view.skin');
		if(!is_dir($sCompilePath)) @mkdir($sCompilePath);
		$this->oSmarty->setCompileDir($sCompilePath);
		$this->oSmarty->setCacheDir(Config::Get('path.smarty.cache'));
		$this->oSmarty->addPluginsDir(array(Config::Get('path.smarty.plug'),'plugins'));
		$this->oSmarty->default_template_handler_func=array($this,'SmartyDefaultTemplateHandler');
	}
	/**
	 * Получает локальную копию модуля
	 *
	 * @return ModuleViewer
	 */
	public function GetLocalViewer() {
		$sClass = __CLASS__;

		$oViewerLocal=new $sClass(Engine::getInstance());
		$oViewerLocal->Init(true);
		$oViewerLocal->VarAssign();
		$oViewerLocal->Assign('aLang',LS::Make(ModuleLang::class)->GetLangMsg());
		return $oViewerLocal;
	}
	/**
	 * Выполняет загрузку необходимый(возможно даже системный :)) переменных в шалон
	 *
	 */
	public function VarAssign() {
		/**
		 * Загружаем весь $_REQUEST, предварительно обработав его функцией func_htmlspecialchars()
		 */
		$aRequest=$_REQUEST;
		func_htmlspecialchars($aRequest);
		$this->Assign("_aRequest",$aRequest);
		/**
		 * Параметры стандартной сессии
		 */
		$this->Assign("_sPhpSessionName",session_name());
		$this->Assign("_sPhpSessionId",session_id());
		if (in_array('SiteStyle', array_keys($_COOKIE))){
			if ($_COOKIE['SiteStyle']=='Dark') {
			    Config::Set("theme", "dark");
			    Config::Set("icon", "sun");
			}
		}
		/**
		 * Short Engine aliases
		 */
		$this->Assign("E", LS::E());
		/**
		 * Загружаем объект доступа к конфигурации
		 */
		$this->Assign("oConfig",Config::getInstance());
		/**
		 * Загружаем роутинг с учетом правил rewrite
		 */
		$aRouter=array();
		$aPages=Config::Get('router.page');

		if(!$aPages or !is_array($aPages)) throw new \Exception('Router rules is underfined.');
		foreach ($aPages as $sPage=>$aAction) {
			$aRouter[$sPage]=Router::GetPath($sPage);
		}
		$this->Assign("aRouter",$aRouter);
		/**
		 * Загружаем в шаблон блоки
		 */
		$this->Assign("aBlocks",$this->aBlocks);
		/**
		 * Загружаем HTML заголовки
		 */
		$this->Assign("sHtmlTitle",htmlspecialchars($this->sHtmlTitle));
		$this->Assign("sHtmlKeywords",htmlspecialchars($this->sHtmlKeywords));
		$this->Assign("sHtmlDescription",htmlspecialchars($this->sHtmlDescription));
		$this->Assign("aHtmlRssAlternate",$this->aHtmlRssAlternate);
		$this->Assign("sHtmlCanonical",LS::Make(ModuleTools::class)->Urlspecialchars($this->sHtmlCanonical));
	}
	/**
	 * Загружаем содержимое menu-контейнеров
	 */
	protected function MenuVarAssign() {
		$this->Assign("aMenuFetch",$this->aMenuFetch);
		$this->Assign("aMenuContainers",array_keys($this->aMenu));
	}

    /**
     * Выводит на экран(браузер) обработанный шаблон
     *
     * @param string $sTemplate Шаблон для вывода
     *
     * @throws \Exception
     */
	public function Display($sTemplate) {
		if ($this->sResponseAjax) {
			$this->DisplayAjax($this->sResponseAjax);
		}
		/**
		 * Если шаблон найден то выводим, иначе ошибка
		 * Предварительно проверяем наличие делегата
		 */
		if ($sTemplate) {
			if ($this->TemplateExists($sTemplate)) {
				$this->oSmarty->display($sTemplate);
			} else {
				throw new \Exception('Can not find the template: '.$sTemplate);
			}
		}
	}
	/**
	 * Возвращает объект Smarty
	 *
	 * @return Smarty
	 */
	public function GetSmartyObject() {
		return $this->oSmarty;
	}
	/**
	 * Создает и возвращает объект Smarty
	 *
	 * @return Smarty
	 */
	public function CreateSmartyObject() {
		return new Smarty();
	}
	/**
	 * Ответ на ajax запрос
	 *
	 * @param string $sType Варианты: json, jsonIframe, jsonp
	 */
	public function DisplayAjax($sType='json') {
		/**
		 * Загружаем статус ответа и сообщение
		 */
		if($sType != 'clear_json') {
			$bStateError = false;
			$sMsgTitle = '';
			$sMsg = '';
			/** @var ModuleMessage $message */
			$message = LS::Make(ModuleMessage::class);
			$aMsgError = $message->GetError();
			$aMsgNotice = $message->GetNotice();
			if (count($aMsgError) > 0) {
				$bStateError = true;
				$sMsgTitle = $aMsgError[0]['title'];
				$sMsg = $aMsgError[0]['msg'];
			} elseif (count($aMsgNotice) > 0) {
				$sMsgTitle = $aMsgNotice[0]['title'];
				$sMsg = $aMsgNotice[0]['msg'];
			}
			$this->AssignAjax('sMsgTitle', $sMsgTitle);
			$this->AssignAjax('sMsg', $sMsg);
			$this->AssignAjax('bStateError', $bStateError);
		}
		if ($sType == 'json' || $sType == 'clear_json') {
			if ($this->bResponseSpecificHeader and !headers_sent()) {
				header('Content-type: application/json');
			}
			echo json_encode($this->aVarsAjax);
		} elseif ($sType=='jsonIframe') {
			// Оборачивает json в тег <textarea>, это не дает браузеру выполнить HTML, который вернул iframe
			if ($this->bResponseSpecificHeader and !headers_sent()) {
				header('Content-type: application/json');
			}
			/**
			 * Избавляемся от бага, когда в возвращаемом тексте есть &quot;
			 */
			echo json_encode($this->aVarsAjax);
		} elseif ($sType=='jsonp') {
			if ($this->bResponseSpecificHeader and !headers_sent()) {
				header('Content-type: application/json');
			}
			echo getRequest('jsonpCallback','callback').'('.json_encode($this->aVarsAjax).');';
		}
		exit();
	}
	/**
	 * Возвращает тип отдачи контекта
	 *
	 * @return string
	 */
	public function GetResponseAjax() {
		return $this->sResponseAjax;
	}
	/**
	 * Устанавливает тип отдачи при ajax запросе, если null то выполняется обычный вывод шаблона в браузер
	 *
	 * @param string $sResponseAjax	Тип ответа
	 * @param bool $bResponseSpecificHeader	Установливать специфичные тиру заголовки через header()
	 * @param bool $bValidate	Производить или нет валидацию формы через {@link Security::ValidateSendForm}
	 */
	public function SetResponseAjax($sResponseAjax='json',$bResponseSpecificHeader=true, $bValidate=true) {
		// Для возможности кросс-доменных запросов
		if ($sResponseAjax!='jsonp' && $bValidate) {
			LS::Make(ModuleSecurity::class)->ValidateSendForm();
		}
		$this->sResponseAjax=$sResponseAjax;
		$this->bResponseSpecificHeader=$bResponseSpecificHeader;
	}

	/**
	 * Устанавливает тип ответа как чистый JSON, без значений по умолчанию, как "sMsg" и "bStateError"
	 */
	public function SetResponseJson() {
		$this->SetResponseAjax('clear_json', true, false);
	}
	/**
	 * Загружает переменную в шаблон
	 *
	 * @param string $sName	Имя переменной в шаблоне
	 * @param mixed $value	Значение переменной
	 */
	public function Assign($sName,$value) {
		$this->oSmarty->assign($sName, $value);
	}
	/**
	 * Загружаем переменную в ajax ответ
	 *
	 * @param string $sName	Имя переменной в шаблоне
	 * @param mixed $value	Значение переменной
	 */
	public function AssignAjax($sName,$value) {
		$this->aVarsAjax[$sName]=$value;
	}
	/**
	 * Возвращает обработанный шаблон
	 *
	 * @param string $sTemplate	Шаблон для рендеринга
	 * @return string
	 */
	public function Fetch($sTemplate) {
		return $this->oSmarty->fetch($sTemplate);
	}
	/**
	 * Проверяет существование шаблона
	 *
	 * @param string $sTemplate	Шаблон
	 * @return bool
	 */
	public function TemplateExists($sTemplate) {
		return $this->oSmarty->templateExists($sTemplate);
	}
	/**
	 * Инициализируем параметры отображения блоков
	 */
	protected function InitBlockParams() {
		if($aRules=Config::Get('block')) {
			$this->aBlockRules=$aRules;
		}
	}
	/**
	 * Добавляет блок для отображения
	 *
	 * @param string $sGroup	Группа блоков
	 * @param string $sName	Название блока
	 * Можно передать название блока, тогда для обработки данных блока будет вызван обработчик из /classes/blocks/, либо передать путь до шаблона, тогда будет выполнено обычное подключение шаблона
	 * @param array  $aParams Параметры блока, которые будут переданы обработчику блока
	 * @param int    $iPriority	Приоритет, согласно которому сортируются блоки
	 * @return bool
	 */
	public function AddBlock($sGroup,$sName,$aParams=array(),$iPriority=5) {
		/**
		 * Если смогли определить тип блока то добавляем его
		 */
		$sType=$this->DefineTypeBlock($sName,isset($aParams['dir'])?$aParams['dir']:null);
		if ($sType=='undefined') {
			return false;
		}
		/**
		 * Если тип "template" и есть параметр "dir", то получаем полный путь до шаблона
		 */
		if ($sType=='template' and isset($aParams['dir'])) {
			$sName=rtrim($aParams['dir'],'/').'/'.ltrim($sName,'/');
		}
		$this->aBlocks[$sGroup][]=array(
			'type'     => $sType,
			'name'     => $sName,
			'params'   => $aParams,
			'priority' => $iPriority,
		);
		return true;
	}
	/**
	 * Добавляет список блоков
	 *
	 * @param string $sGroup	Группа блоков
	 * @param array $aBlocks	Список названий блоков с параметрами
	 * <pre>
	 * LS::Make(ModuleViewer::class)->AddBlocks('right',array('tags',array('block'=>'stream','priority'=>100)));
	 * </pre>
	 * @param bool $ClearBlocks	Очищать или нет перед добавлением блоки в данной группе
	 */
	public function AddBlocks($sGroup,$aBlocks,$ClearBlocks=true) {
		/**
		 * Удаляем ранее добавленые блоки
		 */
		if ($ClearBlocks) {
			$this->ClearBlocks($sGroup);
		}
		foreach ($aBlocks as $sBlock) {
			if (is_array($sBlock)) {
				$this->AddBlock(
					$sGroup,
					$sBlock['block'],
					isset($sBlock['params']) ? $sBlock['params'] : array(),
					isset($sBlock['priority']) ? $sBlock['priority'] : 5
				);
			} else {
				$this->AddBlock($sGroup,$sBlock);
			}
		}
	}
	/**
	 * Удаляет блоки группы
	 *
	 * @param string $sGroup
	 */
	public function ClearBlocks($sGroup) {
		$this->aBlocks[$sGroup]=array();
	}
	/**
	 * Удаляет блоки всех групп
	 *
	 */
	public function ClearBlocksAll() {
		foreach ($this->aBlocks as $sGroup => $aBlock) {
			$this->aBlocks[$sGroup]=array();
		}
	}
	/**
	 * Возвращает список блоков
	 *
	 * @param bool $bSort	Выполнять или нет сортировку блоков
	 * @return array
	 */
	public function GetBlocks($bSort=false) {
		if ($bSort) {
			$this->SortBlocks();
		}
		return $this->aBlocks;
	}

    /**
     * Определяет тип блока
     *
     * @param string      $sName Название блока
     * @param string|null $sDir  Путь до блока, обычно определяется автоматички
     *                           для плагинов, если передать параметр
     *                           'plugin'=>'myplugin'
     *
     * @return string ('block','template','undefined')
     * @throws \Exception
     */
	protected function DefineTypeBlock($sName,$sDir=null) {
		if ($this->TemplateExists(is_null($sDir)?'blocks/block.'.$sName.'.tpl':rtrim($sDir,'/').'/blocks/block.'.$sName.'.tpl')) {
			/**
			 * Если найден шаблон вида block.name.tpl то считаем что тип 'block'
			 */
			return 'block';
		} elseif ($this->TemplateExists(is_null($sDir) ? $sName : rtrim($sDir,'/').'/'.ltrim($sName,'/'))) {
			/**
			 * Если найден шаблон по имени блока то считаем его простым шаблоном
			 */
			return 'template';
		} else {
			/**
			 * Считаем что тип не определен
			 */
			throw new \Exception('Can not find the block`s template: '.$sName);
		}
	}
	/**
	 * Анализируем правила и наборы массивов
	 * получаем окончательные списки блоков
	 */
	protected function BuildBlocks() {
		$sAction = strtolower(Router::GetAction());
		$sEvent  = strtolower(Router::GetActionEvent());
		$sEventName  = strtolower(Router::GetActionEventName());
		foreach($this->aBlockRules as $sName=>$aRule) {
			$bUse=false;
			/**
			 * Если в правиле не указан список блоков, нам такое не нужно
			 */
			if(!array_key_exists('blocks',$aRule)) continue;
			/**
			 * Если не задан action для исполнения и нет ни одного шаблона path,
			 * или текущий не входит в перечисленные в правиле
			 * то выбираем следующее правило
			 */
			if(!array_key_exists('action',$aRule) && !array_key_exists('path',$aRule)) continue;
			if (isset($aRule['action'])) {
				if(in_array($sAction, (array)$aRule['action'])) $bUse=true;
				if(array_key_exists($sAction,(array)$aRule['action'])) {
					/**
					 * Если задан список event`ов и текущий в него не входит,
					 * переходи к следующему действию.
					 */
					foreach ((array)$aRule['action'][$sAction] as $sEventPreg) {
						if(substr($sEventPreg,0,1)=='/') {
							/**
							 * Это регулярное выражение
							 */
							if(preg_match($sEventPreg,$sEvent)) { $bUse=true; break; }
						} elseif (substr($sEventPreg,0,1)=='{') {
							/**
							 * Это имя event'a (именованный евент, если его нет, то совпадает с именем метода евента в экшене)
							 */
							if(trim($sEventPreg,'{}')==$sEventName) {
								$bUse=true;
								break;
							}
						} else {
							/**
							 * Это название event`a
							 */
							if($sEvent==$sEventPreg) { $bUse=true; break; }
						}
					}
				}
			}
			/**
			 * Если не найдено совпадение по паре Action/Event,
			 * переходим к поиску по regexp путей.
			 */
			if(!$bUse && isset($aRule['path'])) {
				$sPath = rtrim(Router::GetPathWebCurrent(),"/");
				/**
				 * Проверяем последовательно каждый regexp
				 */
				foreach((array)$aRule['path'] as $sRulePath) {
					$sPattern = "~".str_replace(array('/','*'),array('\/','[\w\-]+'), $sRulePath)."~";
					if(preg_match($sPattern, $sPath)) {
						$bUse=true;
						break 1;
					}
				}

			}

			if($bUse){
				/**
				 * Если задан режим очистки блоков, сначала чистим старые блоки
				 */
				if(isset($aRule['clear'])) {
					switch (true) {
						/**
						 * Если установлен в true, значит очищаем все
						 */
						case  ($aRule['clear']===true):
							$this->ClearBlocksAll();
							break;

						case is_string($aRule['clear']):
							$this->ClearBlocks($aRule['clear']);
							break;

						case is_array($aRule['clear']):
							foreach ($aRule['clear'] as $sGroup) {
								$this->ClearBlocks($sGroup);
							}
							break;
					}
				}
				/**
				 * Добавляем все блоки, указанные в параметре blocks
				 */
				foreach ($aRule['blocks'] as $sGroup => $aBlocks) {
					foreach ((array)$aBlocks as $sName=>$aParams) {
						/**
						 * Если название блока указывается в параметрах
						 */
						if (is_int($sName)) {
							if (is_array($aParams)) {
								$sName=$aParams['block'];
							}
						}
						/**
						 * Если $aParams не являются массивом, значит передано только имя блока
						 */
						if(!is_array($aParams)) {
							$this->AddBlock($sGroup,$aParams);
						} else {
							$this->AddBlock(
								$sGroup,$sName,
								isset($aParams['params']) ? $aParams['params'] : array(),
								isset($aParams['priority']) ? $aParams['priority'] : 5
							);
						}
					}
				}
			}
		}
	}
	/**
	 * Вспомагательная функция для сортировки блоков по приоритетности
	 *
	 * @param  array $a
	 * @param  array $b
	 * @return int
	 */
	protected function _SortBlocks($a,$b) {
		return ($a["priority"]-$b["priority"]);
	}
	/**
	 * Сортируем блоки
	 *
	 */
	protected function SortBlocks() {
		/**
		 * Сортируем блоки по приоритетности
		 */
		foreach($this->aBlocks as $sGroup=>$aBlocks) {
			uasort($aBlocks,array(&$this,'_SortBlocks'));
			$this->aBlocks[$sGroup] = array_reverse($aBlocks);
		}
	}
	/**
	 * Устанавливаем заголовок страницы(тег title)
	 *
	 * @param string $sText	Заголовок
	 */
	public function SetHtmlTitle($sText) {
		$this->sHtmlTitle=$sText;
	}
	/**
	 * Добавляет часть заголовка страницы через разделитель
	 *
	 * @param string $sText	Заголовок
	 */
	public function AddHtmlTitle($sText) {
		$this->sHtmlTitle=$sText.$this->sHtmlTitleSeparation.$this->sHtmlTitle;
	}
	/**
	 * Возвращает текущий заголовок страницы
	 *
	 * @return string
	 */
	public function GetHtmlTitle() {
		return $this->sHtmlTitle;
	}
	/**
	 * Устанавливает ключевые слова keywords
	 *
	 * @param string $sText	Кейворды
	 */
	public function SetHtmlKeywords($sText) {
		$this->sHtmlKeywords=$sText;
	}
	/**
	 * Устанавливает описание страницы desciption
	 *
	 * @param string $sText	Описание
	 */
	public function SetHtmlDescription($sText) {
		$this->sHtmlDescription=$sText;
	}
	/**
	 * Устанавливает основной адрес страницы
	 *
	 * @param string $sUrl	URL страницы
	 * @param bool $bRewrite	Перезаписывать URL, если он уже установлен
	 */
	public function SetHtmlCanonical($sUrl,$bRewrite=false) {
		if (!$this->sHtmlCanonical or $bRewrite) {
			$this->sHtmlCanonical=$sUrl;
		}
	}
	/**
	 * Устанавливает альтернативный адрес страницы по RSS
	 *
	 * @param string $sUrl	URL
	 * @param string $sTitle	Заголовок
	 */
	public function SetHtmlRssAlternate($sUrl,$sTitle) {
		$this->aHtmlRssAlternate['title']=htmlspecialchars($sTitle);
		$this->aHtmlRssAlternate['url']=htmlspecialchars($sUrl);
	}
	/**
	 * Формирует постраничный вывод
	 *
	 * @param int $iCount	Общее количество элементов
	 * @param int $iCurrentPage	Текущая страница
	 * @param int $iCountPerPage	Количество элементов на одну страницу
	 * @param int $iCountPageLine	Количество ссылок на другие страницы
	 * @param string $sBaseUrl	Базовый URL, к нему будет добавлять постикс /pageN/  и GET параметры
	 * @param array $aGetParamsList	Список GET параметров, которые необходимо передавать при постраничном переходе
	 * @return array
	 */
	public function MakePaging($iCount,$iCurrentPage,$iCountPerPage,$iCountPageLine,$sBaseUrl,$aGetParamsList=array()) {
		if ($iCount==0) {
			return false;
		}

		$iCountPage=ceil($iCount/$iCountPerPage);
		if (!preg_match("/^[1-9]\d*$/i",$iCurrentPage)) {
			$iCurrentPage=1;
		}
		if ($iCurrentPage>$iCountPage) {
			$iCurrentPage=$iCountPage;
		}

		$aPagesLeft=array();
		$iTemp=$iCurrentPage-$iCountPageLine;
		$iTemp = $iTemp<1 ? 1 : $iTemp;
		for ($i=$iTemp;$i<$iCurrentPage;$i++) {
			$aPagesLeft[]=$i;
		}

		$aPagesRight=array();
		for ($i=$iCurrentPage+1;$i<=$iCurrentPage+$iCountPageLine and $i<=$iCountPage;$i++) {
			$aPagesRight[]=$i;
		}

		$iNextPage = $iCurrentPage<$iCountPage ? $iCurrentPage+1 : false;
		$iPrevPage = $iCurrentPage>1 ? $iCurrentPage-1 : false;

		$sGetParams='';
		if (is_string($aGetParamsList) or count($aGetParamsList)){
			$sGetParams='?'.(is_array($aGetParamsList) ? http_build_query($aGetParamsList,'','&') : $aGetParamsList);
		}
		$aPaging=array(
			'aPagesLeft' => $aPagesLeft,
			'aPagesRight' => $aPagesRight,
			'iCount' => $iCount,
			'iCountPage' => $iCountPage,
			'iCurrentPage' => $iCurrentPage,
			'iNextPage' => $iNextPage,
			'iPrevPage' => $iPrevPage,
			'sBaseUrl' => rtrim(LS::Make(ModuleTools::class)->Urlspecialchars($sBaseUrl),'/'),
			'sGetParams' => $sGetParams,
		);
		/**
		 * Избавляемся от дублирования страниц с page=1
		 */
		if ($aPaging['iCurrentPage']==1) {
			$this->SetHtmlCanonical($aPaging['sBaseUrl'].'/'.$aPaging['sGetParams']);
		}
		return $aPaging;
	}
	/**
	 * Добавить меню в контейнер
	 *
	 * @param string $sContainer
	 * @param string $sTemplate
	 */
	public function AddMenu($sContainer, $sTemplate) {
		$this->aMenu[strtolower($sContainer)]=$sTemplate;
	}
	/**
	 * Компилирует меню по контейнерам
	 *
	 */
	protected function BuildMenu() {
		foreach ($this->aMenu as $sContainer=>$sTemplate) {
			$this->aMenuFetch[$sContainer]=$this->Fetch($sTemplate);
		}
	}
	/**
	 * Обработка поиска файла шаблона, если его не смог найти шаблонизатор Smarty
	 *
	 * @param string $sType	Тип шаблона/ресурса
	 * @param string $sName	Имя шаблона - имя файла
	 * @param string $sContent	Возврат содержания шаблона при return true;
	 * @param int $iTimestamp	Возврат даты модификации шаблона при return true;
	 * @param Smarty $oSmarty	Объект Smarty
	 * @return string|bool
	 */
	public function SmartyDefaultTemplateHandler($sType,$sName,&$sContent,&$iTimestamp,$oSmarty) {
		/**
		 * Название шаблона может содержать, как полный путь до файла шаблона, так и относительный любого из каталога в $oSmarty->getTemplateDir()
		 * По дефолту каталоги такие: /templates/skin/[name]/ и /plugins/
		 */
		/**
		 * Задача: если это файл плагина для текущего шаблона, то смотрим этот же файл шаблона плагина в /default/
		 */
		if (Config::Get('view.skin')!='default') {
			// /root/plugins/[plugin name]/templates/skin/[skin name]/dir/test.tpl
			if (preg_match('@^'.preg_quote(Config::Get('path.root.server')).'/plugins/([\w\-_]+)/templates/skin/'.preg_quote(Config::Get('view.skin')).'/@i',$sName,$aMatch)) {
				$sFile=str_replace($aMatch[0],Config::Get('path.root.server').'/plugins/'.$aMatch[1].'/templates/skin/default/',$sName);
				if ($this->TemplateExists($sFile)) {
					return $sFile;
				}
			}
			// [plugin name]/templates/skin/[skin name]/dir/test.tpl
			if (preg_match('@^([\w\-_]+)/templates/skin/'.preg_quote(Config::Get('view.skin')).'/@i',$sName,$aMatch)) {
				$sFile=Config::Get('path.root.server').'/plugins/'.str_replace($aMatch[0],$aMatch[1].'/templates/skin/default/',$sName);
				if ($this->TemplateExists($sFile)) {
					return $sFile;
				}
			}
		}
		return false;
	}
	/**
	 * Загружаем переменные в шаблон при завершении модуля
	 *
	 */
	public function Shutdown() {
		/**
		 * Получаем настройки блоков из конфигов
		 */
		$this->InitBlockParams();
		/**
		 * Добавляем блоки по предзагруженным правилам из конфигов
		 */
		$this->BuildBlocks();

		$this->SortBlocks();

		$this->VarAssign();
		/**
		 * Рендерим меню для шаблонов и передаем контейнеры в шаблон
		 */
		$this->BuildMenu();
		$this->MenuVarAssign();
	}
}
