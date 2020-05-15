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

use App\Modules\ModuleACL;
use App\Entities\EntityComment;
use App\Modules\ModuleComment;
use App\Modules\ModuleFavourite;
use App\Entities\EntityNotification;
use App\Modules\ModuleNotification;
use App\Modules\ModuleNotify;
use App\Modules\ModuleNower;
use App\Entities\EntityTalkUser;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use App\Modules\ModuleWall;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleSecurity;
use Engine\Modules\ModuleText;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки личной почты (сообщения /talk/)
 *
 * @package actions
 * @since 1.0
 */
class ActionTalk extends Action
{
    /**
     * Текущий юзер
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent = null;
    /**
     * Подменю
     *
     * @var string
     */
    protected $sMenuSubItemSelect = '';
    /**
     * Массив ID юзеров адресатов
     *
     * @var array
     */
    protected $aUsersId = array();

    /**
     * Инициализация
     *
     */
    public function Init()
    {
        /**
         * Проверяем авторизован ли юзер
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));
            Router::Action('error'); return;
        }
        /**
         * Получаем текущего юзера
         */
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        $this->SetDefaultEvent('inbox');
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('talk_menu_inbox'));

        /**
         * Загружаем в шаблон JS текстовки
         */
        LS::Make(ModuleLang::class)->AddLangJs(array(
            'delete'
        ));
    }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
    {
        $this->AddEvent('inbox', 'EventInbox');
        $this->AddEvent('add', 'EventAdd');
        $this->AddEvent('read', 'EventRead');
        $this->AddEvent('readcomments', 'EventReadComments');
        $this->AddEvent('delete', 'EventDelete');
        $this->AddEvent('ajaxaddcomment', 'AjaxAddComment');
        $this->AddEvent('ajaxresponsecomment', 'AjaxResponseComment');
        $this->AddEvent('favourites', 'EventFavourites');
        $this->AddEvent('blacklist', 'EventBlacklist');
        $this->AddEvent('ajaxaddtoblacklist', 'AjaxAddToBlacklist');
        $this->AddEvent('ajaxdeletefromblacklist', 'AjaxDeleteFromBlacklist');
        $this->AddEvent('ajaxdeletetalkuser', 'AjaxDeleteTalkUser');
        $this->AddEvent('ajaxinvitetalkuserback', 'AjaxInviteTalkUserBack');
        $this->AddEvent('ajaxacceptinvitetalkuserback', 'AjaxAcceptInviteTalkUserBack');
        $this->AddEvent('ajaxaddtalkuser', 'AjaxAddTalkUser');
        $this->AddEvent('ajaxnewmessages', 'AjaxNewMessages');
        $this->AddEvent('ajaxmarkasread', 'EventMarkAsRead');
    }


    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    /**
     * Удаление письма
     */
    protected function EventMarkAsRead()
    {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        $iTargetId = getRequest('target');
        if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById($iTargetId))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle('System error', LS::Make(ModuleLang::class)->Get('attention'));
            return;
        }
        if (!($oTalkUser = LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))) {
            return;
        }
        $oTalkUser->setDateLast(date("Y-m-d H:i:s"));
        $oTalkUser->setCommentCountNew(0);
        if (LS::Make(ModuleTalk::class)->UpdateTalkUser($oTalkUser)) {
            LS::Make(ModuleMessage::class)->AddNoticeSingle("Сообщение помечено как прочитанное", "Успешно");
        }
    }

    protected function EventDelete()
    {
        LS::Make(ModuleSecurity::class)->ValidateSendForm();
        /**
         * Получаем номер сообщения из УРЛ и проверяем существует ли оно
         */
        $sTalkId = $this->GetParam(0);
        if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById($sTalkId))) {
            parent::EventNotFound(); return;
        }
        /**
         * Пользователь входит в переписку?
         */
        if (!($oTalkUser = LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))) {
            parent::EventNotFound(); return;
        }
        /**
         * Обработка удаления сообщения
         */
        LS::Make(ModuleTalk::class)->DeleteTalkUserByArray($sTalkId, $this->oUserCurrent->getId());
        Router::Location(Router::GetPath('talk'));
    }

    /**
     * Отображение списка сообщений
     */
    protected function EventInbox()
    {
        /**
         * Обработка удаления сообщений
         */
        if (getRequest('submit_talk_del')) {
            LS::Make(ModuleSecurity::class)->ValidateSendForm();

            $aTalksIdDel = getRequest('talk_select');
            if (is_array($aTalksIdDel)) {
                LS::Make(ModuleTalk::class)->DeleteTalkUserByArray(array_keys($aTalksIdDel), $this->oUserCurrent->getId());
            }
        }
        /**
         * Обработка отметки о прочтении
         */
        if (getRequest('submit_talk_read')) {
            LS::Make(ModuleSecurity::class)->ValidateSendForm();

            $aTalksIdDel = getRequest('talk_select');
            if (is_array($aTalksIdDel)) {
                LS::Make(ModuleTalk::class)->MarkReadTalkUserByArray(array_keys($aTalksIdDel), $this->oUserCurrent->getId());
            }
        }
        $this->sMenuSubItemSelect = 'inbox';
        /**
         * Количество сообщений на страницу
         */
        $iPerPage = Config::Get('module.talk.per_page');
        /**
         * Формируем фильтр для поиска сообщений
         */
        $aFilter = $this->BuildFilter();
        /**
         * Если только новые, то добавляем условие в фильтр
         */
        if ($this->GetParam(0) == 'new') {
            $this->sMenuSubItemSelect = 'new';
            $aFilter['only_new'] = true;
            $iPerPage = 50; // новых отображаем только последние 50 писем, без постраничности
        }
        /**
         * Передан ли номер страницы
         */
        $iPage = preg_match("/^page([1-9]\d{0,5})$/i", $this->getParam(0), $aMatch) ? $aMatch[1] : 1;
        /**
         * Получаем список писем
         */
        $aResult = LS::Make(ModuleTalk::class)->GetTalksByFilter(
            $aFilter, $iPage, $iPerPage
        );

        $aTalks = $aResult['collection'];
        /**
         * Формируем постраничность
         */
        $aPaging = LS::Make(ModuleViewer::class)->MakePaging(
            $aResult['count'], $iPage, $iPerPage, Config::Get('pagination.pages.count'),
            Router::GetPath('talk') . $this->sCurrentEvent,
            array_intersect_key(
                $_REQUEST,
                array_fill_keys(
                    array('start', 'end', 'keyword', 'sender', 'keyword_text', 'favourite'),
                    ''
                )
            )
        );
        /**
         * Показываем сообщение, если происходит поиск по фильтру
         */
        if (getRequest('submit_talk_filter')) {
            LS::Make(ModuleMessage::class)->AddNotice(
                ($aResult['count'])
                    ? LS::Make(ModuleLang::class)->Get('talk_filter_result_count', array('count' => $aResult['count']))
                    : LS::Make(ModuleLang::class)->Get('talk_filter_result_empty')
            );
        }
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
        LS::Make(ModuleViewer::class)->Assign('aTalks', $aTalks);
    }

    /**
     * Формирует из REQUEST массива фильтр для отбора писем
     *
     * @return array
     */
    protected function BuildFilter()
    {
        /**
         * Текущий пользователь
         */
        $aFilter = array(
            'user_id' => $this->oUserCurrent->getId(),
        );
        /**
         * Дата старта поиска
         */
        if ($start = getRequestStr('start')) {
            if (func_check($start, 'text', 6, 10) && substr_count($start, '.') == 2) {
                list($d, $m, $y) = explode('.', $start);
                if (@checkdate($m, $d, $y)) {
                    $aFilter['date_min'] = "{$y}-{$m}-{$d}";
                } else {
                    LS::Make(ModuleMessage::class)->AddError(
                        LS::Make(ModuleLang::class)->Get('talk_filter_error_date_format'),
                        LS::Make(ModuleLang::class)->Get('talk_filter_error')
                    );
                    unset($_REQUEST['start']);
                }
            } else {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('talk_filter_error_date_format'),
                    LS::Make(ModuleLang::class)->Get('talk_filter_error')
                );
                unset($_REQUEST['start']);
            }
        }
        /**
         * Дата окончания поиска
         */
        if ($end = getRequestStr('end')) {
            if (func_check($end, 'text', 6, 10) && substr_count($end, '.') == 2) {
                list($d, $m, $y) = explode('.', $end);
                if (@checkdate($m, $d, $y)) {
                    $aFilter['date_max'] = "{$y}-{$m}-{$d} 23:59:59";
                } else {
                    LS::Make(ModuleMessage::class)->AddError(
                        LS::Make(ModuleLang::class)->Get('talk_filter_error_date_format'),
                        LS::Make(ModuleLang::class)->Get('talk_filter_error')
                    );
                    unset($_REQUEST['end']);
                }
            } else {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('talk_filter_error_date_format'),
                    LS::Make(ModuleLang::class)->Get('talk_filter_error')
                );
                unset($_REQUEST['end']);
            }
        }
        /**
         * Ключевые слова в теме сообщения
         */
        if ($sKeyRequest = getRequest('keyword') and is_string($sKeyRequest)) {
            $sKeyRequest = urldecode($sKeyRequest);
            preg_match_all('~(\S+)~u', $sKeyRequest, $aWords);

            if (is_array($aWords[1]) && isset($aWords[1]) && count($aWords[1])) {
                $aFilter['keyword'] = '%' . implode('%', $aWords[1]) . '%';
            } else {
                unset($_REQUEST['keyword']);
            }
        }
        /**
         * Ключевые слова в тексте сообщения
         */
        if ($sKeyRequest = getRequest('keyword_text') and is_string($sKeyRequest)) {
            $sKeyRequest = urldecode($sKeyRequest);
            preg_match_all('~(\S+)~u', $sKeyRequest, $aWords);

            if (is_array($aWords[1]) && isset($aWords[1]) && count($aWords[1])) {
                $aFilter['text_like'] = '%' . implode('%', $aWords[1]) . '%';
            } else {
                unset($_REQUEST['keyword_text']);
            }
        }
        /**
         * Отправитель
         */
        if ($sender = getRequest('sender') and is_string($sender)) {
            $aFilter['user_login'] = urldecode($sender);
        }
        /**
         * Искать только в избранных письмах
         */
        if (getRequest('favourite')) {
            $aTalkIdResult = LS::Make(ModuleFavourite::class)->GetFavouritesByUserId($this->oUserCurrent->getId(), 'talk', 1, 500); // ограничиваем
            $aFilter['id'] = $aTalkIdResult['collection'];
            $_REQUEST['favourite'] = 1;
        } else {
            unset($_REQUEST['favourite']);
        }
        return $aFilter;
    }

    /**
     * Отображение списка блэк-листа
     */
    protected function EventBlacklist()
    {
        $this->sMenuSubItemSelect = 'blacklist';
        $aUsersBlacklist = LS::Make(ModuleTalk::class)->GetBlacklistByUserId($this->oUserCurrent->getId());
        LS::Make(ModuleViewer::class)->Assign('aUsersBlacklist', $aUsersBlacklist);
    }

    /**
     * Отображение списка избранных писем
     */
    protected function EventFavourites()
    {
        $this->sMenuSubItemSelect = 'favourites';
        /**
         * Передан ли номер страницы
         */
        $iPage = preg_match("/^page([1-9]\d{0,5})$/i", $this->getParam(0), $aMatch) ? $aMatch[1] : 1;
        /**
         * Получаем список писем
         */
        $aResult = LS::Make(ModuleTalk::class)->GetTalksFavouriteByUserId(
            $this->oUserCurrent->getId(),
            $iPage, Config::Get('module.talk.per_page')
        );
        $aTalks = $aResult['collection'];
        /**
         * Формируем постраничность
         */
        $aPaging = LS::Make(ModuleViewer::class)->MakePaging(
            $aResult['count'], $iPage, Config::Get('module.talk.per_page'), Config::Get('pagination.pages.count'),
            Router::GetPath('talk') . $this->sCurrentEvent
        );
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
        LS::Make(ModuleViewer::class)->Assign('aTalks', $aTalks);
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('talk_favourite_inbox'));
    }

    /**
     * Страница создания письма
     */
    protected function EventAdd()
    {
        $this->sMenuSubItemSelect = 'add';
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('talk_menu_inbox_create'));
        /**
         * Получаем список друзей
         */
        $aUsersFriend = LS::Make(ModuleUser::class)->GetUsersFriend($this->oUserCurrent->getId());
        if ($aUsersFriend['collection']) {
            LS::Make(ModuleViewer::class)->Assign('aUsersFriend', $aUsersFriend['collection']);
        }
        /**
         * Проверяем отправлена ли форма с данными
         */
        if (!isPost('submit_talk_add')) {
            return;
        }
        /**
         * Проверка корректности полей формы
         */
        if (!$this->checkTalkFields()) {
            return;
        }
        /**
         * Проверяем разрешено ли отправлять инбокс по времени
         */
        if (!LS::Make(ModuleACL::class)->CanSendTalkTime($this->oUserCurrent)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('talk_time_limit'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Отправляем письмо
         */
        if ($oTalk = LS::Make(ModuleTalk::class)->SendTalk(LS::Make(ModuleText::class)->Parser(strip_tags(getRequestStr('talk_title'))), LS::Make(ModuleText::class)->Parser(getRequestStr('talk_text')), $this->oUserCurrent, $this->aUsersId)) {

			/**
			 * Отправка уведомления пользователям
			 */
            $aUsersTalk = LS::Make(ModuleTalk::class)->GetUsersTalk($oTalk->getId(), ModuleTalk::TALK_USER_ACTIVE);
            foreach ($aUsersTalk as $oUserTalk) {
				if ($oUserTalk->getId() != $this->oUserCurrent->getId()) {
					$notificationLink = "/talk/read/" . $oTalk->getId();
					$notificationTitle = "Пользователь " . $this->oUserCurrent->getLogin() . " отправил вам <a href='".$notificationLink."'>личное письмо</a>";
					$notificationText = $oTalk->getTitle();
					$notification = new EntityNotification(
						array(
							'user_id' => $oUserTalk->getUserId(),
							'text' => $notificationText,
							'title' => $notificationTitle,
							'link' => $notificationLink,
							'rating' => 0,
							'notification_type' => 1,
							'target_type' => 'talk',
							'target_id' => $oTalk->getId(),
							'sender_user_id' => $this->oUserCurrent->getId(),
							'group_target_type' => 'nothing',
							'group_target_id' => -1
						)
					);
					if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
						LS::Make(ModuleNower::class)->PostNotification($notificationCreated);
					}
				}
			}

			Router::Location(Router::GetPath('talk') . 'read/' . $oTalk->getId() . '/');
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
            Router::Action('error'); return;
        }
    }

    /**
     * Чтение письма
     */
    protected function EventRead()
    {
        $this->sMenuSubItemSelect = 'read';
        /**
         * Получаем номер сообщения из УРЛ и проверяем существует ли оно
         */
        $sTalkId = $this->GetParam(0);
        if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById($sTalkId))) {
            parent::EventNotFound(); return;
        }
        /**
         * Пользователь есть в переписке?
         */
        if (!($oTalkUser = LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))) {
            parent::EventNotFound(); return;
        }
        /**
         * Пользователь активен в переписке?
         */
        if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
            parent::EventNotFound(); return;
        }
        /**
         * Обрабатываем добавление коммента
         */
        if (isset($_REQUEST['submit_comment'])) {
            $this->SubmitComment();
        }
        /**
         * Достаём комменты к сообщению
         */
        $aReturn = LS::Make(ModuleComment::class)->GetCommentsByTargetId($oTalk->getId(), 'talk');
        $iMaxIdComment = $aReturn['iMaxIdComment'];
        $aComments = $aReturn['comments'];

        $oTalkUser->setDateLast(date("Y-m-d H:i:s"));
        $oTalkUser->setCommentIdLast($iMaxIdComment);
        $oTalkUser->setCommentCountNew(0);
	LS::Make(ModuleTalk::class)->UpdateTalkUser($oTalkUser);

        LS::Make(ModuleViewer::class)->AddHtmlTitle($oTalk->getTitle());
        LS::Make(ModuleViewer::class)->Assign('oTalk', $oTalk);
        LS::Make(ModuleViewer::class)->Assign('aComments', $aComments);
        LS::Make(ModuleViewer::class)->Assign('iMaxIdComment', $iMaxIdComment);
        /**
         * Подсчитываем нужно ли отображать комментарии.
         * Комментарии не отображаются, если у вестки только один читатель
         * и ранее созданных комментариев нет.
         */
        if (count($aComments) == 0) {
            $iActiveSpeakers = 0;
            foreach ((array)$oTalk->getTalkUsers() as $oTalkUser) {
                if (($oTalkUser->getUserId() != $this->oUserCurrent->getId())
                    && $oTalkUser->getUserActive() == ModuleTalk::TALK_USER_ACTIVE) {
                    $iActiveSpeakers++;
                    break;
                }
            }
            if ($iActiveSpeakers == 0) {
                LS::Make(ModuleViewer::class)->Assign('bNoComments', true);
                $oTalkUser = LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId());
                $oTalkUser->setDateLast(date("Y-m-d H:i:s"));
                $oTalkUser->setCommentIdLast($iMaxIdComment);
                $oTalkUser->setCommentCountNew(0);
                LS::Make(ModuleTalk::class)->UpdateTalkUser($oTalkUser);
            }
        }
    }

    protected function EventReadComments()
    {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        /**
         * Получаем номер сообщения из УРЛ и проверяем существует ли оно
         */
        $sTalkId = $this->GetParam(0);
        if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById($sTalkId))) {
            parent::EventNotFound(); return;
        }
        /**
         * Пользователь есть в переписке?
         */
        if (!($oTalkUser = LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))) {
            parent::EventNotFound(); return;
        }
        /**
         * Пользователь активен в переписке?
         */
        if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
            parent::EventNotFound(); return;
        }

        /**
         * Достаём комменты к сообщению
         */
        $aReturn = LS::Make(ModuleComment::class)->GetCommentsByTargetId($oTalk->getId(), 'talk');
        $iMaxIdComment = $aReturn['iMaxIdComment'];
        $aComments = $aReturn['comments'];

        $aResult = array();
        $sReadlast = $oTalkUser->getDateLast();

        foreach ($aComments as $oComment) {
            $aComment = LS::Make(ModuleComment::class)->ConvertCommentToArray($oComment, $sReadlast);
            $aResult[$aComment['id']] = $aComment;
        }

        /**
         * Помечаем дату последнего просмотра
         */
        $oTalkUser->setDateLast(date("Y-m-d H:i:s"));
        $oTalkUser->setCommentIdLast($iMaxIdComment);
        $oTalkUser->setCommentCountNew(0);
        LS::Make(ModuleTalk::class)->UpdateTalkUser($oTalkUser);

        LS::Make(ModuleViewer::class)->AssignAjax('aComments', $aResult);
        LS::Make(ModuleViewer::class)->AssignAjax('sReadlast', $sReadlast);
        LS::Make(ModuleViewer::class)->AssignAjax('iMaxIdComment', $iMaxIdComment);
        LS::Make(ModuleViewer::class)->DisplayAjax();
    }

    /**
     * Проверка полей при создании письма
     *
     * @return bool
     */
    protected function checkTalkFields()
    {
        LS::Make(ModuleSecurity::class)->ValidateSendForm();

        $bOk = true;
        /**
         * Проверяем есть ли заголовок
         */
        if (!func_check(getRequestStr('talk_title'), 'text', 2, 200)) {
            LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('talk_create_title_error'), LS::Make(ModuleLang::class)->Get('error'));
            $bOk = false;
        }
        /**
         * Проверяем есть ли содержание топика
         */
        if (!func_check(getRequestStr('talk_text'), 'text', 2, Config::Get('module.comment.max_length'))) {
            LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('talk_create_text_error'), LS::Make(ModuleLang::class)->Get('error'));
            $bOk = false;
        }
        /**
         * Проверяем адресатов
         */
        $sUsers = getRequest('talk_users');
        $aUsers = explode(',', (string)$sUsers);
        $aUsersNew = array();
        $aUserInBlacklist = LS::Make(ModuleTalk::class)->GetBlacklistByTargetId($this->oUserCurrent->getId());

        $this->aUsersId = array();
        foreach ($aUsers as $sUser) {
            $sUser = trim($sUser);
            if ($sUser == '' or strtolower($sUser) == strtolower($this->oUserCurrent->getLogin())) {
                continue;
            }
            if ($oUser = LS::Make(ModuleUser::class)->GetUserByLogin($sUser) and $oUser->getActivate() == 1) {
                // Проверяем, попал ли отправиль в блек лист
                if (!in_array($oUser->getId(), $aUserInBlacklist)) {
                    $this->aUsersId[] = $oUser->getId();
                } else {
                    LS::Make(ModuleMessage::class)->AddError(
                        str_replace(
                            'login',
                            $oUser->getLogin(),
                            LS::Make(ModuleLang::class)->Get('talk_user_in_blacklist', array('login' => htmlspecialchars($oUser->getLogin())))
                        ),
                        LS::Make(ModuleLang::class)->Get('error')
                    );
                    $bOk = false;
                    continue;
                }
            } else {
                LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('talk_create_users_error_not_found') . ' «' . htmlspecialchars($sUser) . '»', LS::Make(ModuleLang::class)->Get('error'));
                $bOk = false;
            }
            $aUsersNew[] = $sUser;
        }
        if (!count($aUsersNew)) {
            LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('talk_create_users_error'), LS::Make(ModuleLang::class)->Get('error'));
            $_REQUEST['talk_users'] = '';
            $bOk = false;
        } else {
            if (count($aUsersNew) > Config::Get('module.talk.max_users') and !$this->oUserCurrent->isAdministrator()) {
                LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('talk_create_users_error_many'), LS::Make(ModuleLang::class)->Get('error'));
                $bOk = false;
            }
            $_REQUEST['talk_users'] = join(',', $aUsersNew);
        }
        /**
         * Выполнение хуков
         */
        LS::Make(ModuleHook::class)->Run('check_talk_fields', array('bOk' => &$bOk));

        return $bOk;
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
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $idCommentLast = getRequestStr('idCommentLast');
        /**
         * Проверям авторизован ли пользователь
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('need_authorization'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Проверяем разговор
         */
        if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById(getRequestStr('idTarget')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        if (!($oTalkUser = LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Получаем комментарии
         */
        $aReturn = LS::Make(ModuleComment::class)->GetCommentsNewByTargetId($oTalk->getId(), 'talk', $idCommentLast);
        $iMaxIdComment = $aReturn['iMaxIdComment'];
        $sReadlast = $oTalkUser->getDateLast();
        $aComments = array();

        $aCmts = $aReturn['comments'];
        if ($aCmts and is_array($aCmts)) {
            foreach ($aCmts as $aCmt) {
                $aComments[] = array(
                    'html' => $aCmt['html'],
                    'idParent' => $aCmt['obj']->getPid(),
                    'id' => $aCmt['obj']->getId(),
                );
            }
        }

        $idCommentLast = getRequestStr('idCommentLast', null, 'post');

        $aEditedComments = [];
        $aEditedCommentsRaw = LS::Make(ModuleComment::class)->GetCommentsOlderThenEdited('talk', $oTalk->getId(), $idCommentLast);
        foreach ($aEditedCommentsRaw as $oComment) {
            $aEditedComments[$oComment->getId()] = [
                'id' => $oComment->getId(),
                'text' => $oComment->getText(),
            ];
        }

        /**
         * Отмечаем дату прочтения письма
         */
        $oTalkUser->setDateLast(date("Y-m-d H:i:s"));
        if ($iMaxIdComment != 0) {
            $oTalkUser->setCommentIdLast($iMaxIdComment);
        }


        $oTalkUser->setCommentCountNew(0);
        LS::Make(ModuleTalk::class)->UpdateTalkUser($oTalkUser);

        LS::Make(ModuleViewer::class)->AssignAjax('aComments', $aComments);
        LS::Make(ModuleViewer::class)->AssignAjax('iMaxIdComment', $iMaxIdComment);
        LS::Make(ModuleViewer::class)->AssignAjax('aEditedComments', $aEditedComments);
        LS::Make(ModuleViewer::class)->AssignAjax('iUserCurrentCountTalkNew', LS::Make(ModuleTalk::class)->GetCountTalkNew($this->oUserCurrent->getId()));
    }

    /**
     * Обработка добавление комментария к письму через ajax
     *
     */
    protected function AjaxAddComment()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $this->SubmitComment();
    }

    /**
     * Обработка добавление комментария к письму
     *
     */
    protected function SubmitComment()
    {
        /**
         * Проверям авторизован ли пользователь
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('need_authorization'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Проверяем разговор
         */
        if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById(getRequestStr('cmt_target_id')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        if (!($oTalkUser = LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Проверяем разрешено ли отправлять инбокс по времени
         */
        if (!LS::Make(ModuleACL::class)->CanPostTalkCommentTime($this->oUserCurrent)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('talk_time_limit'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Проверяем текст комментария
         */
        $bMark = getRequestStr('form_comment_mark')=="on";
        if ($bMark)
            $sText = LS::Make(ModuleText::class)->Parser(LS::Make(ModuleText::class)->Mark(getRequestStr('comment_text')));
        else
            $sText = LS::Make(ModuleText::class)->Parser(getRequestStr('comment_text'));
        if (!func_check($sText, 'text', 2, Config::Get('module.comment.max_length'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('talk_comment_add_text_error'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Проверям на какой коммент отвечаем
         */
        $sParentId = (int)getRequest('reply');
        if (!func_check($sParentId, 'id')) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        $oCommentParent = null;
        if ($sParentId != 0) {
            /**
             * Проверяем существует ли комментарий на который отвечаем
             */
            if (!($oCommentParent = LS::Make(ModuleComment::class)->GetCommentById($sParentId))) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
                return;
            }
            /**
             * Проверяем из одного топика ли новый коммент и тот на который отвечаем
             */
            if ($oCommentParent->getTargetId() != $oTalk->getId()) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
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
        if (LS::Make(ModuleComment::class)->GetCommentUnique($oTalk->getId(), 'talk', $this->oUserCurrent->getId(), $sParentId, md5($sText))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('topic_comment_spam'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Создаём коммент
         */
        $oCommentNew = new EntityComment();
        $oCommentNew->setTargetId($oTalk->getId());
        $oCommentNew->setTargetType('talk');
        $oCommentNew->setUserId($this->oUserCurrent->getId());
        $oCommentNew->setText($sText);
        $oCommentNew->setDate(date("Y-m-d H:i:s"));
        $oCommentNew->setUserIp(func_getIp());
        $oCommentNew->setPid($sParentId);
        $oCommentNew->setTextHash(md5($sText));
        $oCommentNew->setPublish(1);
		$oCommentNew->setUserRank($this->oUserCurrent->getRank());
		$sFile = LS::Make(ModuleTopic::class)->UploadTopicImageUrl('http:'.$this->oUserCurrent->getProfileAvatarPath(64), $this->oUserCurrent, false);
		$oCommentNew->setUserAvatar($sFile);
        /**
         * Добавляем коммент
         */
        LS::Make(ModuleHook::class)->Run('talk_comment_add_before', array('oCommentNew' => $oCommentNew, 'oCommentParent' => $oCommentParent, 'oTalk' => $oTalk));
        if (LS::Make(ModuleComment::class)->AddComment($oCommentNew)) {
            LS::Make(ModuleHook::class)->Run('talk_comment_add_after', array('oCommentNew' => $oCommentNew, 'oCommentParent' => $oCommentParent, 'oTalk' => $oTalk));

            LS::Make(ModuleViewer::class)->AssignAjax('sCommentId', $oCommentNew->getId());
            $oTalk->setDateLast(date("Y-m-d H:i:s"));
            $oTalk->setUserIdLast($oCommentNew->getUserId());
            $oTalk->setCommentIdLast($oCommentNew->getId());
            $oTalk->setCountComment($oTalk->getCountComment() + 1);
            LS::Make(ModuleTalk::class)->UpdateTalk($oTalk);
            /**
             * Отсылаем уведомления всем адресатам
             */
            $aUsersTalk = LS::Make(ModuleTalk::class)->GetUsersTalk($oTalk->getId(), ModuleTalk::TALK_USER_ACTIVE);

            foreach ($aUsersTalk as $oUserTalk) {
                if ($oUserTalk->getId() != $oCommentNew->getUserId()) {
                    LS::Make(ModuleNotify::class)->SendTalkCommentNew($oUserTalk, $this->oUserCurrent, $oTalk, $oCommentNew);
					/**
					 * Отправка уведомления пользователям
					 */
					$notificationLink = "/talk/read/".$oCommentNew->getTargetId()."#comment".$oCommentNew->getId();
					$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() .
						"</a> ответил вам в личке <a href='".$notificationLink."'>".$oTalk->getTitle()."</a>";
					$notificationText = "";
					$notification = new EntityNotification(
						array(
							'user_id' => $oUserTalk->getId(),
							'text' => $notificationText,
							'title' => $notificationTitle,
							'link' => $notificationLink,
							'rating' => 0,
							'notification_type' => 2,
							'target_type' => 'comment',
							'target_id' => $oCommentNew->getId(),
							'sender_user_id' => $this->oUserCurrent->getId(),
							'group_target_type' => 'talk',
							'group_target_id' => $oCommentNew->getTargetId()
						)
					);
					if($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)){
						LS::Make(ModuleNower::class)->PostNotification($notificationCreated);
					}
                }
            }

            /**
             * Увеличиваем число новых комментов
             */
            LS::Make(ModuleTalk::class)->increaseCountCommentNew($oTalk->getId(), $oCommentNew->getUserId());
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'), LS::Make(ModuleLang::class)->Get('error'));
        }
    }

    /**
     * Добавление нового пользователя(-лей) в блек лист (ajax)
     *
     */
    public function AjaxAddToBlacklist()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $sUsers = getRequestStr('users', null, 'post');
        /**
         * Если пользователь не авторизирован, возвращаем ошибку
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('need_authorization'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        $aUsers = explode(',', $sUsers);
        /**
         * Получаем блекслист пользователя
         */
        $aUserBlacklist = LS::Make(ModuleTalk::class)->GetBlacklistByUserId($this->oUserCurrent->getId());

        $aResult = array();
        /**
         * Обрабатываем добавление по каждому из переданных логинов
         */
        foreach ($aUsers as $sUser) {
            $sUser = trim($sUser);
            if ($sUser == '') {
                continue;
            }
            /**
             * Если пользователь пытается добавить в блеклист самого себя,
             * возвращаем ошибку
             */
            if (strtolower($sUser) == strtolower($this->oUserCurrent->getLogin())) {
                $aResult[] = array(
                    'bStateError' => true,
                    'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                    'sMsg' => LS::Make(ModuleLang::class)->Get('talk_blacklist_add_self')
                );
                continue;
            }
            /**
             * Если пользователь не найден или неактивен, возвращаем ошибку
             */
            if ($oUser = LS::Make(ModuleUser::class)->GetUserByLogin($sUser) and $oUser->getActivate() == 1) {
                if (!isset($aUserBlacklist[$oUser->getId()])) {
                    if (LS::Make(ModuleTalk::class)->AddUserToBlackList($oUser->getId(), $this->oUserCurrent->getId())) {
                        $aResult[] = array(
                            'bStateError' => false,
                            'sMsgTitle' => LS::Make(ModuleLang::class)->Get('attention'),
                            'sMsg' => LS::Make(ModuleLang::class)->Get('talk_blacklist_add_ok', array('login' => htmlspecialchars($sUser))),
                            'sUserId' => $oUser->getId(),
                            'sUserLogin' => htmlspecialchars($sUser),
                            'sUserWebPath' => $oUser->getUserWebPath(),
                            'sUserAvatar48' => $oUser->getProfileAvatarPath(48)
                        );
                    } else {
                        $aResult[] = array(
                            'bStateError' => true,
                            'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                            'sMsg' => LS::Make(ModuleLang::class)->Get('system_error'),
                            'sUserLogin' => htmlspecialchars($sUser)
                        );
                    }
                } else {
                    /**
                     * Попытка добавить уже существующего в блеклисте пользователя, возвращаем ошибку
                     */
                    $aResult[] = array(
                        'bStateError' => true,
                        'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                        'sMsg' => LS::Make(ModuleLang::class)->Get('talk_blacklist_user_already_have', array('login' => htmlspecialchars($sUser))),
                        'sUserLogin' => htmlspecialchars($sUser)
                    );
                    continue;
                }
            } else {
                $aResult[] = array(
                    'bStateError' => true,
                    'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                    'sMsg' => LS::Make(ModuleLang::class)->Get('user_not_found', array('login' => htmlspecialchars($sUser))),
                    'sUserLogin' => htmlspecialchars($sUser)
                );
            }
        }
        /**
         * Передаем во вьевер массив с результатами обработки по каждому пользователю
         */
        LS::Make(ModuleViewer::class)->AssignAjax('aUsers', $aResult);
    }

    /**
     * Удаление пользователя из блек листа (ajax)
     *
     */
    public function AjaxDeleteFromBlacklist()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $idTarget = getRequestStr('idTarget', null, 'post');
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
        /**
         * Если пользователь не существуем, возращаем ошибку
         */
        if (!$oUserTarget = LS::Make(ModuleUser::class)->GetUserById($idTarget)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_not_found_by_id', array('id' => htmlspecialchars($idTarget))),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Получаем блеклист пользователя
         */
        $aBlacklist = LS::Make(ModuleTalk::class)->GetBlacklistByUserId($this->oUserCurrent->getId());
        /**
         * Если указанный пользователь не найден в блекслисте, возвращаем ошибку
         */
        if (!isset($aBlacklist[$oUserTarget->getId()])) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get(
                    'talk_blacklist_user_not_found',
                    array('login' => $oUserTarget->getLogin())
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Производим удаление пользователя из блекслиста
         */
        if (!LS::Make(ModuleTalk::class)->DeleteUserFromBlacklist($idTarget, $this->oUserCurrent->getId())) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        LS::Make(ModuleMessage::class)->AddNoticeSingle(
            LS::Make(ModuleLang::class)->Get(
                'talk_blacklist_delete_ok',
                array('login' => $oUserTarget->getLogin())
            ),
            LS::Make(ModuleLang::class)->Get('attention')
        );
    }

    /**
     * Удаление участника разговора (ajax)
     *
     */
    public function AjaxDeleteTalkUser()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $idTarget = getRequestStr('idTarget', null, 'post');
        $idTalk = getRequestStr('idTalk', null, 'post');
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
        /**
         * Если удаляемый участник не существует в базе данных, возвращаем ошибку
         */
        if (!$oUserTarget = LS::Make(ModuleUser::class)->GetUserById($idTarget)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_not_found_by_id', array('id' => htmlspecialchars($idTarget))),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Если разговор не найден, или пользователь не является его автором (либо админом), возвращаем ошибку
         */
        if ((!$oTalk = LS::Make(ModuleTalk::class)->GetTalkById($idTalk))
            || (($oTalk->getUserId() != $this->oUserCurrent->getId()) && !$this->oUserCurrent->isAdministrator())) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('talk_not_found'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Получаем список всех участников разговора
         */
        $aTalkUsers = $oTalk->getTalkUsers();
        /**
         * Если пользователь не является участником разговора или удалил себя самостоятельно  возвращаем ошибку
         */
        if (!isset($aTalkUsers[$idTarget])
            || $aTalkUsers[$idTarget]->getUserActive() == ModuleTalk::TALK_USER_DELETE_BY_SELF) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get(
                    'talk_speaker_user_not_found',
                    array('login' => $oUserTarget->getLogin())
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Удаляем пользователя из разговора,  если удаление прошло неудачно - возвращаем системную ошибку
         */
        if (!LS::Make(ModuleTalk::class)->DeleteTalkUserByArray($idTalk, $idTarget, ModuleTalk::TALK_USER_DELETE_BY_AUTHOR)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        LS::Make(ModuleMessage::class)->AddNoticeSingle(
            LS::Make(ModuleLang::class)->Get(
                'talk_speaker_delete_ok',
                array('login' => $oUserTarget->getLogin())
            ),
            LS::Make(ModuleLang::class)->Get('attention')
        );
    }

    /**
     * Приглашение участника разговора обратно (ajax)
     *
     */
    public function AjaxInviteTalkUserBack()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $idTarget = getRequestStr('idTarget', null, 'post');
        $idTalk = getRequestStr('idTalk', null, 'post');
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
        /**
         * Если приглашаемый участник не существует в базе данных, возвращаем ошибку
         */
        if (!$oUserTarget = LS::Make(ModuleUser::class)->GetUserById($idTarget)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_not_found_by_id', array('id' => htmlspecialchars($idTarget))),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Если разговор не найден, или пользователь не является его автором (либо админом), возвращаем ошибку
         */
        if ((!$oTalk = LS::Make(ModuleTalk::class)->GetTalkById($idTalk))
            || (($oTalk->getUserId() != $this->oUserCurrent->getId()) && !$this->oUserCurrent->isAdministrator())) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('talk_not_found'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Получаем список всех участников разговора
         */
        $aTalkUsers = $oTalk->getTalkUsers();
        /**
         * Если пользователь не удален возвращаем ошибку
         */
        if ($aTalkUsers[$idTarget]->getUserActive() == ModuleTalk::TALK_USER_INVITED_BACK) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get(
                    'talk_speaker_user_already_invited',
                    array('login' => $oUserTarget->getLogin())
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Если пользователь не удален возвращаем ошибку
         */
        if (!($aTalkUsers[$idTarget]->getUserActive() == ModuleTalk::TALK_USER_DELETE_BY_SELF
        || $aTalkUsers[$idTarget]->getUserActive() == ModuleTalk::TALK_USER_DELETE_BY_AUTHOR)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get(
                    'talk_speaker_user_not_found',
                    array('login' => $oUserTarget->getLogin())
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Приглашаем пользователя в разговора,  если приглашение прошло неудачно - возвращаем системную ошибку
         */
        if (!LS::Make(ModuleTalk::class)->DeleteTalkUserByArray($idTalk, $idTarget, ModuleTalk::TALK_USER_INVITED_BACK)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        LS::Make(ModuleMessage::class)->AddNoticeSingle(
            LS::Make(ModuleLang::class)->Get(
                'talk_speaker_invited_ok',
                array('login' => $oUserTarget->getLogin())
            ),
            LS::Make(ModuleLang::class)->Get('attention')
        );


        $notificationLink = "/talk/read/".$idTalk;
		$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() .
			"</a> приглашает вас вернуться в переписку <a href='".$notificationLink."'>".$oTalk->getTitle()."</a>";
		$notificationText = "<div id=\"accept_invite_talk_back\"><a href=\"#\" idTalk=\"" . $idTalk . "\" id=\"speaker_accept_restore_item_" . $idTarget .
			"\" class=\"delete\" onclick=\"ls.talk.acceptInviteBackToTalk(this)\">Вернуться в переписку</a></div>";
		$notification = new EntityNotification(
			array(
				'user_id' => $oUserTarget->getUserId(),
				'text' => $notificationText,
				'title' => $notificationTitle,
				'link' => "",
				'rating' => 0,
				'notification_type' => 14,
				'target_type' => "talk",
				'target_id' => $idTalk,
				'sender_user_id' => $this->oUserCurrent->getId(),
				'group_target_type' => 'nothing',
				'group_target_id' => -1
			)
		);
		if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
			LS::Make(ModuleNower::class)->PostNotification($notificationCreated);
		}
    }

    /**
     * Приглашение участника разговора обратно (ajax)
     *
     */
    public function AjaxAcceptInviteTalkUserBack()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $idTarget = getRequestStr('idTarget', null, 'post');
        $idTalk = getRequestStr('idTalk', null, 'post');
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
        /**
         * Если приглашенный участник не существует в базе данных, возвращаем ошибку
         */
        if (!$oUserTarget = LS::Make(ModuleUser::class)->GetUserById($idTarget)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_not_found_by_id', array('id' => htmlspecialchars($idTarget))),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
		/**
		 * Если юзер принимает не свое приглашение, возвращаем ошибку
		 */
        if ($this->oUserCurrent->getId() != $idTarget){
			LS::Make(ModuleMessage::class)->AddErrorSingle(
				LS::Make(ModuleLang::class)->Get('system_error'),
				LS::Make(ModuleLang::class)->Get('error')
			);
            return;
        }
        /**
         * Если разговор не найден возвращаем ошибку
         */
        if ((!$oTalk = LS::Make(ModuleTalk::class)->GetTalkById($idTalk))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('talk_not_found'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Получаем список всех участников разговора
         */
        $aTalkUsers = $oTalk->getTalkUsers();
        /**
         * Если пользователь участник, возвращаем ошибку
         */
        if ($aTalkUsers[$idTarget]->getUserActive() == ModuleTalk::TALK_USER_ACTIVE) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get(
                    'talk_speaker_user_already_exist',
                    array('login' => $oUserTarget->getLogin())
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Если пользователь не приглашен, возвращаем ошибку
         */
        if ($aTalkUsers[$idTarget]->getUserActive() != ModuleTalk::TALK_USER_INVITED_BACK) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get(
                    'talk_speaker_user_not_found',
                    array('login' => $oUserTarget->getLogin())
                ),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Активируем пользователя в разговора,  если активация прошла неудачно - возвращаем системную ошибку
         */
        if (!LS::Make(ModuleTalk::class)->DeleteTalkUserByArray($idTalk, $idTarget, ModuleTalk::TALK_USER_ACTIVE)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        LS::Make(ModuleMessage::class)->AddNoticeSingle(
            LS::Make(ModuleLang::class)->Get(
                'talk_speaker_add_ok',
                array('login' => $oUserTarget->getLogin())
            ),
            LS::Make(ModuleLang::class)->Get('attention')
        );
        return;
    }

    /**
     * Добавление нового участника разговора (ajax)
     *
     */
    public function AjaxAddTalkUser()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $sUsers = getRequestStr('users', null, 'post');
        $idTalk = getRequestStr('idTalk', null, 'post');
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
        /**
         * Если разговор не найден, или пользователь не является его автором (или админом), возвращаем ошибку
         */
        if ((!$oTalk = LS::Make(ModuleTalk::class)->GetTalkById($idTalk))
            || (($oTalk->getUserId() != $this->oUserCurrent->getId()) && !$this->oUserCurrent->isAdministrator())) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('talk_not_found'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            return;
        }
        /**
         * Получаем список всех участников разговора
         */
        $aTalkUsers = $oTalk->getTalkUsers();
        $aUsers = explode(',', $sUsers);
        /**
         * Получаем список пользователей, которые не принимают письма
         */
        $aUserInBlacklist = LS::Make(ModuleTalk::class)->GetBlacklistByTargetId($this->oUserCurrent->getId());
        /**
         * Ограничения на максимальное число участников разговора
         */
        if (count($aTalkUsers) >= Config::Get('module.talk.max_users') and !$this->oUserCurrent->isAdministrator()) {
            LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('talk_create_users_error_many'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        /**
         * Обрабатываем добавление по каждому переданному логину пользователя
         */
        foreach ($aUsers as $sUser) {
            $sUser = trim($sUser);
            if ($sUser == '') {
                continue;
            }
            /**
             * Попытка добавить себя
             */
            if (strtolower($sUser) == strtolower($this->oUserCurrent->getLogin())) {
                $aResult[] = array(
                    'bStateError' => true,
                    'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                    'sMsg' => LS::Make(ModuleLang::class)->Get('talk_speaker_add_self')
                );
                continue;
            }
            if (($oUser = LS::Make(ModuleUser::class)->GetUserByLogin($sUser))
                && ($oUser->getActivate() == 1)) {
                if (!in_array($oUser->getId(), $aUserInBlacklist)) {
                    if (array_key_exists($oUser->getId(), $aTalkUsers)) {
                        switch ($aTalkUsers[$oUser->getId()]->getUserActive()) {
                            /**
                             * Если пользователь ранее был удален админом разговора, то добавляем его снова
                             */
                            case ModuleTalk::TALK_USER_DELETE_BY_AUTHOR:
                                if (
                                LS::Make(ModuleTalk::class)->AddTalkUser(
                                    new EntityTalkUser(
                                        array(
                                            'talk_id' => $idTalk,
                                            'user_id' => $oUser->getId(),
                                            'date_last' => null,
                                            'talk_user_active' => ModuleTalk::TALK_USER_ACTIVE
                                        )
                                    )
                                )
                                ) {
                                    LS::Make(ModuleNotify::class)->SendTalkNew($oUser, $this->oUserCurrent, $oTalk);
                                    $aResult[] = array(
                                        'bStateError' => false,
                                        'sMsgTitle' => LS::Make(ModuleLang::class)->Get('attention'),
                                        'sMsg' => LS::Make(ModuleLang::class)->Get('talk_speaker_add_ok', array('login', htmlspecialchars($sUser))),
                                        'sUserId' => $oUser->getId(),
                                        'sUserLogin' => $oUser->getLogin(),
                                        'sUserLink' => $oUser->getUserWebPath(),
                                        'sUserWebPath' => $oUser->getUserWebPath(),
                                        'sUserAvatar48' => $oUser->getProfileAvatarPath(48)
                                    );
                                    $bState = true;
                                } else {
                                    $aResult[] = array(
                                        'bStateError' => true,
                                        'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                                        'sMsg' => LS::Make(ModuleLang::class)->Get('system_error')
                                    );
                                }
                                break;
                            /**
                             * Если пользователь является активным участником разговора, возвращаем ошибку
                             */
                            case ModuleTalk::TALK_USER_ACTIVE:
                                $aResult[] = array(
                                    'bStateError' => true,
                                    'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                                    'sMsg' => LS::Make(ModuleLang::class)->Get('talk_speaker_user_already_exist', array('login' => htmlspecialchars($sUser)))
                                );
                                break;
                            /**
                             * Если пользователь удалил себя из разговора самостоятельно, то блокируем повторное добавление
                             */
                            case ModuleTalk::TALK_USER_DELETE_BY_SELF:
                                $aResult[] = array(
                                    'bStateError' => true,
                                    'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                                    'sMsg' => LS::Make(ModuleLang::class)->Get('talk_speaker_delete_by_self', array('login' => htmlspecialchars($sUser)))
                                );
                                break;

                            default:
                                $aResult[] = array(
                                    'bStateError' => true,
                                    'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                                    'sMsg' => LS::Make(ModuleLang::class)->Get('system_error')
                                );
                        }
                    } elseif (
                    LS::Make(ModuleTalk::class)->AddTalkUser(
                        new EntityTalkUser(
                            array(
                                'talk_id' => $idTalk,
                                'user_id' => $oUser->getId(),
                                'date_last' => null,
                                'talk_user_active' => ModuleTalk::TALK_USER_ACTIVE
                            )
                        )
                    )
                    ) {
                        LS::Make(ModuleNotify::class)->SendTalkNew($oUser, $this->oUserCurrent, $oTalk);
                        $aResult[] = array(
                            'bStateError' => false,
                            'sMsgTitle' => LS::Make(ModuleLang::class)->Get('attention'),
                            'sMsg' => LS::Make(ModuleLang::class)->Get('talk_speaker_add_ok', array('login', htmlspecialchars($sUser))),
                            'sUserId' => $oUser->getId(),
                            'sUserLogin' => $oUser->getLogin(),
                            'sUserLink' => $oUser->getUserWebPath(),
                            'sUserWebPath' => $oUser->getUserWebPath(),
                            'sUserAvatar48' => $oUser->getProfileAvatarPath(48)
                        );
                        $bState = true;
                    } else {
                        $aResult[] = array(
                            'bStateError' => true,
                            'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                            'sMsg' => LS::Make(ModuleLang::class)->Get('system_error')
                        );
                    }
                } else {
                    /**
                     * Добавляем пользователь не принимает сообщения
                     */
                    $aResult[] = array(
                        'bStateError' => true,
                        'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                        'sMsg' => LS::Make(ModuleLang::class)->Get('talk_user_in_blacklist', array('login' => htmlspecialchars($sUser)))
                    );
                }
            } else {
                /**
                 * Пользователь не найден в базе данных или не активен
                 */
                $aResult[] = array(
                    'bStateError' => true,
                    'sMsgTitle' => LS::Make(ModuleLang::class)->Get('error'),
                    'sMsg' => LS::Make(ModuleLang::class)->Get('user_not_found', array('login' => htmlspecialchars($sUser)))
                );
            }
        }
        /**
         * Передаем во вьевер массив результатов обработки по каждому пользователю
         */
        LS::Make(ModuleViewer::class)->AssignAjax('aUsers', $aResult);
    }

    /**
     * Возвращает количество новых сообщений
     */
    public function AjaxNewMessages()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');

        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('need_authorization'), LS::Make(ModuleLang::class)->Get('error'));
            return;
        }
        $iCountTalkNew = LS::Make(ModuleTalk::class)->GetCountTalkNew($this->oUserCurrent->getId());
        LS::Make(ModuleViewer::class)->AssignAjax('iCountTalkNew', $iCountTalkNew);
    }

    /**
     * Обработка завершения работу экшена
     */
    public function EventShutdown()
    {
        if (!$this->oUserCurrent) {
            return;
        }
        $iCountTalkFavourite = LS::Make(ModuleTalk::class)->GetCountTalksFavouriteByUserId($this->oUserCurrent->getId());
        LS::Make(ModuleViewer::class)->Assign('iCountTalkFavourite', $iCountTalkFavourite);

        $iCountTopicFavourite = LS::Make(ModuleTopic::class)->GetCountTopicsFavouriteByUserId($this->oUserCurrent->getId());
        $iCountTopicUser = LS::Make(ModuleTopic::class)->GetCountTopicsPersonalByUser($this->oUserCurrent->getId(), 1);
        $iCountCommentUser = LS::Make(ModuleComment::class)->GetCountCommentsByUserId($this->oUserCurrent->getId(), 'topic');
        $iCountCommentFavourite = LS::Make(ModuleComment::class)->GetCountCommentsFavouriteByUserId($this->oUserCurrent->getId());
        $iCountNoteUser = LS::Make(ModuleUser::class)->GetCountUserNotesByUserId($this->oUserCurrent->getId());

        LS::Make(ModuleViewer::class)->Assign('oUserProfile', $this->oUserCurrent);
        LS::Make(ModuleViewer::class)->Assign('iCountWallUser', LS::Make(ModuleWall::class)->GetCountWall(array('wall_user_id' => $this->oUserCurrent->getId(), 'pid' => null)));
        /**
         * Общее число публикация и избранного
         */
        LS::Make(ModuleViewer::class)->Assign('iCountCreated', $iCountNoteUser + $iCountTopicUser + $iCountCommentUser);
        LS::Make(ModuleViewer::class)->Assign('iCountFavourite', $iCountCommentFavourite + $iCountTopicFavourite);
        LS::Make(ModuleViewer::class)->Assign('iCountFriendsUser', LS::Make(ModuleUser::class)->GetCountUsersFriend($this->oUserCurrent->getId()));

        LS::Make(ModuleViewer::class)->Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
        /**
         * Передаем во вьевер константы состояний участников разговора
         */
        LS::Make(ModuleViewer::class)->Assign('TALK_USER_ACTIVE', ModuleTalk::TALK_USER_ACTIVE);
        LS::Make(ModuleViewer::class)->Assign('TALK_USER_INVITED_BACK', ModuleTalk::TALK_USER_INVITED_BACK);
        LS::Make(ModuleViewer::class)->Assign('TALK_USER_DELETE_BY_SELF', ModuleTalk::TALK_USER_DELETE_BY_SELF);
        LS::Make(ModuleViewer::class)->Assign('TALK_USER_DELETE_BY_AUTHOR', ModuleTalk::TALK_USER_DELETE_BY_AUTHOR);
    }
}
