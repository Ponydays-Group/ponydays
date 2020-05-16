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

use App\Entities\EntityBlog;
use App\Entities\EntityBlogUser;
use App\Entities\EntityComment;
use App\Entities\EntityCommentOnline;
use App\Entities\EntityNotification;
use App\Entities\EntityTopicRead;
use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleCast;
use App\Modules\ModuleComment;
use App\Modules\ModuleNotification;
use App\Modules\ModuleNotify;
use App\Modules\ModuleNower;
use App\Modules\ModuleStream;
use App\Modules\ModuleSubscribe;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use App\Modules\ModuleUserfeed;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleCache;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleLogger;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleSecurity;
use Engine\Modules\ModuleText;
use Engine\Modules\ModuleViewer;
use Engine\Router;
use Zend_Cache;


/**
 * Экшен обработки URL'ов вида /blog/
 *
 * @package actions
 * @since   1.0
 */
class ActionBlog extends Action
{
    /**
     * Главное меню
     *
     * @var string
     */
    protected $sMenuHeadItemSelect = 'blog';
    /**
     * Какое меню активно
     *
     * @var string
     */
    protected $sMenuItemSelect = 'blog';
    /**
     * Какое подменю активно
     *
     * @var string
     */
    protected $sMenuSubItemSelect = 'good';
    /**
     * УРЛ блога который подставляется в меню
     *
     * @var string
     */
    protected $sMenuSubBlogUrl;
    /**
     * Текущий пользователь
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent = null;
    /**
     * Число новых топиков в коллективных блогах
     *
     * @var int
     */
    protected $iCountTopicsCollectiveNew = 0;
    /**
     * Число новых топиков в персональных блогах
     *
     * @var int
     */
    protected $iCountTopicsPersonalNew = 0;
    /**
     * Число новых топиков в конкретном блоге
     *
     * @var int
     */
    protected $iCountTopicsBlogNew = 0;
    /**
     * Число новых топиков
     *
     * @var int
     */
    protected $iCountTopicsNew = 0;
    /**
     * Список URL с котрыми запрещено создавать блог
     *
     * @var array
     */
    protected $aBadBlogUrl = [
        'new',
        'good',
        'bad',
        'discussed',
        'top',
        'edit',
        'add',
        'deleted',
        'admin',
        'delete',
        'restore',
        'invite',
        'ajaxaddcomment',
        'ajaxaddbloginvite',
        'ajaxresponsecomment',
        'ajaxrebloginvite',
        'ajaxbloginfo',
        'ajaxblogjoin'
    ];

    /**
     * Инизиализация экшена
     *
     */
    public function Init()
    {
        /**
         * Устанавливаем евент по дефолту, т.е. будем показывать хорошие топики из коллективных блогов
         */
        $this->SetDefaultEvent('good');
        $this->sMenuSubBlogUrl = Router::GetPath('blog');
        /**
         * Достаём текущего пользователя
         */
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        /**
         * Подсчитываем новые топики
         */
        $this->iCountTopicsCollectiveNew = LS::Make(ModuleTopic::class)->GetCountTopicsCollectiveNew();
        $this->iCountTopicsPersonalNew = LS::Make(ModuleTopic::class)->GetCountTopicsPersonalNew();
        $this->iCountTopicsBlogNew = $this->iCountTopicsCollectiveNew;
        $this->iCountTopicsNew = $this->iCountTopicsCollectiveNew + $this->iCountTopicsPersonalNew;
        /**
         * Загружаем в шаблон JS текстовки
         */
        LS::Make(ModuleLang::class)->AddLangJs(
            [
                'blog_join',
                'blog_leave'
            ]
        );
    }

    /**
     * Регистрируем евенты, по сути определяем УРЛы вида /blog/.../
     *
     */
    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^good$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventTopics', 'topics']);
        $this->AddEvent('good', ['EventTopics', 'topics']);
        $this->AddEventPreg('/^bad$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventTopics', 'topics']);
        $this->AddEventPreg('/^new$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventTopics', 'topics']);
        $this->AddEventPreg('/^newall$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventTopics', 'topics']);
        $this->AddEventPreg('/^discussed$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventTopics', 'topics']);
        $this->AddEventPreg('/^top$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventTopics', 'topics']);

        $this->AddEvent('add', 'EventAddBlog');
        $this->AddEvent('edit', 'EventEditBlog');
        $this->AddEvent('delete', 'EventRemoveBlog');
        $this->AddEvent('restore', 'EventRestoreBlog');
        $this->AddEventPreg('/^admin$/i', '/^\d+$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventAdminBlog');
        $this->AddEvent('invite', 'EventInviteBlog');

        $this->AddEvent('ajaxaddcomment', 'AjaxAddComment');
        $this->AddEvent('ajaxresponsecomment', 'AjaxResponseComment');
        $this->AddEvent('ajaxaddbloginvite', 'AjaxAddBlogInvite');
        $this->AddEvent('ajaxrebloginvite', 'AjaxReBlogInvite');
        $this->AddEvent('ajaxremovebloginvite', 'AjaxRemoveBlogInvite');
        $this->AddEvent('ajaxbloginfo', 'AjaxBlogInfo');
        $this->AddEvent('ajaxblogjoin', 'AjaxBlogJoin');
        $this->AddEvent('ajax-search', 'EventAjaxSearch');

        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^(\d+)$/i', '/comments/', ['EventShowTopicComments', 'topic']);
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^(\d+)$/i', ['EventShowTopic', 'topic']);
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^(\d+)\.html$/i', ['EventShowTopic', 'topic']);

        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventShowBlog', 'blog']);
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^bad$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventShowBlog', 'blog']);
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^new$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventShowBlog', 'blog']);
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^newall$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventShowBlog', 'blog']);
        $this->AddEventPreg(
            '/^[\w\-\_]+$/i',
            '/^discussed$/i',
            '/^(page([1-9]\d{0,5}))?$/i',
            ['EventShowBlog', 'blog']
        );
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^top$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventShowBlog', 'blog']);
        $this->AddEventPreg(
            '/^[\w\-\_]+$/i',
            '/^deleted$/i',
            '/^(page([1-9]\d{0,5}))?$/i',
            ['EventShowDeletedBlog', 'blog']
        );

        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^users$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventShowUsers');
    }


    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    /**
     * Добавление нового блога
     *
     */
    protected function EventAddBlog()
    {
        /**
         * Устанавливаем title страницы
         */
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('blog_create'));
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = 'add';
        $this->sMenuItemSelect = 'blog';
        /**
         * Проверяем авторизован ли пользователь
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Проверяем хватает ли рейтинга юзеру чтоб создать блог
         */
        if (!LS::Make(ModuleACL::class)->CanCreateBlog($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator(
            )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('blog_create_acl'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        LS::Make(ModuleHook::class)->Run('blog_add_show');
        /**
         * Запускаем проверку корректности ввода полей при добалении блога.
         * Дополнительно проверяем, что был отправлен POST запрос.
         */
        if (!$this->checkBlogFields()) {
            return;
        }
        /**
         * Если всё ок то пытаемся создать блог
         */
        $oBlog = new EntityBlog();
        $oBlog->setOwnerId($this->oUserCurrent->getId());
        $oBlog->setTitle(strip_tags(getRequestStr('blog_title')));
        /**
         * Парсим текст на предмет разных ХТМЛ тегов
         */
        $sText = LS::Make(ModuleText::class)->Parser(getRequestStr('blog_description'));
        $oBlog->setDescription($sText);
        $oBlog->setType(getRequestStr('blog_type'));
        $oBlog->setDateAdd(date("Y-m-d H:i:s"));
        $oBlog->setLimitRatingTopic(getRequestStr('blog_limit_rating_topic'));
        $oBlog->setUrl(getRequestStr('blog_url'));
        $oBlog->setAvatar(null);
        /**
         * Загрузка аватара, делаем ресайзы
         */
        if (isset($_FILES['avatar']) and is_uploaded_file($_FILES['avatar']['tmp_name'])) {
            if ($sPath = LS::Make(ModuleBlog::class)->UploadBlogAvatar($_FILES['avatar'], $oBlog)) {
                $oBlog->setAvatar($sPath);
            } else {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('blog_create_avatar_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        }
        /**
         * Создаём блог
         */
        LS::Make(ModuleHook::class)->Run('blog_add_before', ['oBlog' => $oBlog]);
        if (LS::Make(ModuleBlog::class)->AddBlog($oBlog)) {
            LS::Make(ModuleHook::class)->Run('blog_add_after', ['oBlog' => $oBlog]);
            /**
             * Получаем блог, это для получение полного пути блога, если он в будущем будет зависит от других сущностей(компании, юзер и т.п.)
             */
            $oBlog->Blog_GetBlogById($oBlog->getId());

            /**
             * Добавляем событие в ленту
             */
            LS::Make(ModuleStream::class)->write($oBlog->getOwnerId(), 'add_blog', $oBlog->getId());
            Router::Location($oBlog->getUrlFull());
        } else {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
        }
    }

    protected function EventAjaxSearch()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        /**
         * Получаем из реквеста первые быквы для поиска пользователей по логину
         */
        $sBlogId = getRequest('blog_id');
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) {
            parent::EventNotFound();

            return;
        }
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Проверка на право редактировать блог
         */
        if (!LS::Make(ModuleACL::class)->IsAllowEditBlog($oBlog, $this->oUserCurrent)) {
            parent::EventNotFound();

            return;
        }
        $sTitle = getRequest('user_login');
        if (is_string($sTitle) and mb_strlen($sTitle, 'utf-8')) {
            $sTitle = str_replace(['_', '%'], ['\_', '\%'], $sTitle);
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));

            return;
        }
        /**
         * Как именно искать: совпадение в любой частилогина, или только начало или конец логина
         */
        if (getRequest('isPrefix')) {
            $sTitle .= '%';
        } elseif (getRequest('isPostfix')) {
            $sTitle = '%'.$sTitle;
        } else {
            $sTitle = '%'.$sTitle.'%';
        }
        /**
         * Ищем пользователей
         */
        $aResult = LS::Make(ModuleBlog::class)->GetBlogUsersByBlogIdLike(
            $oBlog->getId(),
            [
                ModuleBlog::BLOG_USER_ROLE_BAN,
                ModuleBlog::BLOG_USER_ROLE_RO,
                ModuleBlog::BLOG_USER_ROLE_USER,
                ModuleBlog::BLOG_USER_ROLE_MODERATOR,
                ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR
            ],
            1,
            Config::Get('module.blog.users_per_page'),
            $sTitle
        );
        /**
         * Формируем ответ
         */
        $oViewer = LS::Make(ModuleViewer::class)->GetLocalViewer();
        $oViewer->Assign('aBlogUsers', $aResult['collection']);
        $oViewer->Assign('oUserCurrent', LS::Make(ModuleUser::class)->GetUserCurrent());
        $oViewer->Assign('sUserListEmpty', LS::Make(ModuleLang::class)->Get('user_search_empty'));
        $oViewer->Assign('BLOG_USER_ROLE_BAN', ModuleBlog::BLOG_USER_ROLE_BAN);
        $oViewer->Assign('BLOG_USER_ROLE_RO', ModuleBlog::BLOG_USER_ROLE_RO);
        $oViewer->Assign('BLOG_USER_ROLE_USER', ModuleBlog::BLOG_USER_ROLE_USER);
        $oViewer->Assign('LIVESTREET_SECURITY_KEY', LS::Make(ModuleSecurity::class)->GenerateSessionKey());
        LS::Make(ModuleViewer::class)->AssignAjax('sText', $oViewer->Fetch("actions/ActionBlog/admin_users_table.tpl"));
    }

    /**
     * Редактирование блога
     *
     */
    protected function EventEditBlog()
    {
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = '';
        $this->sMenuItemSelect = 'profile';
        /**
         * Проверяем передан ли в УРЛе номер блога
         */
        $sBlogId = $this->GetParam(0);
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) {
            parent::EventNotFound();

            return;
        }
        /**
         * Проверям авторизован ли пользователь
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Проверка на право редактировать блог
         */
        if (!LS::Make(ModuleACL::class)->IsAllowEditBlog($oBlog, $this->oUserCurrent)) {
            parent::EventNotFound();

            return;
        }

        LS::Make(ModuleHook::class)->Run('blog_edit_show', ['oBlog' => $oBlog]);
        /**
         * Устанавливаем title страницы
         */
        LS::Make(ModuleViewer::class)->AddHtmlTitle($oBlog->getTitle());
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('blog_edit'));

        LS::Make(ModuleViewer::class)->Assign('oBlogEdit', $oBlog);
        /**
         * Устанавливаем шалон для вывода
         */
        $this->SetTemplateAction('add');
        /**
         * Если нажали кнопку "Сохранить"
         */
        if (isPost('submit_blog_add')) {
            /**
             * Запускаем проверку корректности ввода полей при редактировании блога
             */
            if (!$this->checkBlogFields($oBlog)) {
                return;
            }
            $oBlog->setTitle(strip_tags(getRequestStr('blog_title')));
            /**
             * Парсим описание блога на предмет ХТМЛ тегов
             */
            $sText = LS::Make(ModuleText::class)->Parser(getRequestStr('blog_description'));
            $oBlog->setDescription($sText);
            /**
             * Сбрасываем кеш, если поменяли тип блога
             * Нужна доработка, т.к. в этом блоге могут быть топики других юзеров
             */
            if ($oBlog->getType() != getRequestStr('blog_type')) {
                LS::Make(ModuleCache::class)->Clean(
                    Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                    ["topic_update_user_{$oBlog->getOwnerId()}"]
                );
            }
            $oBlog->setType(getRequestStr('blog_type'));
            $oBlog->setLimitRatingTopic(getRequestStr('blog_limit_rating_topic'));
            if ($this->oUserCurrent->isAdministrator()) {
                $oBlog->setUrl(getRequestStr('blog_url'));    // разрешаем смену URL блога только админу
            }
            /**
             * Загрузка аватара, делаем ресайзы
             */
            if (isset($_FILES['avatar']) and is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                if ($sPath = LS::Make(ModuleBlog::class)->UploadBlogAvatar($_FILES['avatar'], $oBlog)) {
                    $oBlog->setAvatar($sPath);
                } else {
                    LS::Make(ModuleMessage::class)->AddError(
                        LS::Make(ModuleLang::class)->Get('blog_create_avatar_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                }
            }
            /**
             * Удалить аватар
             */
            if (isset($_REQUEST['avatar_delete'])) {
                LS::Make(ModuleBlog::class)->DeleteBlogAvatar($oBlog);
                $oBlog->setAvatar(null);
            }
            /**
             * Обновляем блог
             */
            LS::Make(ModuleHook::class)->Run('blog_edit_before', ['oBlog' => $oBlog]);
            if (LS::Make(ModuleBlog::class)->UpdateBlog($oBlog)) {
                LS::Make(ModuleHook::class)->Run('blog_edit_after', ['oBlog' => $oBlog]);
                Router::Location($oBlog->getUrlFull());
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
                Router::Action('error');

                return;
            }
        } else {
            /**
             * Загружаем данные в форму редактирования блога
             */
            $_REQUEST['blog_title'] = $oBlog->getTitle();
            $_REQUEST['blog_url'] = $oBlog->getUrl();
            $_REQUEST['blog_type'] = $oBlog->getType();
            $_REQUEST['blog_description'] = $oBlog->getDescription();
            $_REQUEST['blog_limit_rating_topic'] = $oBlog->getLimitRatingTopic();
            $_REQUEST['blog_id'] = $oBlog->getId();
        }
    }

    /**
     * Управление пользователями блога
     *
     */
    protected function EventAdminBlog()
    {
        /**
         * Меню
         */
        $this->sMenuItemSelect = 'admin';
        $this->sMenuSubItemSelect = '';
        /**
         * Проверяем передан ли в УРЛе номер блога
         */
        $sBlogId = $this->GetParam(0);
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) {
            parent::EventNotFound();

            return;
        }
        /**
         * Проверям авторизован ли пользователь
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Проверка на право управлением пользователями блога
         */
        if (!LS::Make(ModuleACL::class)->IsAllowAdminBlog($oBlog, $this->oUserCurrent)) {
            parent::EventNotFound();

            return;
        }
        /**
         * Обрабатываем сохранение формы
         */
        if (isPost('submit_blog_admin')) {
            LS::Make(ModuleSecurity::class)->ValidateSendForm();

            $aUserRank = getRequest('user_rank', []);
            if (!is_array($aUserRank)) {
                $aUserRank = [];
            }
            foreach ($aUserRank as $sUserId => $sRank) {
                $sRank = (string)$sRank;
                if (!($oBlogUser =
                    LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId($oBlog->getId(), $sUserId))
                ) {
                    LS::Make(ModuleMessage::class)->AddError(
                        LS::Make(ModuleLang::class)->Get('system_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );
                    break;
                }
                /**
                 * Увеличиваем число читателей блога
                 */
                if (in_array($sRank, ['administrator', 'moderator', 'reader']) and $oBlogUser->getUserRole()
                    == ModuleBlog::BLOG_USER_ROLE_BAN
                ) {
                    $oBlog->setCountUser($oBlog->getCountUser() + 1);
                }

                switch ($sRank) {
                    case 'administrator':
                        $oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);
                        break;
                    case 'moderator':
                        $oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_MODERATOR);
                        break;
                    case 'reader':
                        $oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_USER);
                        break;
                    case 'ro':
                        $oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_RO);
                        break;
                    case 'ban':
                        if ($oBlogUser->getUserRole() != ModuleBlog::BLOG_USER_ROLE_BAN) {
                            $oBlog->setCountUser($oBlog->getCountUser() - 1);
                        }
                        $oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_BAN);
                        break;
                    default:
                        $oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_GUEST);
                }
                LS::Make(ModuleBlog::class)->UpdateRelationBlogUser($oBlogUser);
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('blog_admin_users_submit_ok')
                );
            }
            LS::Make(ModuleBlog::class)->UpdateBlog($oBlog);
        }
        /**
         * Текущая страница
         */
        $iPage = $this->GetParamEventMatch(1, 2) ? $this->GetParamEventMatch(1, 2) : 1;
        /**
         * Получаем список подписчиков блога
         */
        $aResult = LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId(
            $oBlog->getId(),
            [
                ModuleBlog::BLOG_USER_ROLE_BAN,
                ModuleBlog::BLOG_USER_ROLE_RO,
                ModuleBlog::BLOG_USER_ROLE_USER,
                ModuleBlog::BLOG_USER_ROLE_MODERATOR,
                ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR
            ],
            $iPage,
            Config::Get('module.blog.users_per_page')
        );
        $aBlogUsers = $aResult['collection'];
        /**
         * Формируем постраничность
         */
        $aPaging = LS::Make(ModuleViewer::class)->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.blog.users_per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('blog')."admin/{$oBlog->getId()}"
        );
        LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
        LS::Make(ModuleViewer::class)->Assign('oBlog', $oBlog);
        /**
         * Устанавливаем title страницы
         */
        LS::Make(ModuleViewer::class)->AddHtmlTitle($oBlog->getTitle());
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('blog_admin'));

        LS::Make(ModuleViewer::class)->Assign('oBlogEdit', $oBlog);
        LS::Make(ModuleViewer::class)->Assign('aBlogUsers', $aBlogUsers);
        /**
         * Устанавливаем шалон для вывода
         */
        $this->SetTemplateAction('admin');
        /**
         * Если блог закрытый, получаем приглашенных
         * и добавляем блок-форму для приглашения
         */
        if ($oBlog->getType() == 'close') {
            $aBlogUsersInvited = LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId(
                $oBlog->getId(),
                ModuleBlog::BLOG_USER_ROLE_INVITE,
                null
            );
            LS::Make(ModuleViewer::class)->Assign('aBlogUsersInvited', $aBlogUsersInvited['collection']);
            LS::Make(ModuleViewer::class)->AddBlock('right', 'actions/ActionBlog/invited.tpl');
        }
    }

    /**
     * Проверка полей блога
     *
     * @param \App\Entities\EntityBlog|null $oBlog
     *
     * @return bool
     */
    protected function checkBlogFields($oBlog = null)
    {
        /**
         * Проверяем только если была отправлена форма с данными (методом POST)
         */
        if (!isPost('submit_blog_add')) {
            $_REQUEST['blog_limit_rating_topic'] = 0;

            return false;
        }
        LS::Make(ModuleSecurity::class)->ValidateSendForm();

        $bOk = true;
        /**
         * Проверяем есть ли название блога
         */
        if (!func_check(getRequestStr('blog_title'), 'text', 2, 200)) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('blog_create_title_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            $bOk = false;
        } else {
            /**
             * Проверяем есть ли уже блог с таким названием
             */
            if ($oBlogExists = LS::Make(ModuleBlog::class)->GetBlogByTitle(getRequestStr('blog_title'))) {
                if (!$oBlog or $oBlog->getId() != $oBlogExists->getId()) {
                    LS::Make(ModuleMessage::class)->AddError(
                        LS::Make(ModuleLang::class)->Get('blog_create_title_error_unique'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );
                    $bOk = false;
                }
            }
        }

        /**
         * Проверяем есть ли URL блога, с заменой всех пробельных символов на "_"
         */
        if (!$oBlog or $this->oUserCurrent->isAdministrator()) {
            $blogUrl = preg_replace("/\s+/", '_', getRequestStr('blog_url'));
            $_REQUEST['blog_url'] = $blogUrl;
            if (!func_check(getRequestStr('blog_url'), 'login', 2, 50)) {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('blog_create_url_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
                $bOk = false;
            }
        }
        /**
         * Проверяем на счет плохих УРЛов
         */
        if (in_array(getRequestStr('blog_url'), $this->aBadBlogUrl)) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('blog_create_url_error_badword').' '.join(',', $this->aBadBlogUrl),
                LS::Make(ModuleLang::class)->Get('error')
            );
            $bOk = false;
        }
        /**
         * Проверяем есть ли уже блог с таким URL
         */
        if ($oBlogExists = LS::Make(ModuleBlog::class)->GetBlogByUrl(getRequestStr('blog_url'))) {
            if (!$oBlog or $oBlog->getId() != $oBlogExists->getId()) {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('blog_create_url_error_unique'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
                $bOk = false;
            }
        }
        /**
         * Проверяем есть ли описание блога
         */
        if (!func_check(getRequestStr('blog_description'), 'text', 10, 50000)) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('blog_create_description_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            $bOk = false;
        }
        /**
         * Проверяем доступные типы блога для создания
         */
        if (!in_array(getRequestStr('blog_type'), ['open', 'close', 'invite', 'personal'])) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('blog_create_type_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            $bOk = false;
        }
        /**
         * Преобразуем ограничение по рейтингу в число
         */
        if (!func_check(getRequestStr('blog_limit_rating_topic'), 'float')) {
            LS::Make(ModuleMessage::class)->AddError(
                LS::Make(ModuleLang::class)->Get('blog_create_rating_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            $bOk = false;
        }
        /**
         * Выполнение хуков
         */
        LS::Make(ModuleHook::class)->Run('check_blog_fields', ['bOk' => &$bOk]);

        return $bOk;
    }

    /**
     * Показ всех топиков
     *
     */
    protected function EventTopics()
    {
        $sPeriod = 1; // по дефолту 1 день
        if (in_array(getRequestStr('period'), [1, 7, 30, 'all'])) {
            $sPeriod = getRequestStr('period');
        }
        $sShowType = $this->sCurrentEvent;
        if (!in_array($sShowType, ['discussed', 'top'])) {
            $sPeriod = 'all';
        }
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = $sShowType == 'newall' ? 'new' : $sShowType;
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
        if ($iPage == 1 and !getRequest('period')) {
            LS::Make(ModuleViewer::class)->SetHtmlCanonical(Router::GetPath('blog').$sShowType.'/');
        }
        /**
         * Получаем список топиков
         */
        $aResult = LS::Make(ModuleTopic::class)->GetTopicsCollective(
            $iPage,
            Config::Get('module.topic.per_page'),
            $sShowType,
            $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
        );
        /**
         * Если нет топиков за 1 день, то показываем за неделю (7)
         */
        if (in_array($sShowType, ['discussed', 'top']) and !$aResult['count'] and $iPage == 1 and !getRequest(
                'period'
            )
        ) {
            $sPeriod = 7;
            $aResult = LS::Make(ModuleTopic::class)->GetTopicsCollective(
                $iPage,
                Config::Get('module.topic.per_page'),
                $sShowType,
                $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
            );
        }
        $aTopics = $aResult['collection'];
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topics_list_show', ['aTopics' => $aTopics]);
        /**
         * Формируем постраничность
         */
        $aPaging = LS::Make(ModuleViewer::class)->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('blog').$sShowType,
            in_array($sShowType, ['discussed', 'top']) ? ['period' => $sPeriod] : []
        );
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('blog_show', ['sShowType' => $sShowType]);
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign('aTopics', $aTopics);
        LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
        if (in_array($sShowType, ['discussed', 'top'])) {
            LS::Make(ModuleViewer::class)->Assign('sPeriodSelectCurrent', $sPeriod);
            LS::Make(ModuleViewer::class)->Assign('sPeriodSelectRoot', Router::GetPath('blog').$sShowType.'/');
        }
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');
    }

    /**
     * Показ топика
     *
     */
    protected function EventShowTopic()
    {
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $sBlogUrl = '';
        if ($this->GetParamEventMatch(0, 1)) {
            // из коллективного блога
            $sBlogUrl = $this->sCurrentEvent;
            $iTopicId = $this->GetParamEventMatch(0, 1);
            $this->sMenuItemSelect = 'blog';
        } else {
            // из персонального блога
            $iTopicId = $this->GetEventMatch(1);
            $this->sMenuItemSelect = 'log';
        }
        $this->sMenuSubItemSelect = '';
        /**
         * Проверяем есть ли такой топик
         */
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($iTopicId))) {
            parent::EventNotFound();

            return;
        }
        /**
         * Проверяем права на просмотр топика
         */
        if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTopic->getUserId()
                    and !$this->oUserCurrent->isAdministrator() and !LS::Make(ModuleACL::class)->IsAllowEditTopic(
                        $oTopic,
                        $this->oUserCurrent
                    )))
        ) {
            parent::EventNotFound();

            return;
        }

        /**
         * Определяем права на отображение записи из закрытого блога
         */
        if (in_array($oTopic->getBlog()->getType(), ['close', 'invite'])
            and (!$this->oUserCurrent
                || !in_array(
                    $oTopic->getBlog()->getId(),
                    LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent)
                )
            )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('blog_close_show'),
                LS::Make(ModuleLang::class)->Get('not_access')
            );
            Router::Action('error');

            return;
        }
        //Router::Location($oTopic->getUrl());
        if ($sBlogUrl == '') {
            Router::Location($oTopic->getUrl());
        }
        /**
         * Если номер топика правильный но УРЛ блога косяный то корректируем его и перенаправляем на нужный адрес
         */
        if ($sBlogUrl != '' and $oTopic->getBlog()->getUrl() != $sBlogUrl) {
            Router::Location($oTopic->getUrl());
        }
        /**
         * Обрабатываем добавление коммента
         */
        if (isset($_REQUEST['submit_comment'])) {
            $this->SubmitComment();
        }
        /**
         * Достаём комменты к топику
         */
        if (!Config::Get('module.comment.nested_page_reverse') and Config::Get('module.comment.use_nested')
            and Config::Get('module.comment.nested_per_page')
        ) {
            $iPageDef = ceil(
                LS::Make(ModuleComment::class)->GetCountCommentsRootByTargetId($oTopic->getId(), 'topic') / Config::Get(
                    'module.comment.nested_per_page'
                )
            );
        } else {
            $iPageDef = 1;
        }
        $iPage = getRequest('cmtpage', 0) ? (int)getRequest('cmtpage', 0) : $iPageDef;
        $aReturn = LS::Make(ModuleComment::class)->GetCommentsByTargetId(
            $oTopic->getId(),
            'topic',
            $iPage,
            Config::Get('module.comment.nested_per_page')
        );
        $iMaxIdComment = $aReturn['iMaxIdComment'];
        $aComments = $aReturn['comments'];
        /**
         * Если используется постраничность для комментариев - формируем ее
         */
        if (Config::Get('module.comment.use_nested') and Config::Get('module.comment.nested_per_page')) {
            $aPaging = $viewer->MakePaging(
                $aReturn['count'],
                $iPage,
                Config::Get('module.comment.nested_per_page'),
                Config::Get('pagination.pages.count'),
                ''
            );
            if (!Config::Get('module.comment.nested_page_reverse') and $aPaging) {
                // переворачиваем страницы в обратном порядке
                $aPaging['aPagesLeft'] = array_reverse($aPaging['aPagesLeft']);
                $aPaging['aPagesRight'] = array_reverse($aPaging['aPagesRight']);
            }
            $viewer->Assign('aPagingCmt', $aPaging);
        }
        /**
         * Отмечаем дату прочтения топика
         */
        /**
         * Выставляем SEO данные
         */
        $sTextSeo = strip_tags($oTopic->getText());
        $viewer->SetHtmlDescription(func_text_words($sTextSeo, Config::Get('seo.description_words_count')));
        $viewer->SetHtmlKeywords($oTopic->getTags());
        $viewer->SetHtmlCanonical($oTopic->getUrl());
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topic_show', ["oTopic" => $oTopic]);
        /**
         * Загружаем переменные в шаблон
         */
        //$aRawVote = LS::Make(ModuleVote::class)->GetVoteById($oTopic->getId(), 'topic');
        $aVote = [];
        //foreach ($aRawVote as $key) {
        //	$aVote[LS::Make(ModuleUser::class)->_GetUserById($key->getVoterId())->getLogin()] = $key->getDirection();
        //}
        $viewer->Assign('aVotes', $aVote);
        $viewer->Assign('oTopic', $oTopic);
        $viewer->Assign('aComments', $aComments);
        $viewer->Assign('iMaxIdComment', $iMaxIdComment);

        if ($this->oUserCurrent) {
            $oTopicRead = new EntityTopicRead();
            $oTopicRead->setTopicId($oTopic->getId());
            $oTopicRead->setUserId($this->oUserCurrent->getId());
            $oTopicRead->setCommentCountLast($oTopic->getCountComment());
            $oTopicRead->setCommentIdLast($iMaxIdComment);
            $oTopicRead->setDateRead(date("Y-m-d H:i:s"));
            LS::Make(ModuleTopic::class)->SetTopicRead($oTopicRead);
        }

        /**
         * Устанавливаем title страницы
         */
        $viewer->AddHtmlTitle($oTopic->getBlog()->getTitle());
        $viewer->AddHtmlTitle($oTopic->getTitle());
        $viewer->SetHtmlRssAlternate(Router::GetPath('rss').'comments/'.$oTopic->getId().'/', $oTopic->getTitle());
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('topic');
    }

    protected function EventShowTopicComments()
    {
        $sBlogUrl = '';
        if ($this->GetParamEventMatch(0, 1)) {
            // из коллективного блога
            $sBlogUrl = $this->sCurrentEvent;
            $iTopicId = $this->GetParamEventMatch(0, 1);
        } else {
            // из персонального блога
            $iTopicId = $this->GetEventMatch(1);
        }
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($iTopicId))) {
            parent::EventNotFound();

            return;
        }
        /**
         * Проверяем права на просмотр топика
         */
        if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTopic->getUserId()
                    and !$this->oUserCurrent->isAdministrator()))
        ) {
            parent::EventNotFound();

            return;
        }
        /**
         * Определяем права на отображение записи из закрытого блога
         */
        if (in_array($oTopic->getBlog()->getType(), ['close', 'invite'])
            and (!$this->oUserCurrent
                || !in_array(
                    $oTopic->getBlog()->getId(),
                    LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent)
                )
            )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('blog_close_show'),
                LS::Make(ModuleLang::class)->Get('not_access')
            );
            Router::Action('error');

            return;
        }
        //Router::Location($oTopic->getUrl());
        if ($sBlogUrl == '') {
            Router::Location($oTopic->getUrl());
        }
        /**
         * Достаём комменты к топику
         */
        if (!Config::Get('module.comment.nested_page_reverse') and Config::Get('module.comment.use_nested')
            and Config::Get('module.comment.nested_per_page')
        ) {
            $iPageDef = ceil(
                LS::Make(ModuleComment::class)->GetCountCommentsRootByTargetId($oTopic->getId(), 'topic') / Config::Get(
                    'module.comment.nested_per_page'
                )
            );
        } else {
            $iPageDef = 1;
        }
        $iPage = getRequest('cmtpage', 0) ? (int)getRequest('cmtpage', 0) : $iPageDef;
        $aReturn = LS::Make(ModuleComment::class)->GetCommentsByTargetId($oTopic->getId(), 'topic');
        $iMaxIdComment = $aReturn['iMaxIdComment'];
        $aComments = $aReturn['comments'];
        $aResult = [];
        $sReadlast = $oTopic->getDateRead();
        foreach ($aComments as $oComment) {
            $aComment = LS::Make(ModuleComment::class)->ConvertCommentToArray($oComment, $sReadlast);
            $aResult[$aComment['id']] = $aComment;
        }
        if ($this->oUserCurrent) {
            $oTopicRead = new EntityTopicRead();
            $oTopicRead->setTopicId($oTopic->getId());
            $oTopicRead->setUserId($this->oUserCurrent->getId());
            $oTopicRead->setCommentCountLast($oTopic->getCountComment());
            $oTopicRead->setCommentIdLast($iMaxIdComment);
            $oTopicRead->setDateRead(date("Y-m-d H:i:s"));
            LS::Make(ModuleTopic::class)->SetTopicRead($oTopicRead);
        }
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->AssignAjax("aComments", $aResult);
        $viewer->AssignAjax("sReadlast", $sReadlast);
        $viewer->AssignAjax("iMaxIdComment", $iMaxIdComment);
        $viewer->DisplayAjax();
    }

    /**
     * Страница со списком читателей блога
     *
     */
    protected function EventShowUsers()
    {
        $sBlogUrl = $this->sCurrentEvent;
        /**
         * Проверяем есть ли блог с таким УРЛ
         */
        if (!($oBlog = LS::Make(ModuleBlog::class)->GetBlogByUrl($sBlogUrl))) {
            parent::EventNotFound();

            return;
        }
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = '';
        $this->sMenuSubBlogUrl = $oBlog->getUrlFull();
        /**
         * Текущая страница
         */
        $iPage = $this->GetParamEventMatch(1, 2) ? $this->GetParamEventMatch(1, 2) : 1;
        $aBlogUsersResult = LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId(
            $oBlog->getId(),
            ModuleBlog::BLOG_USER_ROLE_USER,
            $iPage,
            Config::Get('module.blog.users_per_page')
        );
        $aBlogUsers = $aBlogUsersResult['collection'];
        /**
         * Формируем постраничность
         */
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $aPaging = $viewer->MakePaging(
            $aBlogUsersResult['count'],
            $iPage,
            Config::Get('module.blog.users_per_page'),
            Config::Get('pagination.pages.count'),
            $oBlog->getUrlFull().'users'
        );
        $viewer->Assign('aPaging', $aPaging);
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('blog_collective_show_users', ['oBlog' => $oBlog]);
        /**
         * Загружаем переменные в шаблон
         */
        $viewer->Assign('aBlogUsers', $aBlogUsers);
        $viewer->Assign('iCountBlogUsers', $aBlogUsersResult['count']);
        $viewer->Assign('oBlog', $oBlog);
        /**
         * Устанавливаем title страницы
         */
        $viewer->AddHtmlTitle($oBlog->getTitle());
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('users');
    }

    /**
     * Вывод топиков из определенного блога
     *
     */
    protected function EventShowBlog()
    {
        $sPeriod = 1; // по дефолту 1 день
        if (in_array(getRequestStr('period'), [1, 7, 30, 'all'])) {
            $sPeriod = getRequestStr('period');
        }
        $sBlogUrl = $this->sCurrentEvent;
        $sShowType = in_array($this->GetParamEventMatch(0, 0), ['bad', 'new', 'newall', 'discussed', 'top'])
            ? $this->GetParamEventMatch(0, 0) : 'good';
        if (!in_array($sShowType, ['discussed', 'top'])) {
            $sPeriod = 'all';
        }
        /**
         * Проверяем есть ли блог с таким УРЛ
         */
        if (!($oBlog = LS::Make(ModuleBlog::class)->GetBlogByUrl($sBlogUrl))) {
            parent::EventNotFound();

            return;
        }
        /**
         * Определяем права на отображение закрытого блога
         */
        if (($oBlog->getType() == 'close' or $oBlog->getType() == 'invite')
            and (!$this->oUserCurrent
                or !in_array(
                    $oBlog->getId(),
                    LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent)
                )
            )
        ) {
            $bCloseBlog = true;
        } else {
            $bCloseBlog = false;
        }
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->Assign('iCountBlogTopics', $oBlog->getCountTopic());
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = $sShowType == 'newall' ? 'new' : $sShowType;
        $this->sMenuSubBlogUrl = $oBlog->getUrlFull();
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(($sShowType == 'good') ? 0 : 1, 2) ? $this->GetParamEventMatch(
            ($sShowType == 'good') ? 0 : 1,
            2
        ) : 1;
        if ($iPage == 1 and !getRequest('period') and in_array($sShowType, ['discussed', 'top'])) {
            $viewer->SetHtmlCanonical($oBlog->getUrlFull().$sShowType.'/');
        }

        if (!$bCloseBlog) {
            /**
             * Получаем список топиков
             */
            $aResult = LS::Make(ModuleTopic::class)->GetTopicsByBlog(
                $oBlog,
                $iPage,
                Config::Get('module.topic.per_page'),
                $sShowType,
                $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
            );
            /**
             * Если нет топиков за 1 день, то показываем за неделю (7)
             */
            if (in_array($sShowType, ['discussed', 'top']) and !$aResult['count'] and $iPage == 1 and !getRequest(
                    'period'
                )
            ) {
                $sPeriod = 7;
                $aResult = LS::Make(ModuleTopic::class)->GetTopicsByBlog(
                    $oBlog,
                    $iPage,
                    Config::Get('module.topic.per_page'),
                    $sShowType,
                    $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
                );
            }
            $aTopics = $aResult['collection'];
            /**
             * Формируем постраничность
             */
            $aPaging = ($sShowType == 'good')
                ? $viewer->MakePaging(
                    $aResult['count'],
                    $iPage,
                    Config::Get('module.topic.per_page'),
                    Config::Get('pagination.pages.count'),
                    rtrim($oBlog->getUrlFull(), '/')
                )
                : $viewer->MakePaging(
                    $aResult['count'],
                    $iPage,
                    Config::Get('module.topic.per_page'),
                    Config::Get('pagination.pages.count'),
                    $oBlog->getUrlFull().$sShowType,
                    ['period' => $sPeriod]
                );
            /**
             * Получаем число новых топиков в текущем блоге
             */
            $this->iCountTopicsBlogNew = LS::Make(ModuleTopic::class)->GetCountTopicsByBlogNew($oBlog);

            $viewer->Assign('aPaging', $aPaging);
            $viewer->Assign('aTopics', $aTopics);
            if (in_array($sShowType, ['discussed', 'top'])) {
                $viewer->Assign('sPeriodSelectCurrent', $sPeriod);
                $viewer->Assign('sPeriodSelectRoot', $oBlog->getUrlFull().$sShowType.'/');
            }
        }
        /**
         * Выставляем SEO данные
         */
        $sTextSeo = strip_tags($oBlog->getDescription());
        $viewer->SetHtmlDescription(func_text_words($sTextSeo, Config::Get('seo.description_words_count')));
        /**
         * Получаем список юзеров блога
         */
        $aBlogUsersResult =
            LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId($oBlog->getId(), ModuleBlog::BLOG_USER_ROLE_USER, 1, 25);
        $aBlogUsers = $aBlogUsersResult['collection'];
        $aBlogModeratorsResult =
            LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId($oBlog->getId(), ModuleBlog::BLOG_USER_ROLE_MODERATOR);
        $aBlogModerators = $aBlogModeratorsResult['collection'];
        $aBlogAdministratorsResult = LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId(
            $oBlog->getId(),
            ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR
        );
        $aBlogAdministrators = $aBlogAdministratorsResult['collection'];
        /**
         * Для админов проекта получаем список блогов и передаем их во вьювер
         */
        if ($this->oUserCurrent and $this->oUserCurrent->isAdministrator()) {
            $aBlogs = LS::Make(ModuleBlog::class)->GetBlogs();
            unset($aBlogs[$oBlog->getId()]);

            $viewer->Assign('aBlogs', $aBlogs);
        }
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('blog_collective_show', ['oBlog' => $oBlog, 'sShowType' => $sShowType]);
        /**
         * Загружаем переменные в шаблон
         */
        $viewer->Assign('aBlogUsers', $aBlogUsers);
        $viewer->Assign('aBlogModerators', $aBlogModerators);
        $viewer->Assign('aBlogAdministrators', $aBlogAdministrators);
        $viewer->Assign('iCountBlogUsers', $aBlogUsersResult['count']);
        $viewer->Assign('iCountBlogModerators', $aBlogModeratorsResult['count']);
        $viewer->Assign('iCountBlogAdministrators', $aBlogAdministratorsResult['count'] + 1);
        $viewer->Assign('oBlog', $oBlog);
        $viewer->Assign('bCloseBlog', $bCloseBlog);
        /**
         * Устанавливаем title страницы
         */
        $viewer->AddHtmlTitle($oBlog->getTitle());
        $viewer->SetHtmlRssAlternate(Router::GetPath('rss').'blog/'.$oBlog->getUrl().'/', $oBlog->getTitle());
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('blog');
    }

    /**
     * Вывод удаленных топиков из определенного блога
     *
     */
    protected function EventShowDeletedBlog()
    {
        $sBlogUrl = $this->sCurrentEvent;
        $sShowType = 'deleted';
        $sPeriod = 'all';

        /**
         * Проверяем есть ли блог с таким УРЛ
         */
        if (!($oBlog = LS::Make(ModuleBlog::class)->GetBlogByUrl($sBlogUrl))) {
            parent::EventNotFound();

            return;
        }
        /**
         * Определяем права на отображение закрытого блога
         */

        if (!$bAccess = LS::Make(ModuleACL::class)->IsAllowDeleteBlog($oBlog, $this->oUserCurrent)) {
            $bCloseBlog = true;
        } else {
            $bCloseBlog = false;
        }
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->Assign('iCountBlogTopics', $oBlog->getCountTopic());
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = $sShowType == 'newall' ? 'new' : $sShowType;
        $this->sMenuSubBlogUrl = $oBlog->getUrlFull();
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(($sShowType == 'good') ? 0 : 1, 2) ? $this->GetParamEventMatch(
            ($sShowType == 'good') ? 0 : 1,
            2
        ) : 1;
        if ($iPage == 1 and !getRequest('period') and in_array($sShowType, ['discussed', 'top'])) {
            $viewer->SetHtmlCanonical($oBlog->getUrlFull().$sShowType.'/');
        }

        if (!$bCloseBlog) {
            /**
             * Получаем список топиков
             */
            $aResult = LS::Make(ModuleTopic::class)->GetDeletedTopicsByBlog(
                $oBlog,
                $iPage,
                Config::Get('module.topic.per_page'),
                $sShowType,
                $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
            );

            $aTopics = $aResult['collection'];
            /**
             * Формируем постраничность
             */
            $aPaging = ($sShowType == 'good')
                ? $viewer->MakePaging(
                    $aResult['count'],
                    $iPage,
                    Config::Get('module.topic.per_page'),
                    Config::Get('pagination.pages.count'),
                    rtrim($oBlog->getUrlFull(), '/')
                )
                : $viewer->MakePaging(
                    $aResult['count'],
                    $iPage,
                    Config::Get('module.topic.per_page'),
                    Config::Get('pagination.pages.count'),
                    $oBlog->getUrlFull().$sShowType,
                    ['period' => $sPeriod]
                );
            /**
             * Получаем число новых топиков в текущем блоге
             */
            $this->iCountTopicsBlogNew = LS::Make(ModuleTopic::class)->GetCountTopicsByBlogNew($oBlog);

            $viewer->Assign('aPaging', $aPaging);
            $viewer->Assign('aTopics', $aTopics);
            if (in_array($sShowType, ['discussed', 'top'])) {
                $viewer->Assign('sPeriodSelectCurrent', $sPeriod);
                $viewer->Assign('sPeriodSelectRoot', $oBlog->getUrlFull().$sShowType.'/');
            }
        }
        /**
         * Выставляем SEO данные
         */
        $sTextSeo = strip_tags($oBlog->getDescription());
        $viewer->SetHtmlDescription(func_text_words($sTextSeo, Config::Get('seo.description_words_count')));
        /**
         * Получаем список юзеров блога
         */
        $aBlogUsersResult =
            LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId($oBlog->getId(), ModuleBlog::BLOG_USER_ROLE_USER, 1, 25);
        $aBlogUsers = $aBlogUsersResult['collection'];
        $aBlogModeratorsResult =
            LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId($oBlog->getId(), ModuleBlog::BLOG_USER_ROLE_MODERATOR);
        $aBlogModerators = $aBlogModeratorsResult['collection'];
        $aBlogAdministratorsResult = LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId(
            $oBlog->getId(),
            ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR
        );
        $aBlogAdministrators = $aBlogAdministratorsResult['collection'];
        /**
         * Для админов проекта получаем список блогов и передаем их во вьювер
         */
        if ($this->oUserCurrent and $this->oUserCurrent->isAdministrator()) {
            $aBlogs = LS::Make(ModuleBlog::class)->GetBlogs();
            unset($aBlogs[$oBlog->getId()]);

            $viewer->Assign('aBlogs', $aBlogs);
        }
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('blog_collective_show', ['oBlog' => $oBlog, 'sShowType' => $sShowType]);
        /**
         * Загружаем переменные в шаблон
         */
        $viewer->Assign('aBlogUsers', $aBlogUsers);
        $viewer->Assign('aBlogModerators', $aBlogModerators);
        $viewer->Assign('aBlogAdministrators', $aBlogAdministrators);
        $viewer->Assign('iCountBlogUsers', $aBlogUsersResult['count']);
        $viewer->Assign('iCountBlogModerators', $aBlogModeratorsResult['count']);
        $viewer->Assign('iCountBlogAdministrators', $aBlogAdministratorsResult['count'] + 1);
        $viewer->Assign('oBlog', $oBlog);
        $viewer->Assign('bCloseBlog', $bCloseBlog);
        $viewer->Assign('bInTrash', true);
        /**
         * Устанавливаем title страницы
         */
        $viewer->AddHtmlTitle($oBlog->getTitle());
        $viewer->SetHtmlRssAlternate(Router::GetPath('rss').'blog/'.$oBlog->getUrl().'/', $oBlog->getTitle());
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('blog');
    }

    /**
     * Обработка добавление комментария к топику через ajax
     *
     */
    protected function AjaxAddComment()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseAjax('json');
        $this->SubmitComment();
    }

    /**
     * Обработка добавление комментария к топику
     *
     */
    protected function SubmitComment()
    {
        /**
         * Проверям авторизован ли пользователь
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем топик
         */
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById(getRequestStr('cmt_target_id')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Возможность постить коммент в топик в черновиках
         */
        if (!$oTopic->getPublish() and $this->oUserCurrent->getId() != $oTopic->getUserId()
            and !$this->oUserCurrent->isAdministrator()
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем разрешено ли постить комменты
         */
        if (!LS::Make(ModuleACL::class)->CanPostComment($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator(
            )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_comment_acl'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем разрешено ли постить комменты по времени
         */
        if (!LS::Make(ModuleACL::class)->CanPostCommentTime($this->oUserCurrent)
            and !$this->oUserCurrent->isAdministrator()
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_comment_limit'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем запрет на добавления коммента автором топика
         */
        if ($oTopic->getForbidComment()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_comment_notallow'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        if (LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId(
            $oTopic->getBlog()->getId(),
            $this->oUserCurrent->getId()
        )
        ) {
            if (LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId(
                    $oTopic->getBlog()->getId(),
                    $this->oUserCurrent->getId()
                )->getUserRole() == ModuleBlog::BLOG_USER_ROLE_RO
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('topic_create_blog_error_noallow'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        }

        /**
         * Проверяем текст комментария
         */
//        echo getRequestStr('comment_text');
        $bMark = getRequestStr('form_comment_mark') == "on";
        if ($bMark) {
            $sText =
                LS::Make(ModuleText::class)->Parser(LS::Make(ModuleText::class)->Mark(getRequestStr('comment_text')));
        } else {
            $sText = LS::Make(ModuleText::class)->Parser(getRequestStr('comment_text'));
        }
        if (!func_check($sText, 'text', 2, Config::Get('module.comment.max_length'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_comment_add_text_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверям на какой коммент отвечаем
         */
        $sParentId = (int)getRequest('reply');
        if (!func_check($sParentId, 'id')) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        $oCommentParent = null;
        if ($sParentId != 0) {
            /**
             * Проверяем существует ли комментарий на который отвечаем
             */
            if (!($oCommentParent = LS::Make(ModuleComment::class)->GetCommentById($sParentId))) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            /**
             * Проверяем из одного топика ли новый коммент и тот на который отвечаем
             */
            if ($oCommentParent->getTargetId() != $oTopic->getId()) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            if ($oCommentParent->getDelete()) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }

        } else {
            /**
             * Корневой комментарий
             */
            $sParentId = null;
        }
        /**
         * Проверка на дублирующий коммент
         */
        if (LS::Make(ModuleComment::class)->GetCommentUnique(
            $oTopic->getId(),
            'topic',
            $this->oUserCurrent->getId(),
            $sParentId,
            md5($sText)
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_comment_spam'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Создаём коммент
         */
        $oCommentNew = new EntityComment();
        $oCommentNew->setTargetId($oTopic->getId());
        $oCommentNew->setTargetType('topic');
        $oCommentNew->setTargetParentId($oTopic->getBlog()->getId());
        $oCommentNew->setUserId($this->oUserCurrent->getId());
        $oCommentNew->setText($sText);
        $oCommentNew->setDate(date("Y-m-d H:i:s"));
        $oCommentNew->setUserIp(func_getIp());
        $oCommentNew->setPid($sParentId);
        $oCommentNew->setTextHash(md5($sText));
        $oCommentNew->setPublish($oTopic->getPublish());
        $oCommentNew->setUserRank($this->oUserCurrent->getRank());
        $sFile = LS::Make(ModuleTopic::class)->UploadTopicImageUrl(
            'http:'.$this->oUserCurrent->getProfileAvatarPath(64),
            $this->oUserCurrent,
            false
        );
        $oCommentNew->setUserAvatar($sFile);
        /**
         * Добавляем коммент
         */

        LS::Make(ModuleHook::class)->Run(
            'comment_add_before',
            ['oCommentNew' => $oCommentNew, 'oCommentParent' => $oCommentParent, 'oTopic' => $oTopic]
        );
        if (LS::Make(ModuleComment::class)->AddComment($oCommentNew)) {
            /**
             * Отправка уведомления пользователям
             */
            $postLink = LS::Make(ModuleTopic::class)->GetTopicById($oCommentNew->getTargetId())->getUrl();
            $notificationLink = $postLink."#comment".$oCommentNew->getId();
            $notificationTitle =
                "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()."</a> <a href='"
                .$notificationLink."'>ответил</a> вам в посте <a href='".$postLink."'>".$oTopic->getTitle()."</a>";
            $notificationText = "";
            if ($oCommentParent && $this->oUserCurrent->getId() != $oCommentParent->getUserId()) {
                $notification = new EntityNotification(
                    [
                        'user_id'           => $oCommentParent->getUserId(),
                        'text'              => $notificationText,
                        'title'             => $notificationTitle,
                        'link'              => $notificationLink,
                        'rating'            => 0,
                        'notification_type' => 3,
                        'target_type'       => 'comment',
                        'target_id'         => $oCommentNew->getId(),
                        'sender_user_id'    => $this->oUserCurrent->getId(),
                        'group_target_type' => 'topic',
                        'group_target_id'   => $oCommentNew->getTargetId()
                    ]
                );
                if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
                    LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oCommentNew);
                }
            }

            if ($this->oUserCurrent->getId() != $oTopic->getUserId()) {
                $notificationLink = $postLink."#comment".$oCommentNew->getId();
                $notificationTitle =
                    "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
                    ."</a> оставил <a href='".$notificationLink."'>комментарий</a> в вашем посте <a href='".$postLink
                    ."'>".$oTopic->getTitle()."</a>";
                $notificationText = "";
                $notification = new EntityNotification(
                    [
                        'user_id'           => $oTopic->getUserId(),
                        'text'              => $notificationText,
                        'title'             => $notificationTitle,
                        'link'              => $notificationLink,
                        'rating'            => 0,
                        'notification_type' => 5,
                        'target_type'       => 'comment',
                        'target_id'         => $oCommentNew->getId(),
                        'sender_user_id'    => $this->oUserCurrent->getId(),
                        'group_target_type' => 'topic',
                        'group_target_id'   => $oCommentNew->getTargetId()
                    ]
                );
                //TODO: Требуется система подписок, чтобы не создавать копии постов.
//				if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
//                    LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oCommentNew);
//				}
                LS::Make(ModuleNower::class)->PostNotificationWithComment($notification, $oCommentNew);
            }

            LS::Make(ModuleHook::class)->Run(
                'comment_add_after',
                ['oCommentNew' => $oCommentNew, 'oCommentParent' => $oCommentParent, 'oTopic' => $oTopic]
            );
            LS::Make(ModuleCast::class)->sendCastNotify('comment', $oCommentNew, $oTopic, $oCommentNew->getText());

            /** @var \Engine\Modules\ModuleViewer $viewer */
            $viewer = LS::Make(ModuleViewer::class);
            $viewer->AssignAjax('sCommentId', $oCommentNew->getId());
            if ($oTopic->getPublish()) {
                /**
                 * Добавляем коммент в прямой эфир если топик не в черновиках
                 */
                $oCommentOnline = new EntityCommentOnline();
                $oCommentOnline->setTargetId($oCommentNew->getTargetId());
                $oCommentOnline->setTargetType($oCommentNew->getTargetType());
                $oCommentOnline->setTargetParentId($oCommentNew->getTargetParentId());
                $oCommentOnline->setCommentId($oCommentNew->getId());

                LS::Make(ModuleComment::class)->AddCommentOnline($oCommentOnline);
            }
            /**
             * Сохраняем дату последнего коммента для юзера
             */
            $this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
            LS::Make(ModuleUser::class)->Update($this->oUserCurrent);

            /**
             * Список емайлов на которые не нужно отправлять уведомление
             */
            $aExcludeMail = [$this->oUserCurrent->getMail()];
            /**
             * Отправляем уведомление тому на чей коммент ответили
             */
            if ($oCommentParent and $oCommentNew->getUserId() != $oCommentParent->getUserId()) {
                $oUserAuthorComment = $oCommentParent->getUser();
                $aExcludeMail[] = $oUserAuthorComment->getMail();
                LS::Make(ModuleNotify::class)->SendCommentReplyToAuthorParentComment(
                    $oUserAuthorComment,
                    $oTopic,
                    $oCommentNew,
                    $this->oUserCurrent
                );
            }
            /**
             * Отправка уведомления автору топика
             */
            LS::Make(ModuleSubscribe::class)->Send(
                'topic_new_comment',
                $oTopic->getId(),
                'notify.comment_new.tpl',
                LS::Make(ModuleLang::class)->Get('notify_subject_comment_new'),
                [
                    'oTopic'       => $oTopic,
                    'oComment'     => $oCommentNew,
                    'oUserComment' => $this->oUserCurrent,
                ],
                $aExcludeMail
            );
            /**
             * Добавляем событие в ленту
             */
            if ($oCommentNew) {
                LS::Make(ModuleStream::class)->write(
                    $oCommentNew->getUserId(),
                    'add_comment',
                    $oCommentNew->getId(),
                    $oTopic->getPublish() && !in_array($oTopic->getBlog()->getType(), ['close', 'invite'])
                );
            }
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
        }
    }

    /**
     * Получение новых комментариев
     *
     */
    protected function AjaxResponseComment()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseAjax('json');
        /**
         * Пользователь авторизован?
         */
//		if (!$this->oUserCurrent) {
//			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('need_authorization'),LS::Make(ModuleLang::class)->Get('error'));
//			return;
//		}
        /**
         * Топик существует?
         */
        $idTopic = getRequestStr('idTarget', null, 'post');
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($idTopic))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        if ($oTopic->getType() != 'talk') {
            if ($this->oUserCurrent) {
                if (!in_array(
                        $oTopic->getBlogId(),
                        LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent)
                    ) and $oTopic->getBlog()->getType() != "open"
                ) {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('system_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                }
            } else {
                if ($oTopic->getBlog()->getType() != "open") {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('system_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                }
            }
        }

        $idCommentLast = getRequestStr('idCommentLast', null, 'post');
        $selfIdComment = getRequestStr('selfIdComment', null, 'post');
        $aComments = [];
        /**
         * Если используется постраничность, возвращаем только добавленный комментарий
         */
        if (getRequest('bUsePaging', null, 'post') and $selfIdComment) {
            if ($oComment = LS::Make(ModuleComment::class)->GetCommentById($selfIdComment) and $oComment->getTargetId()
                == $oTopic->getId() and $oComment->getTargetType() == 'topic'
            ) {
                $oViewerLocal = $viewer->GetLocalViewer();
                $oViewerLocal->Assign('oUserCurrent', $this->oUserCurrent);
                $oViewerLocal->Assign('bOneComment', true);

                $oViewerLocal->Assign('oComment', $oComment);
                $sText = $oViewerLocal->Fetch(
                    LS::Make(ModuleComment::class)->GetTemplateCommentByTarget($oTopic->getId(), 'topic')
                );
                $aCmt = [];
                $aCmt[] = [
                    'html' => $sText,
                    'obj'  => $oComment,
                ];
            } else {
                $aCmt = [];
            }
            $aReturn['comments'] = $aCmt;
            $aReturn['iMaxIdComment'] = $selfIdComment;
        } else {
            $aReturn =
                LS::Make(ModuleComment::class)->GetCommentsNewByTargetId($oTopic->getId(), 'topic', $idCommentLast);
        }
        $iMaxIdComment = $aReturn['iMaxIdComment'];

//		if ($this->oUserCurrent) {
        $sReadlast = $oTopic->getReadLast();
//        }

        if ($this->oUserCurrent) {
            $oTopicRead = new EntityTopicRead();
            $oTopicRead->setTopicId($oTopic->getId());
            $oTopicRead->setUserId($this->oUserCurrent->getId());
            $oTopicRead->setCommentCountLast($oTopic->getCountComment());
            $oTopicRead->setCommentIdLast($iMaxIdComment);
            $oTopicRead->setDateRead(date("Y-m-d H:i:s"));
            LS::Make(ModuleTopic::class)->SetTopicRead($oTopicRead);
        }

        $aEditedComments = [];
        $aEditedCommentsRaw =
            LS::Make(ModuleComment::class)->GetCommentsOlderThenEdited('topic', $oTopic->getId(), $idCommentLast);
        foreach ($aEditedCommentsRaw as $oComment) {
            $aEditedComments[$oComment->getId()] = [
                'id'   => $oComment->getId(),
                'text' => $oComment->getText(),
            ];
        }

        $aCmts = $aReturn['comments'];
        if ($aCmts and is_array($aCmts)) {
            foreach ($aCmts as $aCmt) {
                $aComments[] = [
                    'html'     => $aCmt['html'],
                    'idParent' => $aCmt['obj']->getPid(),
                    'id'       => $aCmt['obj']->getId(),
                ];
            }
        }
        $viewer->AssignAjax('iMaxIdComment', $iMaxIdComment);
        $viewer->AssignAjax('aComments', $aComments);

        $viewer->AssignAjax('iMaxIdComment', $iMaxIdComment);
        $viewer->AssignAjax('aComments', $aComments);
        $viewer->AssignAjax('aEditedComments', $aEditedComments);
        if ($this->oUserCurrent) {
            $viewer->AssignAjax(
                'iUserCurrentCountTalkNew',
                LS::Make(ModuleTalk::class)->GetCountTalkNew($this->oUserCurrent->getId())
            );
        }
    }

    /**
     * Обработка ajax запроса на отправку
     * пользователям приглашения вступить в закрытый блог
     */
    protected function AjaxAddBlogInvite()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseAjax('json');
        $sUsers = getRequest('users', null, 'post');
        $sBlogId = getRequestStr('idBlog', null, 'post');
        /**
         * Если пользователь не авторизирован, возвращаем ошибку
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        /**
         * Проверяем существование блога
         */
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId) or !is_string($sUsers)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем, имеет ли право текущий пользователь добавлять invite в blog
         */
        $oBlogUser =
            LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId($oBlog->getId(), $this->oUserCurrent->getId());
        $bIsAdministratorBlog = $oBlogUser ? $oBlogUser->getIsAdministrator() : false;
        if ($oBlog->getOwnerId() != $this->oUserCurrent->getId() and !$this->oUserCurrent->isAdministrator()
            and !$bIsAdministratorBlog
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Получаем список пользователей блога (любого статуса)
         * Это полный АХТУНГ - исправить!
         */
        $aBlogUsersResult = LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId(
            $oBlog->getId(),
            [
                ModuleBlog::BLOG_USER_ROLE_BAN,
                ModuleBlog::BLOG_USER_ROLE_REJECT,
                ModuleBlog::BLOG_USER_ROLE_INVITE,
                ModuleBlog::BLOG_USER_ROLE_USER,
                ModuleBlog::BLOG_USER_ROLE_MODERATOR,
                ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR
            ],
            null // пока костылем
        );
        $aBlogUsers = $aBlogUsersResult['collection'];
        $aUsers = explode(',', $sUsers);

        $aResult = [];
        /**
         * Обрабатываем добавление по каждому из переданных логинов
         */
        foreach ($aUsers as $sUser) {
            $sUser = trim($sUser);
            if ($sUser == '') {
                continue;
            }
            /**
             * Если пользователь пытается добавить инвайт
             * самому себе, возвращаем ошибку
             */
            if (strtolower($sUser) == strtolower($this->oUserCurrent->getLogin())) {
                $aResult[] = [
                    'bStateError' => true,
                    'sMsgTitle'   => LS::Make(ModuleLang::class)->Get('error'),
                    'sMsg'        => LS::Make(ModuleLang::class)->Get('blog_user_invite_add_self')
                ];
                continue;
            }
            /**
             * Если пользователь не найден или неактивен,
             * возвращаем ошибку
             */
            if (!$oUser = LS::Make(ModuleUser::class)->GetUserByLogin($sUser) or $oUser->getActivate() != 1) {
                $aResult[] = [
                    'bStateError' => true,
                    'sMsgTitle'   => LS::Make(ModuleLang::class)->Get('error'),
                    'sMsg'        => LS::Make(ModuleLang::class)->Get(
                        'user_not_found',
                        ['login' => htmlspecialchars($sUser)]
                    ),
                    'sUserLogin'  => htmlspecialchars($sUser)
                ];
                continue;
            }

            if (!isset($aBlogUsers[$oUser->getId()])) {
                /**
                 * Создаем нового блог-пользователя со статусом INVITED
                 */
                $oBlogUserNew = new EntityBlogUser();
                $oBlogUserNew->setBlogId($oBlog->getId());
                $oBlogUserNew->setUserId($oUser->getId());
                $oBlogUserNew->setUserRole(ModuleBlog::BLOG_USER_ROLE_INVITE);

                if (LS::Make(ModuleBlog::class)->AddRelationBlogUser($oBlogUserNew)) {
                    $aResult[] = [
                        'bStateError'   => false,
                        'sMsgTitle'     => LS::Make(ModuleLang::class)->Get('attention'),
                        'sMsg'          => LS::Make(ModuleLang::class)->Get(
                            'blog_user_invite_add_ok',
                            ['login' => htmlspecialchars($sUser)]
                        ),
                        'sUserLogin'    => htmlspecialchars($sUser),
                        'sUserWebPath'  => $oUser->getUserWebPath(),
                        'sUserAvatar48' => $oUser->getProfileAvatarPath(48)
                    ];
                    $this->SendBlogInvite($oBlog, $oUser);
                } else {
                    $aResult[] = [
                        'bStateError' => true,
                        'sMsgTitle'   => LS::Make(ModuleLang::class)->Get('error'),
                        'sMsg'        => LS::Make(ModuleLang::class)->Get('system_error'),
                        'sUserLogin'  => htmlspecialchars($sUser)
                    ];
                }
            } else {
                /**
                 * Попытка добавить приглашение уже существующему пользователю,
                 * возвращаем ошибку (сначала определяя ее точный текст)
                 */
                switch (true) {
                    case ($aBlogUsers[$oUser->getId()]->getUserRole() == ModuleBlog::BLOG_USER_ROLE_INVITE):
                        $sErrorMessage = LS::Make(ModuleLang::class)->Get(
                            'blog_user_already_invited',
                            ['login' => htmlspecialchars($sUser)]
                        );
                        break;
                    case ($aBlogUsers[$oUser->getId()]->getUserRole() > ModuleBlog::BLOG_USER_ROLE_GUEST):
                        $sErrorMessage = LS::Make(ModuleLang::class)->Get(
                            'blog_user_already_exists',
                            ['login' => htmlspecialchars($sUser)]
                        );
                        break;
                    case ($aBlogUsers[$oUser->getId()]->getUserRole() == ModuleBlog::BLOG_USER_ROLE_REJECT):
                        $sErrorMessage = LS::Make(ModuleLang::class)->Get(
                            'blog_user_already_reject',
                            ['login' => htmlspecialchars($sUser)]
                        );
                        break;
                    default:
                        $sErrorMessage = LS::Make(ModuleLang::class)->Get('system_error');
                }
                $aResult[] = [
                    'bStateError' => true,
                    'sMsgTitle'   => LS::Make(ModuleLang::class)->Get('error'),
                    'sMsg'        => $sErrorMessage,
                    'sUserLogin'  => htmlspecialchars($sUser)
                ];
                continue;
            }
        }
        /**
         * Передаем во вьевер массив с результатами обработки по каждому пользователю
         */
        $viewer->AssignAjax('aUsers', $aResult);
    }

    /**
     * Обработка ajax запроса на отправку
     * повторного приглашения вступить в закрытый блог
     */
    protected function AjaxReBlogInvite()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseAjax('json');
        $sUserId = getRequestStr('idUser', null, 'post');
        $sBlogId = getRequestStr('idBlog', null, 'post');
        /**
         * Если пользователь не авторизирован, возвращаем ошибку
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        /**
         * Проверяем существование блога
         */
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Пользователь существует и активен?
         */
        if (!$oUser = LS::Make(ModuleUser::class)->GetUserById($sUserId) or $oUser->getActivate() != 1) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем, имеет ли право текущий пользователь добавлять invite в blog
         */
        $oBlogUser =
            LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId($oBlog->getId(), $this->oUserCurrent->getId());
        $bIsAdministratorBlog = $oBlogUser ? $oBlogUser->getIsAdministrator() : false;
        if ($oBlog->getOwnerId() != $this->oUserCurrent->getId() and !$this->oUserCurrent->isAdministrator()
            and !$bIsAdministratorBlog
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $oBlogUser = LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
        if ($oBlogUser->getUserRole() == ModuleBlog::BLOG_USER_ROLE_INVITE) {
            $this->SendBlogInvite($oBlog, $oUser);
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                LS::Make(ModuleLang::class)->Get('blog_user_invite_add_ok', ['login' => $oUser->getLogin()]),
                LS::Make(ModuleLang::class)->Get('attention')
            );
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
        }
    }

    /**
     * Обработка ajax запроса на удаление вступить в закрытый блог
     */
    protected function AjaxRemoveBlogInvite()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseAjax('json');
        $sUserId = getRequestStr('idUser', null, 'post');
        $sBlogId = getRequestStr('idBlog', null, 'post');
        /**
         * Если пользователь не авторизирован, возвращаем ошибку
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        /**
         * Проверяем существование блога
         */
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Пользователь существует и активен?
         */
        if (!$oUser = LS::Make(ModuleUser::class)->GetUserById($sUserId) or $oUser->getActivate() != 1) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяем, имеет ли право текущий пользователь добавлять invite в blog
         */
        $oBlogUser =
            LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId($oBlog->getId(), $this->oUserCurrent->getId());
        $bIsAdministratorBlog = $oBlogUser ? $oBlogUser->getIsAdministrator() : false;
        if ($oBlog->getOwnerId() != $this->oUserCurrent->getId() and !$this->oUserCurrent->isAdministrator()
            and !$bIsAdministratorBlog
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $oBlogUser = LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
        if ($oBlogUser->getUserRole() == ModuleBlog::BLOG_USER_ROLE_INVITE) {
            /**
             * Удаляем связь/приглашение
             */
            LS::Make(ModuleBlog::class)->DeleteRelationBlogUser($oBlogUser);
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                LS::Make(ModuleLang::class)->Get('blog_user_invite_remove_ok', ['login' => $oUser->getLogin()]),
                LS::Make(ModuleLang::class)->Get('attention')
            );
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
        }
    }

    /**
     * Выполняет отправку приглашения в блог
     * (по внутренней почте и на email)
     *
     * @param \App\Entities\EntityBlog $oBlog
     * @param \App\Entities\EntityUser $oUser
     */
    protected function SendBlogInvite($oBlog, $oUser)
    {
        /** @var ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
        $sTitle = $lang->Get(
            'blog_user_invite_title',
            [
                'blog_title' => $oBlog->getTitle()
            ]
        );

        require_once './lib/XXTEA/encrypt.php';
        /**
         * Формируем код подтверждения в URL
         */
        $sCode = $oBlog->getId().'_'.$oUser->getId();
        $sCode = rawurlencode(base64_encode(xxtea_encrypt($sCode, Config::Get('module.blog.encrypt'))));

        $aPath = [
            'accept' => Router::GetPath('blog').'invite/accept/?code='.$sCode,
            'reject' => Router::GetPath('blog').'invite/reject/?code='.$sCode
        ];

        $sText = $lang->Get(
            'blog_user_invite_text',
            [
                'login'       => $this->oUserCurrent->getLogin(),
                'accept_path' => $aPath['accept'],
                'reject_path' => $aPath['reject'],
                'blog_title'  => $oBlog->getTitle()
            ]
        );

        $notification = new EntityNotification(
            [
                'user_id'           => $oUser->getUserId(),
                'text'              => $sText,
                'title'             => $sTitle,
                'link'              => "",
                'rating'            => 0,
                'notification_type' => 13,
                'target_type'       => 'blog',
                'target_id'         => $oBlog->getId(),
                'sender_user_id'    => $this->oUserCurrent->getId(),
                'group_target_type' => 'nothing',
                'group_target_id'   => -1
            ]
        );
        if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
            LS::Make(ModuleNower::class)->PostNotification($notificationCreated);
        }
    }

    /**
     * Обработка отправленого пользователю приглашения вступить в блог
     */
    protected function EventInviteBlog()
    {
        require_once './lib/XXTEA/encrypt.php';
        /**
         * Получаем код подтверждения из ревеста и дешефруем его
         */
        $sCode = xxtea_decrypt(base64_decode(rawurldecode(getRequestStr('code'))), Config::Get('module.blog.encrypt'));
        if (!$sCode) {
            $this->EventNotFound();

            return;
        }
        list($sBlogId, $sUserId) = explode('_', $sCode, 2);

        $sAction = $this->GetParam(0);
        /**
         * Получаем текущего пользователя
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            $this->EventNotFound();

            return;
        }
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        /**
         * Если приглашенный пользователь не является авторизированным
         */
        if ($this->oUserCurrent->getId() != $sUserId) {
            $this->EventNotFound();

            return;
        }
        /**
         * Получаем указанный блог
         */
        if ((!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) || $oBlog->getType() != 'close') {
            $this->EventNotFound();

            return;
        }
        /**
         * Получаем связь "блог-пользователь" и проверяем,
         * чтобы ее тип был INVITE или REJECT
         */
        if (!$oBlogUser =
            LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId($oBlog->getId(), $this->oUserCurrent->getId())
        ) {
            $this->EventNotFound();

            return;
        }
        if ($oBlogUser->getUserRole() > ModuleBlog::BLOG_USER_ROLE_GUEST) {
            $sMessage = LS::Make(ModuleLang::class)->Get('blog_user_invite_already_done');
            LS::Make(ModuleMessage::class)->AddError($sMessage, LS::Make(ModuleLang::class)->Get('error'), true);
            Router::Location(Router::GetPath('talk'));

            return;
        }
        /** @var ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
        if (!in_array(
            $oBlogUser->getUserRole(),
            [ModuleBlog::BLOG_USER_ROLE_INVITE, ModuleBlog::BLOG_USER_ROLE_REJECT]
        )
        ) {
            LS::Make(ModuleMessage::class)->AddError($lang->Get('system_error'), $lang->Get('error'), true);
            Router::Location(Router::GetPath('talk'));

            return;
        }
        /**
         * Обновляем роль пользователя до читателя
         */
        $oBlogUser->setUserRole(
            ($sAction == 'accept') ? ModuleBlog::BLOG_USER_ROLE_USER : ModuleBlog::BLOG_USER_ROLE_REJECT
        );
        if (!LS::Make(ModuleBlog::class)->UpdateRelationBlogUser($oBlogUser)) {
            LS::Make(ModuleMessage::class)->AddError($lang->Get('system_error'), $lang->Get('error'), true);
            Router::Location(Router::GetPath('talk'));

            return;
        }
        if ($sAction == 'accept') {
            /**
             * Увеличиваем число читателей блога
             */
            $oBlog->setCountUser($oBlog->getCountUser() + 1);
            LS::Make(ModuleBlog::class)->UpdateBlog($oBlog);
            $sMessage = $lang->Get('blog_user_invite_accept');
            /**
             * Добавляем событие в ленту
             */
            LS::Make(ModuleStream::class)->write($oBlogUser->getUserId(), 'join_blog', $oBlog->getId());
        } else {
            $sMessage = $lang->Get('blog_user_invite_reject');
        }
        LS::Make(ModuleMessage::class)->AddNotice($sMessage, $lang->Get('attention'), true);
        /**
         * Перенаправляем на страницу личной почты
         */
        Router::Location(Router::GetPath('talk'));
    }

    /**
     * Удаление блога
     *
     */
    protected function EventDeleteBlog()
    {
        LS::Make(ModuleSecurity::class)->ValidateSendForm();
        /**
         * Проверяем передан ли в УРЛе номер блога
         */
        $sBlogId = $this->GetParam(0);
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) {
            parent::EventNotFound();

            return;
        }
        /**
         * Проверям авторизован ли пользователь
         */
        /** @var \Engine\Modules\ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('not_access'), $lang->Get('error'));
            Router::Action('error');

            return;
        }
        /**
         * проверяем есть ли право на удаление топика
         */
        if (!$bAccess = LS::Make(ModuleACL::class)->IsAllowDeleteBlog($oBlog, $this->oUserCurrent)) {
            parent::EventNotFound();

            return;
        }
        $aTopics = LS::Make(ModuleTopic::class)->GetTopicsByBlogId($sBlogId);
        switch ($bAccess) {
            case ModuleACL::CAN_DELETE_BLOG_EMPTY_ONLY :
                if (is_array($aTopics) and count($aTopics)) {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        $lang->Get('blog_admin_delete_not_empty'),
                        $lang->Get('error'),
                        true
                    );
                    Router::Location($oBlog->getUrlFull());
                }
                break;
            case ModuleACL::CAN_DELETE_BLOG_WITH_TOPICS :
                /**
                 * Если указан идентификатор блога для перемещения,
                 * то делаем попытку переместить топики.
                 *
                 * (-1) - выбран пункт меню "удалить топики".
                 */
                if ($sBlogIdNew = getRequestStr('topic_move_to') and ($sBlogIdNew != -1) and is_array($aTopics)
                    and count($aTopics)
                ) {
                    if (!$oBlogNew = LS::Make(ModuleBlog::class)->GetBlogById($sBlogIdNew)) {
                        LS::Make(ModuleMessage::class)->AddErrorSingle(
                            $lang->Get('blog_admin_delete_move_error'),
                            $lang->Get('error'),
                            true
                        );
                        Router::Location($oBlog->getUrlFull());
                    }
                    /**
                     * Если выбранный блог является персональным, возвращаем ошибку
                     */
                    if ($oBlogNew->getType() == 'personal') {
                        LS::Make(ModuleMessage::class)->AddErrorSingle(
                            $lang->Get('blog_admin_delete_move_personal'),
                            $lang->Get('error'),
                            true
                        );
                        Router::Location($oBlog->getUrlFull());
                    }
                    /**
                     * Перемещаем топики
                     */
                    LS::Make(ModuleTopic::class)->MoveTopics($sBlogId, $sBlogIdNew);
                }
                break;
            default:
                parent::EventNotFound();

                return;
        }
        /**
         * Удаляяем блог и перенаправляем пользователя к списку блогов
         */
        LS::Make(ModuleHook::class)->Run('blog_delete_before', ['sBlogId' => $sBlogId]);
        if (LS::Make(ModuleBlog::class)->DeleteBlog($sBlogId)) {
            LS::Make(ModuleHook::class)->Run('blog_delete_after', ['sBlogId' => $sBlogId]);
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                $lang->Get('blog_admin_delete_success'),
                $lang->Get('attention'),
                true
            );
            Router::Location(Router::GetPath('blogs'));
        } else {
            Router::Location($oBlog->getUrlFull());
        }
    }

    /**
     * Удаление блога в корзину
     *
     */
    protected function EventRemoveBlog()
    {
        LS::Make(ModuleSecurity::class)->ValidateSendForm();
        /**
         * Проверяем передан ли в УРЛе номер блога
         */
        $sBlogId = $this->GetParam(0);
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) {
            parent::EventNotFound();

            return;
        }
        /**
         * Проверям авторизован ли пользователь
         */
        /** @var \Engine\Modules\ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('not_access'), $lang->Get('error'));
            Router::Action('error');

            return;
        }
        /**
         * проверяем есть ли право на удаление топика
         */
        if (!$bAccess = LS::Make(ModuleACL::class)->IsAllowDeleteBlog($oBlog, $this->oUserCurrent)) {
            parent::EventNotFound();

            return;
        }
        switch ($bAccess) {
            case ModuleACL::CAN_DELETE_BLOG_EMPTY_ONLY :
            case ModuleACL::CAN_DELETE_BLOG_WITH_TOPICS :
                break;
            default:
                parent::EventNotFound();

                return;
        }
        /**
         * Удаляяем блог и перенаправляем пользователя к списку блогов
         */
        LS::Make(ModuleHook::class)->Run('blog_delete_before', ['sBlogId' => $sBlogId]);
        $oBlog->setDeleted(true);
        if (LS::Make(ModuleBlog::class)->UpdateBlog($oBlog)) {
            LS::Make(ModuleHook::class)->Run('blog_delete_after', ['sBlogId' => $sBlogId]);
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                $lang->Get('blog_admin_delete_success'),
                $lang->Get('attention'),
                true
            );
            $sLogText = $this->oUserCurrent->getLogin()." удалил блог ".$oBlog->getId();
            LS::Make(ModuleLogger::class)->Notice($sLogText);
            Router::Location(Router::GetPath('blogs'));
        } else {
            Router::Location($oBlog->getUrlFull());
        }
    }

    /**
     * Восстановление блога из корзины
     *
     */
    protected function EventRestoreBlog()
    {
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseAjax('json');
        LS::Make(ModuleSecurity::class)->ValidateSendForm();
        /**
         * Проверяем передан ли в УРЛе номер блога
         */
        /** @var \Engine\Modules\ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
        $sBlogId = $this->GetParam(0);
        if (!$oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('system_error'), $lang->Get('error'));
            $viewer->AssignAjax('bState', true);
        }
        /**
         * Проверям авторизован ли пользователь
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('not_access'), $lang->Get('error'));
            $viewer->AssignAjax('bState', true);
        }
        /**
         * проверяем есть ли право на удаление топика
         */
        if (!$bAccess = LS::Make(ModuleACL::class)->IsAllowDeleteBlog($oBlog, $this->oUserCurrent)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('system_error'), $lang->Get('error'));
            $viewer->AssignAjax('bState', true);
        }
        switch ($bAccess) {
            case ModuleACL::CAN_DELETE_BLOG_EMPTY_ONLY :
            case ModuleACL::CAN_DELETE_BLOG_WITH_TOPICS :
                break;
            default:
                LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('system_error'), $lang->Get('error'));
                $viewer->AssignAjax('bState', true);
        }
        /**
         * Восстанавливаем блог
         */
        $oBlog->setDeleted(false);
        if (LS::Make(ModuleBlog::class)->UpdateBlog($oBlog)) {
            $sLogText = $this->oUserCurrent->getLogin()." восстановил блог ".$oBlog->getId();
            LS::Make(ModuleLogger::class)->Notice($sLogText);
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                $lang->Get('blog_admin_restore_success'),
                $lang->Get('attention'),
                true
            );
            $viewer->AssignAjax('bState', false);
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('system_error'), $lang->Get('error'));

            return;
        }
    }

    /**
     * Получение описания блога
     *
     */
    protected function AjaxBlogInfo()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseAjax('json');
        $sBlogId = getRequestStr('idBlog', null, 'post');
        /**
         * Определяем тип блога и получаем его
         */
        $oBlog = null;
        if ($sBlogId == 0) {
            if ($this->oUserCurrent) {
                $oBlog = LS::Make(ModuleBlog::class)->GetPersonalBlogByUserId($this->oUserCurrent->getId());
            }
        } else {
            $oBlog = LS::Make(ModuleBlog::class)->GetBlogById($sBlogId);
        }
        /**
         * если блог найден, то возвращаем описание
         */
        if ($oBlog) {
            $sText = $oBlog->getDescription();
            $viewer->AssignAjax('sText', $sText);
        }
    }

    /**
     * Подключение/отключение к блогу
     *
     */
    protected function AjaxBlogJoin()
    {
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        /** @var \Engine\Modules\ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
        /**
         * Устанавливаем формат Ajax ответа
         */
        $viewer->SetResponseAjax('json');
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('need_authorization'), $lang->Get('error'));

            return;
        }
        /**
         * Блог существует?
         */
        $idBlog = getRequestStr('idBlog', null, 'post');
        if (!($oBlog = LS::Make(ModuleBlog::class)->GetBlogById($idBlog))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('system_error'), $lang->Get('error'));

            return;
        }
        /**
         * Проверяем тип блога
         */
        if (!in_array($oBlog->getType(), ['open', 'close', 'invite'])) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('blog_join_error_invite'), $lang->Get('error'));

            return;
        }
        /**
         * Получаем текущий статус пользователя в блоге
         */
        $oBlogUser =
            LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId($oBlog->getId(), $this->oUserCurrent->getId());
        $roles = [ModuleBlog::BLOG_USER_ROLE_BAN, ModuleBlog::BLOG_USER_ROLE_RO];
        if (!$oBlogUser
            || (($oBlogUser->getUserRole() < ModuleBlog::BLOG_USER_ROLE_GUEST
                    && $oBlog->getType() != 'close')
                && !in_array($oBlogUser->getUserRole(), $roles)
                || $this->oUserCurrent->getIsAdministrator() && $oBlog->getType() == 'close')
        ) {
            if ($oBlog->getOwnerId() != $this->oUserCurrent->getId()) {
                /**
                 * Присоединяем юзера к блогу
                 */
                $bResult = false;
                if ($oBlogUser) {
                    $oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_USER);
                    $bResult = LS::Make(ModuleBlog::class)->UpdateRelationBlogUser($oBlogUser);
                    //} elseif($oBlog->getType()=='open' or $oBlog->getType()=='invite') {
                } elseif ($oBlog->getType() != 'close') {
                    $oBlogUserNew = new EntityBlogUser();
                    $oBlogUserNew->setBlogId($oBlog->getId());
                    $oBlogUserNew->setUserId($this->oUserCurrent->getId());
                    $oBlogUserNew->setUserRole(ModuleBlog::BLOG_USER_ROLE_USER);
                    $bResult = LS::Make(ModuleBlog::class)->AddRelationBlogUser($oBlogUserNew);
                }
                if ($bResult) {
                    LS::Make(ModuleMessage::class)->AddNoticeSingle(
                        $lang->Get('blog_join_ok'),
                        $lang->Get('attention')
                    );
                    $viewer->AssignAjax('bState', true);
                    /**
                     * Увеличиваем число читателей блога
                     */
                    $oBlog->setCountUser($oBlog->getCountUser() + 1);
                    LS::Make(ModuleBlog::class)->UpdateBlog($oBlog);
                    $viewer->AssignAjax('iCountUser', $oBlog->getCountUser());
                    /**
                     * Добавляем событие в ленту
                     */
                    LS::Make(ModuleStream::class)->write($this->oUserCurrent->getId(), 'join_blog', $oBlog->getId());
                    /**
                     * Добавляем подписку на этот блог в ленту пользователя
                     */
                    //LS::Make(ModuleUserfeed::class)->subscribeUser($this->oUserCurrent->getId(), ModuleUserfeed::SUBSCRIBE_TYPE_BLOG, $oBlog->getId());
                } else {
                    $sMsg = ($oBlog->getType() == 'close')
                        ? $lang->Get('blog_join_error_invite')
                        : $lang->Get('system_error');
                    LS::Make(ModuleMessage::class)->AddErrorSingle($sMsg, $lang->Get('error'));

                    return;
                }
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    $lang->Get('blog_join_error_self'),
                    $lang->Get('attention')
                );

                return;
            }
        }
        if ($oBlogUser && $oBlogUser->getUserRole() > ModuleBlog::BLOG_USER_ROLE_GUEST) {
            /**
             * Покидаем блог
             */
            if (in_array($oBlogUser->getUserRole(), $roles)) {
                LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('system_error'), $lang->Get('error'));

                return;
            }
            if (LS::Make(ModuleBlog::class)->DeleteRelationBlogUser($oBlogUser)) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle($lang->Get('blog_leave_ok'), $lang->Get('attention'));
                $viewer->AssignAjax('bState', false);
                /**
                 * Уменьшаем число читателей блога
                 */
                $oBlog->setCountUser($oBlog->getCountUser() - 1);
                LS::Make(ModuleBlog::class)->UpdateBlog($oBlog);
                $viewer->AssignAjax('iCountUser', $oBlog->getCountUser());
                /**
                 * Удаляем подписку на этот блог в ленте пользователя
                 */
                LS::Make(ModuleUserfeed::class)->unsubscribeUser(
                    $this->oUserCurrent->getId(),
                    ModuleUserfeed::SUBSCRIBE_TYPE_BLOG,
                    $oBlog->getId()
                );
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle($lang->Get('system_error'), $lang->Get('error'));

                return;
            }
        }
    }

    /**
     * Выполняется при завершении работы экшена
     *
     */
    public function EventShutdown()
    {
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        /**
         * Загружаем в шаблон необходимые переменные
         */
        $viewer->Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        $viewer->Assign('sMenuItemSelect', $this->sMenuItemSelect);
        $viewer->Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
        $viewer->Assign('sMenuSubBlogUrl', $this->sMenuSubBlogUrl);
        $viewer->Assign('iCountTopicsCollectiveNew', $this->iCountTopicsCollectiveNew);
        $viewer->Assign('iCountTopicsPersonalNew', $this->iCountTopicsPersonalNew);
        $viewer->Assign('iCountTopicsBlogNew', $this->iCountTopicsBlogNew);
        $viewer->Assign('iCountTopicsNew', $this->iCountTopicsNew);

        $viewer->Assign('BLOG_USER_ROLE_GUEST', ModuleBlog::BLOG_USER_ROLE_GUEST);
        $viewer->Assign('BLOG_USER_ROLE_USER', ModuleBlog::BLOG_USER_ROLE_USER);
        $viewer->Assign('BLOG_USER_ROLE_MODERATOR', ModuleBlog::BLOG_USER_ROLE_MODERATOR);
        $viewer->Assign('BLOG_USER_ROLE_ADMINISTRATOR', ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);
        $viewer->Assign('BLOG_USER_ROLE_INVITE', ModuleBlog::BLOG_USER_ROLE_INVITE);
        $viewer->Assign('BLOG_USER_ROLE_REJECT', ModuleBlog::BLOG_USER_ROLE_REJECT);
        $viewer->Assign('BLOG_USER_ROLE_BAN', ModuleBlog::BLOG_USER_ROLE_BAN);
    }
}
