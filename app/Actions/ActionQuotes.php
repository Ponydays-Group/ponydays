<?php

namespace App\Actions;

use App\Modules\Quotes\ModuleQuotes;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Viewer\ModuleViewer;
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
		$this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();

		if (LS::Make(ModuleUser::class)->IsAuthorization() && $this->oUserCurrent) {
			$this->SetDefaultEvent('view');
			LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
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
		$iCountQuotes = LS::Make(ModuleQuotes::class)->GetCount();

		// Передан ли номер страницы
		$iPage = 1;
		if($iCountQuotes) {
			$iPage = ctype_digit($this->GetEventMatch(2))
				? $this->GetEventMatch(2)
				: ceil($iCountQuotes / Config::Get('module.quotes.per_page'));
		}
		$aResult = LS::Make(ModuleQuotes::class)->GetQuotesForPage($iPage, Config::Get('module.quotes.per_page'));

		// Формируем постраничность
		$aPaging = LS::Make(ModuleViewer::class)->MakePaging(
			$iCountQuotes,
			$iPage,
			Config::Get('module.quotes.per_page'),
			Config::Get('pagination.pages.count'),
			Router::GetPath('quotes'),
			[]
		);

		// Загружаем в шаблон языковые данные
		LS::Make(ModuleLang::class)->AddLangJs(array (
			'quotes_link', 'quotes_delete_confirm', 'quotes_add',
			'quotes_update', 'quotes_delete', 'quotes_deleted',
			'quotes_updated', 'quotes_added'));

		// Выключаем сайдбар
		LS::Make(ModuleViewer::class)->Assign('noSidebar', true);

		// Передаем в шаблон цитатки
		LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
		LS::Make(ModuleViewer::class)->Assign('aQuotes', $aResult);
		LS::Make(ModuleViewer::class)->Assign('bIsAdmin', $this->IsAdmin());
		LS::Make(ModuleViewer::class)->Assign('iCountQuotes', $iCountQuotes);

		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('quotes_header'));
		$this->SetTemplateAction('index');
		return true;
	}

	protected function EventRandom() {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $aQuote = LS::Make(ModuleQuotes::class)->GetRandomQuote();
        LS::Make(ModuleViewer::class)->AssignAjax("sQuote", $aQuote['data']);
        LS::Make(ModuleViewer::class)->AssignAjax("iId", $aQuote['id']);
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
				LS::Make(ModuleViewer::class)->SetResponseAjax('json');

				if ($iId = LS::Make(ModuleQuotes::class)->addQuote(getRequestStr('data'))) {
					// Подгрузка ID в AJAX-ответ
					LS::Make(ModuleViewer::class)->AssignAjax('id', $iId);
					LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('quotes_added'), LS::Make(ModuleLang::class)->Get('attention'));
					return true;
				}

				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('quotes_error'), LS::Make(ModuleLang::class)->Get('error'));
				return false;

			// Удаление цитаты
			case 'delete':
				LS::Make(ModuleViewer::class)->SetResponseAjax('json');

				if (LS::Make(ModuleQuotes::class)->deleteQuote(getRequestStr('id'))) {
					LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('quotes_deleted'), LS::Make(ModuleLang::class)->Get('attention'));
					return true;
				}

				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
				return false;

			// Изменение цитаты
			case 'update':
				LS::Make(ModuleViewer::class)->SetResponseAjax('json');

				if (LS::Make(ModuleQuotes::class)->updateQuote(getRequestStr('id'), getRequestStr('data'))) {
					LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('quotes_updated'), LS::Make(ModuleLang::class)->Get('attention'));
					return true;
				}

				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('quotes_error'), LS::Make(ModuleLang::class)->Get('error'));
				return false;

			// восстановление удалённой цитаты
			case 'restore':
				LS::Make(ModuleViewer::class)->SetResponseAjax('json');

				if (LS::Make(ModuleQuotes::class)->restoreQuote(getRequestStr('id'))) {
					LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('quotes_added'), LS::Make(ModuleLang::class)->Get('attention'));
					return true;
				}

				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
				return false;


			// Дефолтная страница со списком цитат
			default:
				// Загружаем в шаблон языковые данные
				LS::Make(ModuleLang::class)->AddLangJs(array ('quotes_link', 'quotes_delete_confirm', 'quotes_add', 'quotes_update', 'quotes_delete'));

				// Выключаем сайдбар
				LS::Make(ModuleViewer::class)->Assign('noSidebar', true);

				// Передаем в шаблон цитатки
				LS::Make(ModuleViewer::class)->Assign('aQuotes', LS::Make(ModuleQuotes::class)->getQuotes());
				LS::Make(ModuleViewer::class)->Assign('bIsAdmin', $this->IsAdmin());
				LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('quotes_header'));
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

		$iCountQuotes = LS::Make(ModuleQuotes::class)->GetCount(true);

		LS::Make(ModuleLang::class)->AddLangJs(array ('quotes_add', 'quotes_restore'));

		// Выключаем сайдбар
		LS::Make(ModuleViewer::class)->Assign('noSidebar', true);

		// Передаем в шаблон цитатки
		LS::Make(ModuleViewer::class)->Assign('aQuotes', LS::Make(ModuleQuotes::class)->getDeletedQuotes());
		LS::Make(ModuleViewer::class)->Assign('iCountQuotes', $iCountQuotes);
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('quotes_trash'));
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
		$iPage = LS::Make(ModuleQuotes::class)->getPageById($iQuote);
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
