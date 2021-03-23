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

use App\Entities\EntityUserField;
use App\Modules\ModuleBlog;
use App\Modules\ModuleComment;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleCache;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleSecurity;
use Engine\Result\Traits\Messages;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Result\View\View;
use Engine\Routing\Controller;
use Engine\Routing\Exception\Http\NotFoundHttpException;

/**
 * Экшен обработки УРЛа вида /admin/
 * TODO: very very bad
 * @package actions
 * @since   1.0
 */
class ActionAdmin extends Controller
{
    /**
     * Текущий пользователь
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $currentUser = null;
    /**
     * Главное меню
     *
     * @var string
     */
    protected $sMenuHeadItemSelect = 'admin';

    /**
     * Инициализация
     */
    public function boot()
    {
        /**
         * Если нет прав доступа - перекидываем на 404 страницу
         */
        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);
        if (!$user->IsAuthorization() or !$oUserCurrent = $user->GetUserCurrent() or !$oUserCurrent->isAdministrator()) {
            throw new NotFoundHttpException();
        } else {
            $this->currentUser = $oUserCurrent;
        }
    }

    protected function eventSaveUser(ModuleUser $User): AjaxView
    {
        if (!$oUser = $User->GetUserById(getRequest('user_id'))) {
            return AjaxView::empty();
        }
        $sRank = getRequest('user_rank');
        $sMail = getRequest('user_mail');
        $sLogin = getRequest('user_login');
        $oUser->setRank($sRank);
        $oUser->setMail($sMail);
        $oUser->setLogin($sLogin);
        $iPrivs = 0;
        if (getRequest('user_privileges_moderator') == 'on') {
            $iPrivs |= ModuleUser::USER_PRIV_MODERATOR;
        }
        if (getRequest('user_privileges_quotes') == 'on') {
            $iPrivs |= ModuleUser::USER_PRIV_QUOTES;
        }
        $User->SetUserPrivileges($oUser->getId(), $iPrivs);
        $User->Update($oUser);

        return AjaxView::empty();
    }

    protected function eventAdminConfigSave(): AjaxView
    {
        $values = getRequest('values');
        //TODO: too bad
        Config::LoadFromFile(dirname(__FILE__)."/../../config/local.config.json", true, 'adminsave');
        foreach ($values as $key => $val) {
            if ($val == "1") {
                $val = true;
            }
            if ($val == "0") {
                $val = false;
            }
            Config::Set($key, $val, 'adminsave');
        }
        file_put_contents(
            dirname(__FILE__)."/../../config/local.config.json",
            json_encode(
                Config::getInstance('adminsave')->aConfig,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );

        return AjaxView::empty();
    }

    protected function eventAdminConfig()
    {
        $params = [
            "sep1"                   => ["type" => "separator", "description" => "Настройки сайта"],
            "general.close"          => [
                "type"        => "bool",
                "description" => "Закрытый режим работы сайта",
            ],
            "general.reg.invite"     => [
                "type"        => "bool",
                "description" => "Регистрация по инвайтам",
            ],
            "general.reg.activation" => [
                "type"        => "bool",
                "description" => "Активация по почте",
            ],
            "view.name"              => [
                "type"        => "string",
                "description" => "Название сайта",
            ],
            "path.root.web"          => [
                "type"        => "string",
                "description" => "URL сайта",
            ],
            "sep2"                   => ["type" => "separator", "description" => "Картинки"],
            "module.image.use_anon"  => [
                "type"        => "bool",
                "description" => "Использовать анонимайзер при загрузке изображений",
            ],
            "sep"                    => ["type" => "separator", "description" => "БД"],
            "db.params.host"         => [
                "type"        => "string",
                "description" => "Хост БД",
            ],
            "db.params.port"         => [
                "type"        => "int",
                "description" => "Порт БД",
            ],
            "db.params.user"         => [
                "type"        => "string",
                "description" => "Пользователь БД",
            ],
            "db.params.pass"         => [
                "type"        => "password",
                "description" => "Пароль БД",
            ],
            "db.params.type"         => [
                "type"        => "string",
                "description" => "Тип БД",
            ],
            "db.params.dbname"       => [
                "type"        => "string",
                "description" => "Название базы",
            ],
            "sep3"                   => ["type" => "separator", "description" => "Модераторы"],
            "moderator"              => [
                "type"        => "list",
                "description" => "Модераторы",
            ],
            "sep4"                   => ["type" => "separator", "description" => "Почтовик"],
            "sys.mail.from_email"    => [
                "type"        => "string",
                "description" => "Адрес для почтовика",
            ],
            "sys.mail.from_name"     => [
                "type"        => "string",
                "description" => "Имя отправителя в почтовике",
            ],
            "sep5"                   => ["type" => "separator", "description" => "Комментарии"],
            "module.comment.bad"     => [
                "type"        => "int",
                "description" => "Порог скрытия комментария",
            ],
        ];

        return HtmlView::by('admin/config')->with(["aConfig" => $params, 'sMenuHeadItemSelect' => $this->sMenuHeadItemSelect]);
    }

    /**
     * Отображение главной страницы админки
     * Нет никакой логики, просто отображение дефолтного шаблона евента index.tpl
     */
    protected function eventIndex(): HtmlView
    {
        return HtmlView::by('admin/index')->with(['sMenuHeadItemSelect' => $this->sMenuHeadItemSelect]);
    }

    /**
     * Перестроение дерева комментариев, актуально при $config['module']['comment']['use_nested'] = true;
     *
     * @param \App\Modules\ModuleComment     $Comment
     * @param \Engine\Modules\ModuleSecurity $Security
     * @param \Engine\Modules\ModuleCache    $Cache
     * @param \Engine\Modules\ModuleLang     $Lang
     *
     * @return \Engine\Result\View\HtmlView
     */
    protected function eventRestoreComment(ModuleComment $Comment, ModuleSecurity $Security, ModuleCache $Cache, ModuleLang $Lang): HtmlView
    {
        $Security->ValidateSendForm();
        set_time_limit(0); // TODO: very bad
        $Comment->RestoreTree();
        $Cache->Clean();

        return HtmlView::by('admin/index')->with(['sMenuHeadItemSelect' => $this->sMenuHeadItemSelect])->msgNotice($Lang->Get('admin_comment_restore_tree'), $Lang->Get('attention'));
    }

    /**
     * Пересчет счетчика избранных
     *
     * @param \App\Modules\ModuleComment     $Comment
     * @param \App\Modules\ModuleTopic       $Topic
     * @param \Engine\Modules\ModuleSecurity $Security
     * @param \Engine\Modules\ModuleCache    $Cache
     * @param \Engine\Modules\ModuleLang     $Lang
     *
     * @return \Engine\Result\View\HtmlView
     */
    protected function eventRecalculateFavourite(ModuleComment $Comment, ModuleTopic $Topic, ModuleSecurity $Security, ModuleCache $Cache, ModuleLang $Lang): HtmlView
    {
        $Security->ValidateSendForm();
        set_time_limit(0);
        $Comment->RecalculateFavourite();
        $Topic->RecalculateFavourite();
        $Cache->Clean();

        return HtmlView::by('admin/index')->with(['sMenuHeadItemSelect' => $this->sMenuHeadItemSelect])->msgNotice($Lang->Get('admin_favourites_recalculated'), $Lang->Get('attention'));
    }

    /**
     * Пересчет счетчика голосований
     *
     * @param \App\Modules\ModuleTopic $Topic
     * @param \Engine\Modules\ModuleSecurity $Security
     * @param \Engine\Modules\ModuleCache $Cache
     * @param \Engine\Modules\ModuleLang $Lang
     *
     * @return \Engine\Result\View\HtmlView
     */
    protected function eventRecalculateVote(ModuleTopic $Topic, ModuleSecurity $Security, ModuleCache $Cache, ModuleLang $Lang): HtmlView
    {
        $Security->ValidateSendForm();
        set_time_limit(0);
        $Topic->RecalculateVote();
        $Cache->Clean();

        return HtmlView::by('admin/index')->with(['sMenuHeadItemSelect' => $this->sMenuHeadItemSelect])->msgNotice($Lang->Get('admin_votes_recalculated'), $Lang->Get('attention'));
    }

    /**
     * Пересчет количества топиков в блогах
     *
     * @param \App\Modules\ModuleBlog        $Blog
     * @param \Engine\Modules\ModuleSecurity $Security
     * @param \Engine\Modules\ModuleCache    $Cache
     * @param \Engine\Modules\ModuleLang     $Lang
     *
     * @return \Engine\Result\View\HtmlView
     */
    protected function eventRecalculateTopic(ModuleBlog $Blog, ModuleSecurity $Security, ModuleCache $Cache, ModuleLang $Lang): HtmlView
    {
        $Security->ValidateSendForm();
        set_time_limit(0);
        $Blog->RecalculateCountTopic();
        $Cache->Clean();

        return HtmlView::by('admin/index')->msgNotice($Lang->Get('admin_topics_recalculated'), $Lang->Get('attention'));
    }

    /**
     * Управление полями пользователя
     *
     * @param \App\Modules\ModuleUser    $User
     * @param \Engine\Modules\ModuleLang $Lang
     *
     * @return \Engine\Result\View\View
     */
    protected function eventUserFields(ModuleUser $User, ModuleLang $Lang): View
    {
        switch (getRequestStr('action')) {
            /**
             * Создание нового поля
             */
            case 'add':
                $view = AjaxView::empty();
                if (!$this->checkUserField($view)) {
                    return $view;
                }
                $oField = new EntityUserField();
                $oField->setName(getRequestStr('name'));
                $oField->setTitle(getRequestStr('title'));
                $oField->setPattern(getRequestStr('pattern'));
                if (in_array(getRequestStr('type'), $User->GetUserFieldTypes())) {
                    $oField->setType(getRequestStr('type'));
                } else {
                    $oField->setType('');
                }

                $iId = $User->addUserField($oField);
                if (!$iId) {
                    return $view->msgError($Lang->Get('system_error'), $Lang->Get('error'));
                }

                return $view->with([
                    'id' => $iId,
                    'lang_delete' => $Lang->Get('user_field_delete'),
                    'lang_edit' => $Lang->Get('user_field_update')
                ])->msgNotice($Lang->Get('user_field_added'), $Lang->Get('attention'));
            /**
             * Удаление поля
             */
            case 'delete':
                if (!getRequestStr('id')) {
                    return AjaxView::empty()->msgError($Lang->Get('system_error'), $Lang->Get('error'));
                }
                $User->deleteUserField(getRequestStr('id'));

                return AjaxView::empty()->msgNotice($Lang->Get('user_field_deleted'), $Lang->Get('attention'));
            /**
             * Изменение поля
             */
            case 'update':
                $view = AjaxView::empty();
                if (!getRequestStr('id')) {
                    return $view->msgError($Lang->Get('system_error'), $Lang->Get('error'));
                }
                if (!$User->userFieldExistsById(getRequestStr('id'))) {
                    return $view->msgError($Lang->Get('system_error'), $Lang->Get('error'));
                }
                if (!$this->checkUserField($view)) {
                    return $view;
                }
                $oField = new EntityUserField;
                $oField->setId(getRequestStr('id'));
                $oField->setName(getRequestStr('name'));
                $oField->setTitle(getRequestStr('title'));
                $oField->setPattern(getRequestStr('pattern'));
                if (in_array(getRequestStr('type'), $User->GetUserFieldTypes())) {
                    $oField->setType(getRequestStr('type'));
                } else {
                    $oField->setType('');
                }

                if ($User->updateUserField($oField)) {
                    return $view->msgError($Lang->Get('system_error'), $Lang->Get('error'));
                }

                return $view->msgNotice($Lang->Get('user_field_updated'), $Lang->Get('attention'));
            /**
             * Показываем страницу со списком полей
             */
            default:
                /**
                 * Загружаем в шаблон JS текстовки
                 */
                $Lang->AddLangJs(['user_field_delete_confirm']);
                /**
                 * Получаем список всех полей
                 */
                return HtmlView::by('admin/user_fields')->with([
                    'aUserFields' => $User->getUserFields(),
                    'aUserFieldTypes' => $User->GetUserFieldTypes()
                ]);
        }
    }

    /**
     * Проверка поля пользователя на корректность из реквеста
     *
     * @param \Engine\Result\Traits\Messages $messages
     *
     * @return bool
     */
    public function checkUserField(Messages $messages): bool
    {
        /** @var ModuleUser $User */
        $User = LS::Make(ModuleUser::class);
        /** @var \Engine\Modules\ModuleLang $Lang */
        $Lang = LS::Make(ModuleLang::class);

        if (!getRequestStr('title')) {
            $messages->msgError($Lang->Get('user_field_error_add_no_title'), $Lang->Get('error'));

            return false;
        }
        if (!getRequestStr('name')) {
            $messages->msgError($Lang->Get('user_field_error_add_no_name'), $Lang->Get('error'));

            return false;
        }
        /**
         * Не допускаем дубликатов по имени
         */
        if ($User->userFieldExistsByName(getRequestStr('name'), getRequestStr('id'))) {
            $messages->msgError($Lang->Get('user_field_error_name_exists'), $Lang->Get('error'));

            return false;
        }

        return true;
    }
}
