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
	protected $oUserCurrent=null;
	/**
	 * Главное меню
	 *
	 * @var string
	 */
	protected $sMenuHeadItemSelect='quotes';

	/**
	 * Инициализация
	 *
	 * @return string
	 */
	public function Init() {
		$this->oUserCurrent=$this->User_GetUserCurrent();

		if ($this->User_IsAuthorization() && $this->oUserCurrent) {
			// А можете ли вы админить цитатник? >_>

			$aQuotesAdmins = Config::Get('quotes_admin');
			foreach ($aQuotesAdmins as $iId) {
				if ((int)$this->oUserCurrent->getId() === $iId) {
					$this->SetDefaultEvent('list');
					return "";
				}
			}
		}

		return parent::EventNotFound();
	}


	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent() {
		$this->AddEvent('list','EventList');
		$this->AddEvent('upload','EventUpload');

		// TODO extra
		// $this->AddEvent('view','EventView');
	}

	/**
	 * Ивент редактора цитатника
	 *
	 * @return bool
	 */
	protected function EventList() : bool {
		switch(getRequestStr('action')) {
			// Создаём цитату
			case 'add':
				// Обрабатываем как ajax запрос (json)
				$this->Viewer_SetResponseAjax('json');

				if($iId = $this->Quotes_addQuote(getRequestStr('data'))) {
					// Подгрузка ID в AJAX-ответ
					$this->Viewer_AssignAjax('id', $iId);
					$this->Message_AddNotice($this->Lang_Get('quotes_added'),$this->Lang_Get('attention'));
					return true;
				}

				$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return false;

			// Удаление цитаты
			case 'delete':
				$this->Viewer_SetResponseAjax('json');

				if ($this->Quotes_deleteQuote(getRequestStr('id'))) {
					$this->Message_AddNotice($this->Lang_Get('quotes_deleted'),$this->Lang_Get('attention'));
					return true;
				}

				$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return false;

			// Изменение цитаты
			case 'update':
				$this->Viewer_SetResponseAjax('json');

				if($this->Quotes_updateQuote(getRequestStr('id'), getRequestStr('data'))) {
					$this->Message_AddNotice($this->Lang_Get('quotes_updated'),$this->Lang_Get('attention'));
					return true;
				}

				$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return false;

			// Дефолтная страница со списком цитат
			default:
				// Загружаем в шаблон языковые данные
				$this->Lang_AddLangJs(array('quotes_delete_confirm', 'quotes_add', 'quotes_update', 'quotes_delete'));

				// Выключаем сайдбар
				$this->Viewer_Assign('noSidebar', true);

				// Передаем в шаблон цитатки
				$this->Viewer_Assign('aQuotes', $this->Quotes_getQuotes());
				$this->SetTemplateAction('quotes_list');
				return true;
		}
	}

	// Только для заливки из старого цитатника, будет удалено
	protected function EventUpload (): bool {
		include('templates/skin/developer/bquote.php');

		foreach ($quote as $data) {
			echo "fir";
			$this->Quotes_addQuote($data);
		}

		echo "Success!";
		return true;
	}

}