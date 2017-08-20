<?php

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
			// А можете ли вы админить цитатник? >_>

			$aQuotesAdmins = Config::Get('quotes_admin');
			foreach ($aQuotesAdmins as $iId) {
				if ((int)$this->oUserCurrent->getId() === $iId) {
					$this->SetDefaultEvent('view');
					return "";
				}
			}
		}

		return parent::EventNotFound();
	}


	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent () {
		$this->AddEventPreg('/^(page([1-9]\d{0,5}))?$/i', 'EventView');
		$this->AddEvent('view', 'EventView');
		$this->AddEvent('edit', 'EventEdit');
	}

	protected function EventView (): bool {
		// Передан ли номер страницы
		$iPage = preg_match("/^\d+$/i", $this->GetEventMatch(2)) ? $this->GetEventMatch(2) : 1;

		$aResult = $this->Quotes_getQuotesForPage($iPage, Config::Get('module.quotes.per_page'));
		$iCountQuotes = $this->Quotes_getCount();
		
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
		$this->Lang_AddLangJs(array ('quotes_delete_confirm', 'quotes_add', 'quotes_update', 'quotes_delete'));

		// Выключаем сайдбар
		$this->Viewer_Assign('noSidebar', true);

		// Передаем в шаблон цитатки
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('aQuotes', $aResult);
		$this->Viewer_Assign('iCountQuotes', $iCountQuotes);

		$this->Viewer_AddHtmlTitle($this->Lang_Get('quotes_header'));
		$this->SetTemplateAction('index');
		return true;
	}

	/**
	 * Ивент редактора цитатника
	 *
	 * @return bool
	 */
	protected function EventEdit (): bool {
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

			// Дефолтная страница со списком цитат
			default:
				// Загружаем в шаблон языковые данные
				$this->Lang_AddLangJs(array ('quotes_delete_confirm', 'quotes_add', 'quotes_update', 'quotes_delete'));

				// Выключаем сайдбар
				$this->Viewer_Assign('noSidebar', true);

				// Передаем в шаблон цитатки
				$this->Viewer_Assign('aQuotes', $this->Quotes_getQuotes());
				$this->Viewer_AddHtmlTitle($this->Lang_Get('quotes_header'));
				$this->SetTemplateAction('index');
				return true;
		}

	}

}