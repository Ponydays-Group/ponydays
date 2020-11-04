<?php

namespace App\Actions;

use App\Entities\EntityUser;
use App\Modules\ModuleQuotes;
use App\Modules\ModuleUser;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleViewer;
use Engine\Result\Redirect;
use Engine\Result\Result;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Result\View\View;
use Engine\Routing\Controller;
use Engine\Routing\Exception\Http\ForbiddenHttpException;

/**
 * Class ActionQuotes
 *
 * /quotes/ etc
 * Silvman
 */
class ActionQuotes extends Controller
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
    public function boot()
    {
        /**
         * @var ModuleUser $user
         */
        $user = LS::Make(ModuleUser::class);

        $this->oUserCurrent = $user->GetUserCurrent();

        if ($user->IsAuthorization() && $this->oUserCurrent) {
            LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        }
    }

    /**
     * Эвент просмотра цитатника
     *
     * @param \App\Modules\ModuleQuotes    $quotes
     * @param \Engine\Modules\ModuleViewer $viewer
     * @param \Engine\Modules\ModuleLang   $lang
     * @param int                          $page
     *
     * @return \Engine\Result\View\View
     */
    protected function view(ModuleQuotes $quotes, ModuleViewer $viewer, ModuleLang $lang, int $page = 0): View
    {
        $iCountQuotes = $quotes->GetCount();

        // Передан ли номер страницы
        $iPage = 1;
        if ($iCountQuotes) {
            $iPage = $page ?: ceil($iCountQuotes / Config::Get('module.quotes.per_page'));
        }
        $aResult = $quotes->GetQuotesForPage($iPage, Config::Get('module.quotes.per_page'));

        // Формируем постраничность
        $aPaging = $viewer->MakePaging(
            $iCountQuotes,
            $iPage,
            Config::Get('module.quotes.per_page'),
            Config::Get('pagination.pages.count'),
            '/quotes/',
            []
        );

        // Загружаем в шаблон языковые данные
        $lang->AddLangJs([
                'quotes_link',
                'quotes_delete_confirm',
                'quotes_add',
                'quotes_update',
                'quotes_delete',
                'quotes_deleted',
                'quotes_updated',
                'quotes_added'
        ]);

        return HtmlView::by('quotes/index')->with([
            'noSidebar' => true, // Выключаем сайдбар
            'aPaging' => $aPaging,
            'aQuotes' => $aResult,
            'bIsAdmin' => $this->IsAdmin(),
            'iCountQuotes' => $iCountQuotes
        ])->withHtmlTitle($lang->Get('quotes_header'));
    }

    /**
     * @param \Engine\Modules\ModuleViewer $viewer
     * @param \App\Modules\ModuleQuotes    $quotes
     */
    protected function random(ModuleViewer $viewer, ModuleQuotes $quotes)
    {
        $viewer->SetResponseAjax('json');
        $aQuote = $quotes->GetRandomQuote();
        $viewer->AssignAjax("sQuote", $aQuote['data']);
        $viewer->AssignAjax("iId", $aQuote['id']);
    }

    /**
     * Ивент редактора цитатника
     *
     * @param \Engine\Modules\ModuleViewer  $viewer
     * @param \App\Modules\ModuleQuotes     $quotes
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     *
     * @return \Engine\Result\Result
     */
    protected function edit(ModuleViewer $viewer, ModuleQuotes $quotes, ModuleMessage $message, ModuleLang $lang): Result
    {
        if (!$this->IsAdmin()) {
            throw new ForbiddenHttpException();
        }

        switch (getRequestStr('action')) {
            // Создаём цитату
            case 'add':
                if ($iId = $quotes->addQuote(getRequestStr('data'))) {
                    // Подгрузка ID в AJAX-ответ
                    $viewer->AssignAjax('id', $iId);
                    $message->AddNotice($lang->Get('quotes_added'), $lang->Get('attention'));
                } else {
                    $message->AddError($lang->Get('quotes_error'), $lang->Get('error'));
                }

                return AjaxView::empty();

            // Удаление цитаты
            case 'delete':
                if ($quotes->deleteQuote(getRequestStr('id'))) {
                    $message->AddNotice($lang->Get('quotes_deleted'), $lang->Get('attention'));
                } else {
                    $message->AddError($lang->Get('system_error'), $lang->Get('error'));
                }

                return AjaxView::empty();

            // Изменение цитаты
            case 'update':
                if ($quotes->updateQuote(getRequestStr('id'), getRequestStr('data'))) {
                    $message->AddNotice($lang->Get('quotes_updated'), $lang->Get('attention'));
                } else {
                    $message->AddError($lang->Get('quotes_error'), $lang->Get('error'));
                }

                return AjaxView::empty();

            // восстановление удалённой цитаты
            case 'restore':
                if ($quotes->restoreQuote(getRequestStr('id'))) {
                    $message->AddNotice($lang->Get('quotes_added'), $lang->Get('attention'));
                } else {
                    $message->AddError($lang->Get('system_error'), $lang->Get('error'));
                }

                return AjaxView::empty();

            // Дефолтная страница со списком цитат
            default:
                // Загружаем в шаблон языковые данные
                $lang->AddLangJs(
                    ['quotes_link', 'quotes_delete_confirm', 'quotes_add', 'quotes_update', 'quotes_delete']
                );

                return HtmlView::by('quotes/index')->with([
                    'noSidebar' => true,
                    'aQuotes' => $quotes->getQuotes(),
                    'bIsAdmin' => $this->IsAdmin()
                ])->withHtmlTitle($lang->Get('quotes_header'));
        }
    }

    /**
     * Эвент корзины
     *
     * @param \App\Modules\ModuleQuotes  $quotes
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return bool|string
     */
    protected function trash(ModuleQuotes $quotes, ModuleLang $lang)
    {
        if (!$this->IsAdmin()) {
            throw new ForbiddenHttpException();
        }

        $iCountQuotes = $quotes->GetCount(true);

        $lang->AddLangJs(['quotes_add', 'quotes_restore']);

        return HtmlView::by('quotes/trash')->with([
            'noSidebar' => true,
            'aQuotes' => $quotes->getDeletedQuotes(),
            'iCountQuotes' => $iCountQuotes
        ])->withHtmlTitle($lang->Get('quotes_trash'));
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
     * @param \App\Modules\ModuleQuotes $quotes
     * @param int                       $id
     *
     * @return \Engine\Result\Redirect
     */
    protected function findQuote(ModuleQuotes $quotes, int $id): Redirect
    {
        $iPage = $quotes->getPageById($id);

        if ($iPage) {
            return Redirect::to("/quotes/page$iPage/#field_$id");
        } else {
            return Redirect::to("/quotes/");
        }
    }
}
