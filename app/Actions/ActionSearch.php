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

namespace App\Actions;

use App\Modules\Comment\ModuleComment;
use App\Modules\Sphinx\ModuleSphinx;
use App\Modules\Topic\ModuleTopic;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Text\ModuleText;
use Engine\Modules\Viewer\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки поиска по сайту через поисковый движок Sphinx
 *
 * @package actions
 * @since 1.0
 */
class ActionSearch extends Action {
	/**
	 * Допустимые типы поиска с параметрами
	 *
	 * @var array
	 */
	protected $sTypesEnabled = array('topics' => array('topic_publish' => 1), 'comments' => array('comment_delete' => 0));
	/**
	 * Массив результата от Сфинкса
	 *
	 * @var null|array
	 */
	protected $aSphinxRes = null;
	/**
	 * Поиск вернул результат или нет
	 *
	 * @var bool
	 */
	protected $bIsResults = FALSE;

	/**
	 * Инициализация
	 */
	public function Init() {
		$this->SetDefaultEvent('index');
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('search'));
	}
	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent() {
		$this->AddEvent('index','EventIndex');
		$this->AddEvent('topics','EventTopics');
		$this->AddEvent('comments','EventComments');
		$this->AddEvent('opensearch','EventOpenSearch');
	}
	/**
	 * Отображение формы поиска
	 */
	function EventIndex(){
	}
	/**
	 * Обработка стандарта для браузеров Open Search
	 */
	function EventOpenSearch(){
		Router::SetIsShowStats(false);
		LS::Make(ModuleViewer::class)->Assign('sAdminMail', Config::Get('sys.mail.from_email'));
	}
	/**
	 * Поиск топиков
	 *
	 */
	function EventTopics(){
		/**
		 * Ищем
		 */
		$aReq = $this->PrepareRequest();
		$aRes = $this->PrepareResults($aReq, Config::Get('module.topic.per_page'));
		if(FALSE === $aRes) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			Router::Action('error'); return;
		}
		/**
		 * Если поиск дал результаты
		 */
		if($this->bIsResults){
			/**
			 * Получаем топик-объекты по списку идентификаторов
			 */
			$aTopics = LS::Make(ModuleTopic::class)->GetTopicsAdditionalData(array_keys($this->aSphinxRes['matches']));
			/**
			 * Конфигурируем парсер jevix
			 */
			LS::Make(ModuleText::class)->LoadJevixConfig('search');
			/**
			 *  Делаем сниппеты
			 */
			foreach($aTopics AS $oTopic){
				/**
				 * Т.к. текст в сниппетах небольшой, то можно прогнать через парсер
				 */
				$oTopic->setTextShort(LS::Make(ModuleText::class)->JevixParser(LS::Make(ModuleSphinx::class)->GetSnippet(
																  $oTopic->getText(),
																  'topics',
																  $aReq['q'],
																  '<span class="searched-item">',
																  '</span>'
															  )));
			}
			/**
			 *  Отправляем данные в шаблон
			 */
			LS::Make(ModuleViewer::class)->Assign('bIsResults', TRUE);
			LS::Make(ModuleViewer::class)->Assign('aRes', $aRes);
			LS::Make(ModuleViewer::class)->Assign('aTopics', $aTopics);
		}
	}
	/**
	 * Поиск комментариев
	 *
	 */
	function EventComments(){
		/**
		 * Ищем
		 */
		$aReq = $this->PrepareRequest();
		$aRes = $this->PrepareResults($aReq, Config::Get('module.comment.per_page'));
		if(FALSE === $aRes) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			Router::Action('error'); return;
		}
		/**
		 * Если поиск дал результаты
		 */
		if($this->bIsResults){
			/**
			 *  Получаем топик-объекты по списку идентификаторов
			 */
			$aComments = LS::Make(ModuleComment::class)->GetCommentsAdditionalData(array_keys($this->aSphinxRes['matches']));
			/**
			 * Конфигурируем парсер jevix
			 */
			LS::Make(ModuleText::class)->LoadJevixConfig('search');
			/**
			 * Делаем сниппеты
			 */
			foreach($aComments AS $oComment){
				$oComment->setText(LS::Make(ModuleText::class)->JevixParser(LS::Make(ModuleSphinx::class)->GetSnippet(
															   htmlspecialchars($oComment->getText()),
															   'comments',
															   $aReq['q'],
															   '<span class="searched-item">',
															   '</span>'
														   )));
			}
			/**
			 *  Отправляем данные в шаблон
			 */
			LS::Make(ModuleViewer::class)->Assign('aRes', $aRes);
			LS::Make(ModuleViewer::class)->Assign('aComments', $aComments);
		}
	}
	/**
	 * Подготовка запроса на поиск
	 *
	 * @return array
	 */
	private function PrepareRequest(){
		$aReq['q'] = getRequestStr('q');
		if (!func_check($aReq['q'],'text', 2, 255)) {
			/**
			 * Если запрос слишком короткий перенаправляем на начальную страницу поиска
			 * Хотя тут лучше показывать юзеру в чем он виноват
			 */
			Router::Location(Router::GetPath('search'));
		}
		$aReq['sType'] = strtolower(Router::GetActionEvent());
		/**
		 * Определяем текущую страницу вывода результата
		 */
		$aReq['iPage'] = intval(preg_replace('#^page([1-9]\d{0,5})$#', '\1', $this->getParam(0)));
		if(!$aReq['iPage']) { $aReq['iPage'] = 1; }
		/**
		 *  Передача данных в шаблонизатор
		 */
		LS::Make(ModuleViewer::class)->Assign('aReq', $aReq);
		return $aReq;
	}
	/**
	 * Поиск и формирование результата
	 *
	 * @param array $aReq
	 * @param int $iLimit
	 * @return array|bool
	 */
	protected function PrepareResults($aReq, $iLimit){
		/**
		 *  Количество результатов по типам
		 */
		foreach($this->sTypesEnabled as $sType => $aExtra){
			$aRes['aCounts'][$sType] = intval(LS::Make(ModuleSphinx::class)->GetNumResultsByType($aReq['q'], $sType, $aExtra));
		}
		if($aRes['aCounts'][$aReq['sType']] == 0){
			/**
			 *  Объектов необходимого типа не найдено
			 */
			unset($this->sTypesEnabled[$aReq['sType']]);
			/**
			 * Проверяем отсальные типы
			 */
			foreach(array_keys($this->sTypesEnabled) as $sType){
				if($aRes['aCounts'][$sType])
					Router::Location(Router::GetPath('search').$sType.'/?q='.$aReq['q']);
			}
		} elseif(($aReq['iPage']-1)*$iLimit <= $aRes['aCounts'][$aReq['sType']]) {
			/**
			 * Ищем
			 */
			$this->aSphinxRes = LS::Make(ModuleSphinx::class)->FindContent(
				$aReq['q'],
				$aReq['sType'],
				($aReq['iPage']-1)*$iLimit,
				$iLimit,
				$this->sTypesEnabled[$aReq['sType']]
			);
			/**
			 * Возможно демон Сфинкса не доступен
			 */
			if (FALSE === $this->aSphinxRes) {
				return FALSE;
			}

			$this->bIsResults = TRUE;
			/**
			 * Формируем постраничный вывод
			 */
			$aPaging = LS::Make(ModuleViewer::class)->MakePaging(
				$aRes['aCounts'][$aReq['sType']],
				$aReq['iPage'],
				$iLimit,
				Config::Get('pagination.pages.count'),
				Router::GetPath('search').$aReq['sType'],
				array(
					'q' => $aReq['q']
				)
			);
			LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
		}

		$this->SetTemplateAction('results');
		LS::Make(ModuleViewer::class)->AddHtmlTitle($aReq['q']);
		LS::Make(ModuleViewer::class)->Assign('bIsResults', $this->bIsResults);
		return $aRes;
	}
}
