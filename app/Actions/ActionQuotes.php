<?php

namespace App\Actions;

use App\Entities\EntityUser;
use App\Modules\ModuleQuotes;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Class ActionQuotes
 *
 * /quotes/ etc
 * Silvman
 */
class ActionQuotes extends Action
{
    /**
     * Текущий пользователь
     *
     * @var EntityUser|null
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
     */
    public function Init()
    {
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();

        if (LS::Make(ModuleUser::class)->IsAuthorization() && $this->oUserCurrent) {
            $this->SetDefaultEvent('view');
            LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        }
    }


    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
    {
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
    protected function EventView(): bool
    {
        $iCountQuotes = LS::Make(ModuleQuotes::class)->GetCount();

        // Передан ли номер страницы
        $iPage = 1;
        if ($iCountQuotes) {
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
        LS::Make(ModuleLang::class)->AddLangJs(
            [
                'quotes_link',
                'quotes_delete_confirm',
                'quotes_add',
                'quotes_update',
                'quotes_delete',
                'quotes_deleted',
                'quotes_updated',
                'quotes_added'
            ]
        );

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

    protected function EventRandom()
    {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $aQuote = LS::Make(ModuleQuotes::class)->GetRandomQuote();
        LS::Make(ModuleViewer::class)->AssignAjax("sQuote", $aQuote['data']);
        LS::Make(ModuleViewer::class)->AssignAjax("iId", $aQuote['id']);

        return true;
    }

    /**
     * Ивент редактора цитатника
     */
    protected function EventEdit()
    {
        if (!$this->IsAdmin()) {
            $this->SetTemplateAction('blank');
            echo "Permission denied.";
            Router::Action('error');

            return;
        }

        switch (getRequestStr('action')) {
            // Создаём цитату
            case 'add':
                // Обрабатываем как ajax запрос (json)
                LS::Make(ModuleViewer::class)->SetResponseAjax('json');

                if ($iId = LS::Make(ModuleQuotes::class)->addQuote(getRequestStr('data'))) {
                    // Подгрузка ID в AJAX-ответ
                    LS::Make(ModuleViewer::class)->AssignAjax('id', $iId);
                    LS::Make(ModuleMessage::class)->AddNotice(
                        LS::Make(ModuleLang::class)->Get('quotes_added'),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );

                    return;
                }

                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('quotes_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;

            // Удаление цитаты
            case 'delete':
                LS::Make(ModuleViewer::class)->SetResponseAjax('json');

                if (LS::Make(ModuleQuotes::class)->deleteQuote(getRequestStr('id'))) {
                    LS::Make(ModuleMessage::class)->AddNotice(
                        LS::Make(ModuleLang::class)->Get('quotes_deleted'),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );

                    return;
                }

                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;

            // Изменение цитаты
            case 'update':
                LS::Make(ModuleViewer::class)->SetResponseAjax('json');

                if (LS::Make(ModuleQuotes::class)->updateQuote(getRequestStr('id'), getRequestStr('data'))) {
                    LS::Make(ModuleMessage::class)->AddNotice(
                        LS::Make(ModuleLang::class)->Get('quotes_updated'),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );

                    return;
                }

                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('quotes_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;

            // восстановление удалённой цитаты
            case 'restore':
                LS::Make(ModuleViewer::class)->SetResponseAjax('json');

                if (LS::Make(ModuleQuotes::class)->restoreQuote(getRequestStr('id'))) {
                    LS::Make(ModuleMessage::class)->AddNotice(
                        LS::Make(ModuleLang::class)->Get('quotes_added'),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );

                    return;
                }

                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;


            // Дефолтная страница со списком цитат
            default:
                // Загружаем в шаблон языковые данные
                LS::Make(ModuleLang::class)->AddLangJs(
                    ['quotes_link', 'quotes_delete_confirm', 'quotes_add', 'quotes_update', 'quotes_delete']
                );

                // Выключаем сайдбар
                LS::Make(ModuleViewer::class)->Assign('noSidebar', true);

                // Передаем в шаблон цитатки
                LS::Make(ModuleViewer::class)->Assign('aQuotes', LS::Make(ModuleQuotes::class)->getQuotes());
                LS::Make(ModuleViewer::class)->Assign('bIsAdmin', $this->IsAdmin());
                LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('quotes_header'));
                $this->SetTemplateAction('index');

                return;
        }
    }

    /**
     * Эвент корзины
     *
     * @return bool|string
     */
    protected function EventTrash()
    {
        if (!$this->IsAdmin()) {
            $this->SetTemplateAction('blank');
            Router::Action('error');

            return;
        }

        $iCountQuotes = LS::Make(ModuleQuotes::class)->GetCount(true);

        LS::Make(ModuleLang::class)->AddLangJs(['quotes_add', 'quotes_restore']);

        // Выключаем сайдбар
        LS::Make(ModuleViewer::class)->Assign('noSidebar', true);

        // Передаем в шаблон цитатки
        LS::Make(ModuleViewer::class)->Assign('aQuotes', LS::Make(ModuleQuotes::class)->getDeletedQuotes());
        LS::Make(ModuleViewer::class)->Assign('iCountQuotes', $iCountQuotes);
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('quotes_trash'));
        $this->SetTemplateAction('trash');

        return;
    }

    /**
     * Проверка, является ли пользователь администратором цитатника
     * TODO: Move to ACL
     *
     * @return bool
     */
    protected function IsAdmin(): bool
    {
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
    protected function EventFindQuote(): bool
    {
        $iQuote = (int)$this->GetEventMatch(1);
        $iPage = LS::Make(ModuleQuotes::class)->getPageById($iQuote);
        $this->SetTemplateAction('blank');

        if ($iPage) {
            Router::Location(Router::GetPath("quotes")."page".$iPage."/#field_".$iQuote);

            return true;
        } else {
            Router::Location(Router::GetPath("quotes"));

            return false;
        }
    }

}
