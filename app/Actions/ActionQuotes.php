<?php

use Engine\Action;
use Engine\Config;
use Engine\Router;

/**
 * Class ActionQuotes
 *
 * /quotes/ etc
 * Silvman
 */
class ActionQuotes extends Action {
	/**
	 * Текущий пользователь
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent = null;
	/**
	 * Главное меню
	 *
	 * @var string
	 */
	protected $sMenuHeadItemSelect = 'quotes';

	/**
	 * Инициализация
	 *
	 * @return string
	 */
	public function Init () {
		$this->oUserCurrent = $this->User_GetUserCurrent();

		if ($this->User_IsAuthorization() && $this->oUserCurrent) {
			$this->SetDefaultEvent('view');
			$this->Viewer_Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
			return "";
		}
	}


	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent () {
		$this->AddEventPreg('/^(page([1-9]\d{0,5}))?$/i', 'EventView');
		$this->AddEvent('view', 'EventView');
		$this->AddEvent('deleted', 'EventTrash');
        $this->AddEvent('edit', 'EventEdit');
        $this->AddEvent('random', 'EventRandom');
		$this->AddEventPreg('/^([0-9]\d{0,5})?$/i', 'EventFindQuote');

	}

	/**
	 * Эвент просмотра цитатника
	 *
	 * @return bool
	 */
	protected function EventView (): bool {
		$iCountQuotes = $this->Quotes_GetCount();

		// Передан ли номер страницы
		$iPage = 1;
		if($iCountQuotes) {
			$iPage = ctype_digit($this->GetEventMatch(2))
				? $this->GetEventMatch(2)
				: ceil($iCountQuotes / Config::Get('module.quotes.per_page'));
		}
		$aResult = $this->Quotes_GetQuotesForPage($iPage, Config::Get('module.quotes.per_page'));

		// Формируем постраничность
		$aPaging = $this->Viewer_MakePaging(
			$iCountQuotes,
			$iPage,
			Config::Get('module.quotes.per_page'),
			Config::Get('pagination.pages.count'),
			Router::GetPath('quotes'),
			[]
		);

		// Загружаем в шаблон языковые данные
		$this->Lang_AddLangJs(array (
			'quotes_link', 'quotes_delete_confirm', 'quotes_add',
			'quotes_update', 'quotes_delete', 'quotes_deleted',
			'quotes_updated', 'quotes_added'));

		// Выключаем сайдбар
		$this->Viewer_Assign('noSidebar', true);

		// Передаем в шаблон цитатки
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('aQuotes', $aResult);
		$this->Viewer_Assign('bIsAdmin', $this->IsAdmin());
		$this->Viewer_Assign('iCountQuotes', $iCountQuotes);

		$this->Viewer_AddHtmlTitle($this->Lang_Get('quotes_header'));
		$this->SetTemplateAction('index');
		return true;
	}

	protected function EventRandom() {
        $this->Viewer_SetResponseAjax('json');
        $aQuote = $this->Quotes_GetRandomQuote();
        $this->Viewer_AssignAjax("sQuote", $aQuote['data']);
        $this->Viewer_AssignAjax("iId", $aQuote['id']);
        return true;
    }

	/**
	 * Ивент редактора цитатника
	 *
	 * @return bool
	 */
	protected function EventEdit (): bool {
		if (!$this->IsAdmin()) {
			$this->SetTemplateAction('blank');
			echo "Permission denied.";
			return Router::Action('error');
		}

		switch (getRequestStr('action')) {
			// Создаём цитату
			case 'add':
				// Обрабатываем как ajax запрос (json)
				$this->Viewer_SetResponseAjax('json');

				if ($iId = $this->Quotes_addQuote(getRequestStr('data'))) {
					// Подгрузка ID в AJAX-ответ
					$this->Viewer_AssignAjax('id', $iId);
					$this->Message_AddNotice($this->Lang_Get('quotes_added'), $this->Lang_Get('attention'));
					return true;
				}

				$this->Message_AddError($this->Lang_Get('quotes_error'), $this->Lang_Get('error'));
				return false;

			// Удаление цитаты
			case 'delete':
				$this->Viewer_SetResponseAjax('json');

				if ($this->Quotes_deleteQuote(getRequestStr('id'))) {
					$this->Message_AddNotice($this->Lang_Get('quotes_deleted'), $this->Lang_Get('attention'));
					return true;
				}

				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;

			// Изменение цитаты
			case 'update':
				$this->Viewer_SetResponseAjax('json');

				if ($this->Quotes_updateQuote(getRequestStr('id'), getRequestStr('data'))) {
					$this->Message_AddNotice($this->Lang_Get('quotes_updated'), $this->Lang_Get('attention'));
					return true;
				}

				$this->Message_AddError($this->Lang_Get('quotes_error'), $this->Lang_Get('error'));
				return false;

			// восстановление удалённой цитаты
			case 'restore':
				$this->Viewer_SetResponseAjax('json');

				if ($this->Quotes_restoreQuote(getRequestStr('id'))) {
					$this->Message_AddNotice($this->Lang_Get('quotes_added'), $this->Lang_Get('attention'));
					return true;
				}

				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;


			// Дефолтная страница со списком цитат
			default:
				// Загружаем в шаблон языковые данные
				$this->Lang_AddLangJs(array ('quotes_link', 'quotes_delete_confirm', 'quotes_add', 'quotes_update', 'quotes_delete'));

				// Выключаем сайдбар
				$this->Viewer_Assign('noSidebar', true);

				// Передаем в шаблон цитатки
				$this->Viewer_Assign('aQuotes', $this->Quotes_getQuotes());
				$this->Viewer_Assign('bIsAdmin', $this->IsAdmin());
				$this->Viewer_AddHtmlTitle($this->Lang_Get('quotes_header'));
				$this->SetTemplateAction('index');
				return true;
		}
	}

	/**
	 * Эвент корзины
	 *
	 * @return bool|string
	 */
	protected function EventTrash () {
		if (!$this->IsAdmin()) {
			$this->SetTemplateAction('blank');
			return Router::Action('error');
		}

		$iCountQuotes = $this->Quotes_GetCount(true);

		$this->Lang_AddLangJs(array ('quotes_add', 'quotes_restore'));

		// Выключаем сайдбар
		$this->Viewer_Assign('noSidebar', true);

		// Передаем в шаблон цитатки
		$this->Viewer_Assign('aQuotes', $this->Quotes_getDeletedQuotes());
		$this->Viewer_Assign('iCountQuotes', $iCountQuotes);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('quotes_trash'));
		$this->SetTemplateAction('trash');

		return true;
	}

	/**
	 * Проверка, является ли пользователь администратором цитатника
	 *
	 * @return bool
	 */
	protected function IsAdmin (): bool {
	    if (!$this->oUserCurrent) {
	        return false;
        }
	    if ($this->oUserCurrent->isAdministrator()) {
	        return true;
        }
	    return $this->oUserCurrent->hasPrivileges(ModuleUser::USER_PRIV_QUOTES);
	}

	/**
	 * Эвент, определяющий страницу, на которой располагается цитата
	 *
	 * @return bool
	 */
	protected function EventFindQuote (): bool {
		$iQuote = (int)$this->GetEventMatch(1);
		$iPage = $this->Quotes_getPageById($iQuote);
		$this->SetTemplateAction('blank');

		if($iPage) {
			Router::Location(Router::GetPath("quotes") . "page" . $iPage . "/#field_" . $iQuote);
			return true;
		} else {
			Router::Location(Router::GetPath("quotes"));
			return false;
		}
	}

}
