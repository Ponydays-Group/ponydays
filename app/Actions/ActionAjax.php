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

use App\Entities\EntityCommentOnline;
use App\Entities\EntityEditCommentData;
use App\Entities\EntityFavourite;
use App\Entities\EntityNotification;
use App\Entities\EntityTopic;
use App\Entities\EntityTopicQuestionVote;
use App\Entities\EntityUser;
use App\Entities\EntityVote;
use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleCast;
use App\Modules\ModuleComment;
use App\Modules\ModuleEditComment;
use App\Modules\ModuleFavourite;
use App\Modules\ModuleGeo;
use App\Modules\ModuleNotification;
use App\Modules\ModuleNower;
use App\Modules\ModuleRating;
use App\Modules\ModuleStream;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use App\Modules\ModuleVote;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleImage;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleLogger;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleText;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки ajax запросов
 * Ответ отдает в JSON фомате
 *
 * @package actions
 * @since   1.0
 */
class ActionAjax extends Action
{
    /**
     * Текущий пользователь
     *
     * @var EntityUser|null
     */
    protected $oUserCurrent = null;
    /**
     * @var ModuleViewer
     */
    protected $viewer = null;
    /**
     * @var \App\Modules\ModuleUser
     */
    protected $user = null;

    /**
     * Инициализация
     */
    public
    function Init()
    {
        $this->viewer = LS::Make(ModuleViewer::class);
        $this->user = LS::Make(ModuleUser::class);
        /**
         * Устанавливаем формат ответа
         */
        $this->viewer->SetResponseAjax('json');
        /**
         * Получаем текущего пользователя
         */
        $this->oUserCurrent = $this->user->GetUserCurrent();
    }

    /**
     * Регистрация евентов
     */
    protected
    function RegisterEvent()
    {
        $this->AddEventPreg('/^vote$/i', '/^comment$/', 'EventVoteComment');
        $this->AddEventPreg('/^vote$/i', '/^topic$/', 'EventVoteTopic');
        $this->AddEventPreg('/^vote$/i', '/^user$/', 'EventVoteUser');
        $this->AddEventPreg('/^vote$/i', '/^question$/', 'EventVoteQuestion');
        $this->AddEventPreg('/^favourite$/i', '/^save-tags/', 'EventFavouriteSaveTags');
        $this->AddEventPreg('/^favourite$/i', '/^topic$/', 'EventFavouriteTopic');
        $this->AddEventPreg('/^favourite$/i', '/^comment$/', 'EventFavouriteComment');
        $this->AddEventPreg('/^favourite$/i', '/^talk$/', 'EventFavouriteTalk');
        $this->AddEventPreg('/^stream$/i', '/^comment$/', 'EventStreamComment');
        $this->AddEventPreg('/^stream$/i', '/^topic$/', 'EventStreamTopic');
        $this->AddEventPreg('/^blogs$/i', '/^top$/', 'EventBlogsTop');
        $this->AddEventPreg('/^blogs$/i', '/^self$/', 'EventBlogsSelf');
        $this->AddEventPreg('/^blogs$/i', '/^join$/', 'EventBlogsJoin');
        $this->AddEventPreg('/^preview$/i', '/^text$/', 'EventPreviewText');
        $this->AddEventPreg('/^preview$/i', '/^topic/', 'EventPreviewTopic');
        $this->AddEventPreg('/^upload$/i', '/^image$/', 'EventUploadImage');
        $this->AddEventPreg('/^autocompleter$/i', '/^tag$/', 'EventAutocompleterTag');
        $this->AddEventPreg('/^autocompleter$/i', '/^user$/', 'EventAutocompleterUser');
        $this->AddEventPreg('/^comment$/i', '/^delete$/', 'EventCommentDelete');
        $this->AddEventPreg('/^geo/i', '/^get/', '/^regions$/', 'EventGeoGetRegions');
        $this->AddEventPreg('/^geo/i', '/^get/', '/^cities/', 'EventGeoGetCities');
        $this->AddEventPreg('/^infobox/i', '/^info/', '/^blog/', 'EventInfoboxInfoBlog');
        $this->AddEventPreg('/^askinvite/', 'EventInviteUser');
        $this->AddEvent('topic-lock-control', 'EventTopicLockControl');
        $this->AddEvent('get-object-votes', 'EventGetObjectVotes');
        $this->AddEventPreg('/^ignore$/i', 'EventIgnoreUser');
        $this->AddEventPreg('/^forbid-ignore$/i', 'EventForbidIgnoreUser');
        $this->AddEvent('editcomment-gethistory', 'EventGetHistory');
        $this->AddEvent('editcomment-getsource', 'EventGetSource');
        $this->AddEvent('editcomment-edit', 'EventEdit');
        $this->AddEventPreg('/^comment$/i', 'EventGetComment');
        $this->AddEvent('ban', 'EventBan');
    }

    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    protected
    function EventBan()
    {
        if (!$this->oUserCurrent
            || !($this->oUserCurrent->isAdministrator()
                || $this->oUserCurrent->isGlobalModerator())
        ) {
            Router::Action('error');

            return;
        }

        $iUserId = (int)getRequest('iUserId');

        if ((int)$this->oUserCurrent->getId() == $iUserId) {
            Router::Action('error');

            return;
        }

        if ((int)getRequest('iUnban')) {
            $this->user->Unban($iUserId);
            $sLogText = $this->oUserCurrent->getLogin()." разбанил пользователя ".$iUserId;
            LS::Make(ModuleLogger::class)->Notice($sLogText);

            $notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
                ."</a> разбанил вас на сайте";
            $notification = new EntityNotification(
                [
                    'user_id'           => $iUserId,
                    'text'              => "",
                    'title'             => $notificationTitle,
                    'link'              => "",
                    'rating'            => 0,
                    'notification_type' => 16,
                    'target_type'       => "global",
                    'target_id'         => -1,
                    'sender_user_id'    => $this->oUserCurrent->getId(),
                    'group_target_type' => 'global',
                    'group_target_id'   => -1
                ]
            );
            if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
                LS::Make(ModuleNower::class)->PostNotification($notificationCreated);
            }

            return;
        }

        $sBanComment = getRequest('sBanComment');
        $iBanHours = getRequest('iBanHours');

        if ((int)$iBanHours) {
            $t = time() + ((int)$iBanHours * 60 * 60);
            $this->user->Ban($iUserId, $this->oUserCurrent->getId(), date("Y-m-d H:i:s", $t), 0, $sBanComment);
        } else {
            $this->user->Ban($iUserId, $this->oUserCurrent->getId(), null, 1, $sBanComment);
        }

        $sLogText = $this->oUserCurrent->getLogin()." забанил пользователя ".$iUserId;
        LS::Make(ModuleLogger::class)->Notice($sLogText);

        $notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
            ."</a> забанил вас на сайте";
        $notification = new EntityNotification(
            [
                'user_id'           => $iUserId,
                'text'              => "",
                'title'             => $notificationTitle,
                'link'              => "",
                'rating'            => 0,
                'notification_type' => 16,
                'target_type'       => "global",
                'target_id'         => -1,
                'sender_user_id'    => $this->oUserCurrent->getId(),
                'group_target_type' => 'global',
                'group_target_id'   => -1
            ]
        );
        if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
            LS::Make(ModuleNower::class)->PostNotification($notificationCreated);
        }
    }

    /**
     * Вывод информации о блоге
     */
    protected
    function EventInfoboxInfoBlog()
    {
        /**
         * Если блог существует и он не персональный
         */
        if (!is_string(getRequest('iBlogId'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));

            return;
        }

        if (!($oBlog = LS::Make(ModuleBlog::class)->GetBlogById(getRequest('iBlogId'))) or $oBlog->getType()
            == 'personal'
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));

            return;
        }

        /**
         * Получаем локальный вьюер для рендеринга шаблона
         */
        $oViewer = $this->viewer->GetLocalViewer();
        $oViewer->Assign('oBlog', $oBlog);
        if ($oBlog->getType() != 'close' or $oBlog->getUserIsJoin()) {
            /**
             * Получаем последний топик
             */
            $aResult = LS::Make(ModuleTopic::class)->GetTopicsByFilter(
                [
                    'blog_id'       => $oBlog->getId(),
                    'topic_publish' => 1
                ],
                1,
                1
            );
            $oViewer->Assign('oTopicLast', reset($aResult['collection']));
        }

        $oViewer->Assign('oUserCurrent', $this->oUserCurrent);
        /**
         * Устанавливаем переменные для ajax ответа
         */
        $this->viewer->AssignAjax('sText', $oViewer->Fetch("infobox.info.blog.tpl"));
    }

    /**
     * Получение списка регионов по стране
     */
    protected
    function EventGeoGetRegions()
    {
        $iCountryId = getRequestStr('country');
        $iLimit = 200;
        if (is_numeric(getRequest('limit')) and getRequest('limit') > 0) {
            $iLimit = getRequest('limit');
        }

        /**
         * Находим страну
         */
        if (!($oCountry = LS::Make(ModuleGeo::class)->GetGeoObject('country', $iCountryId))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));

            return;
        }

        /**
         * Получаем список регионов
         */
        $aResult = LS::Make(ModuleGeo::class)->GetRegions(
            [
                'country_id' => $oCountry->getId()
            ],
            [
                'sort' => 'asc'
            ],
            1,
            $iLimit
        );
        $aRegions = [];
        foreach ($aResult['collection'] as $oObject) {
            $aRegions[] = [
                'id'   => $oObject->getId(),
                'name' => $oObject->getName(),
            ];
        }

        /**
         * Устанавливаем переменные для ajax ответа
         */
        $this->viewer->AssignAjax('aRegions', $aRegions);
    }

    /**
     * Получение списка городов по региону
     */
    protected
    function EventGeoGetCities()
    {
        $iRegionId = getRequestStr('region');
        $iLimit = 500;
        if (is_numeric(getRequest('limit')) and getRequest('limit') > 0) {
            $iLimit = getRequest('limit');
        }

        /**
         * Находим регион
         */
        if (!($oRegion = LS::Make(ModuleGeo::class)->GetGeoObject('region', $iRegionId))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));

            return;
        }

        /**
         * Получаем города
         */
        $aResult = LS::Make(ModuleGeo::class)->GetCities(
            [
                'region_id' => $oRegion->getId()
            ],
            [
                'sort' => 'asc'
            ],
            1,
            $iLimit
        );
        $aCities = [];
        foreach ($aResult['collection'] as $oObject) {
            $aCities[] = [
                'id'   => $oObject->getId(),
                'name' => $oObject->getName(),
            ];
        }

        /**
         * Устанавливаем переменные для ajax ответа
         */
        $this->viewer->AssignAjax('aCities', $aCities);
    }

    /**
     * Голосование за комментарий
     *
     */
    protected
    function EventVoteComment()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Комментарий существует?
         */
        if (!($oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequestStr('idComment', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_vote_error_noexists'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Голосует автор комментария?
         */
        if ($oComment->getUserId() == $this->oUserCurrent->getId()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_vote_error_self'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Время голосования истекло?
         */
        if (Config::Get('acl.vote.comment.limit_time') != 0
            && strtotime($oComment->getDate()) <= time() - Config::Get(
                'acl.vote.comment.limit_time'
            )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_vote_error_time'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Пользователь имеет право голоса?
         */
        if (!LS::Make(ModuleACL::class)->CanVoteComment($this->oUserCurrent, $oComment)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_vote_error'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Как именно голосует пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array(
            $iValue,
            [
                '1',
                '-1'
            ]
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_vote_error_value'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        $iVoteType = 0; //0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
        $iCountVote = 1;
        if ($oTopicCommentVote =
            LS::Make(ModuleVote::class)->GetVote($oComment->getId(), 'comment', $this->oUserCurrent->getId())
        ) {
            if ($iValue == $oTopicCommentVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
                $iVoteType = 2;
                $iCountVote = -1;
            } elseif ($oTopicCommentVote->getDirection() != 0) {
                $iValue += $iValue;
                $iVoteType = 1;
                $iCountVote = 0;
            }
            LS::Make(ModuleVote::class)->DeleteVote($oComment->getId(), 'comment', $this->oUserCurrent->getId());
        }

        $oTopicCommentVote = new EntityVote();
        $oTopicCommentVote->setTargetId($oComment->getId());
        $oTopicCommentVote->setTargetType('comment');
        $oTopicCommentVote->setVoterId($this->oUserCurrent->getId());
        $oTopicCommentVote->setDirection($iValueOld);
        $oTopicCommentVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float)LS::Make(ModuleRating::class)->VoteComment(
            $this->oUserCurrent,
            $oComment,
            $iValue,
            $iValueOld,
            $iCountVote,
            $iVoteType
        );
        $oTopicCommentVote->setValue($iVal);

        if (LS::Make(ModuleVote::class)->AddVote($oTopicCommentVote) and LS::Make(ModuleComment::class)->UpdateComment(
                $oComment
            )
        ) {
            if ($iValueOld == 0) {
                LS::Make(ModuleVote::class)->DeleteVote($oComment->getId(), 'comment', $this->oUserCurrent->getId());
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('comment_vote_deleted'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
            } else {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('comment_vote_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
            }
            $this->viewer->AssignAjax('iRating', $oComment->getRating());
            $this->viewer->AssignAjax('iCountVote', $oComment->getCountVote());
            /**
             * Добавляем событие в ленту
             */
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_vote_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    /**
     * Голосование за топик
     *
     */
    protected
    function EventVoteTopic()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Голосует автор топика?
         */
        if ($oTopic->getUserId() == $this->oUserCurrent->getId()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_vote_error_self'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Время голосования истекло?
         */
        if (strtotime($oTopic->getDateAdd()) <= time() - Config::Get('acl.vote.topic.limit_time')) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_vote_error_time'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Как проголосовал пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array(
            $iValue,
            [
                '1',
                '-1',
                '0'
            ]
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Права на голосование
         */
        if (!LS::Make(ModuleACL::class)->CanVoteTopic($this->oUserCurrent, $oTopic) and $iValue) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_vote_error'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        $iCountVote = 1;
        $iVoteType = 0; //0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
        if ($oTopicVote =
            LS::Make(ModuleVote::class)->GetVote($oTopic->getId(), 'topic', $this->oUserCurrent->getId())
        ) {
            if ($iValue == $oTopicVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
                $iVoteType = 2;
                $iCountVote = -1;
            } elseif ($oTopicVote->getDirection() != 0) {
                $iValue += $iValue;
                $iVoteType = 1;
                $iCountVote = 0;
            }
            LS::Make(ModuleVote::class)->DeleteVote($oTopic->getId(), 'topic', $this->oUserCurrent->getId());
        }

        $oTopicVote = new EntityVote();
        $oTopicVote->setTargetId($oTopic->getId());
        $oTopicVote->setTargetType('topic');
        $oTopicVote->setVoterId($this->oUserCurrent->getId());
        $oTopicVote->setDirection($iValueOld);
        $oTopicVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float)LS::Make(ModuleRating::class)->VoteTopic(
            $this->oUserCurrent,
            $oTopic,
            $iValue,
            $iValueOld,
            $iCountVote,
            $iVoteType
        );
        $oTopicVote->setValue($iVal);
        if ($iValue == 1) {
            $oTopic->setCountVoteUp($oTopic->getCountVoteUp() + 1);
        } elseif ($iValue == -1) {
            $oTopic->setCountVoteDown($oTopic->getCountVoteDown() + 1);
        } elseif ($iValue == 0) {
            $oTopic->setCountVoteAbstain($oTopic->getCountVoteAbstain() + 1);
        }

        if (LS::Make(ModuleVote::class)->AddVote($oTopicVote) and LS::Make(ModuleTopic::class)->UpdateTopic($oTopic)) {
            if ($iValue) {
                if ($iValueOld == 0) {
                    LS::Make(ModuleVote::class)->DeleteVote($oTopic->getId(), 'topic', $this->oUserCurrent->getId());
                    LS::Make(ModuleMessage::class)->AddNoticeSingle(
                        LS::Make(ModuleLang::class)->Get('topic_vote_deleted'),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );
                } else {
                    LS::Make(ModuleMessage::class)->AddNoticeSingle(
                        LS::Make(ModuleLang::class)->Get('topic_vote_ok'),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );
                }
            } else {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('topic_vote_ok_abstain'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
            }

            $this->viewer->AssignAjax('iRating', $oTopic->getRating());
            $this->viewer->AssignAjax('iCountVote', $oTopic->getCountVote());
            /**
             * Добавляем событие в ленту
             */
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    /**
     * Голосование за блог
     *
     */
    protected
    function EventVoteBlog()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Блог существует?
         */
        if (!($oBlog = LS::Make(ModuleBlog::class)->GetBlogById(getRequestStr('idBlog', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Голосует за свой блог?
         */
        if ($oBlog->getOwnerId() == $this->oUserCurrent->getId()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('blog_vote_error_self'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Имеет право на голосование?
         */
        switch (LS::Make(ModuleACL::class)->CanVoteBlog($this->oUserCurrent, $oBlog)) {
            case ModuleACL::CAN_VOTE_BLOG_ERROR_CLOSE:
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('blog_vote_error_close'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );

                return;
                break;

            default:
            case ModuleACL::CAN_VOTE_BLOG_FALSE:
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('blog_vote_error_acl'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );

                return;
                break;
        }

        /**
         * Как именно голосует пользователь
         * FIXME: Unreachable statement
         */
        /** @noinspection PhpUnreachableStatementInspection */
        $iValue = getRequestStr('value', null, 'post');
        if (in_array(
            $iValue,
            [
                '1',
                '-1'
            ]
        )
        ) {
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        if ($oBlogVote = LS::Make(ModuleVote::class)->GetVote($oBlog->getId(), 'blog', $this->oUserCurrent->getId())) {
            if ($iValue == $oBlogVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
            } elseif ($oBlogVote->getDirection() != 0) {
                $iValue += $iValue;
            }
            LS::Make(ModuleVote::class)->DeleteVote($oBlog->getId(), 'comment', $this->oUserCurrent->getId());
        }
        $oBlogVote = new EntityVote();
        $oBlogVote->setTargetId($oBlog->getId());
        $oBlogVote->setTargetType('blog');
        $oBlogVote->setVoterId($this->oUserCurrent->getId());
        $oBlogVote->setDirection($iValueOld);
        $oBlogVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float)LS::Make(ModuleRating::class)->VoteBlog($this->oUserCurrent, $oBlog, $iValue);
        $oBlogVote->setValue($iVal);
        $oBlog->setCountVote($oBlog->getCountVote() + 1);
        if (LS::Make(ModuleVote::class)->AddVote($oBlogVote) and LS::Make(ModuleBlog::class)->UpdateBlog($oBlog)) {
            $this->viewer->AssignAjax('iCountVote', $oBlog->getCountVote());
            $this->viewer->AssignAjax('iRating', $oBlog->getRating());
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                LS::Make(ModuleLang::class)->Get('blog_vote_ok'),
                LS::Make(ModuleLang::class)->Get('attention')
            );
            /**
             * Добавляем событие в ленту
             */
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }
    }

    /**
     * Голосование за пользователя
     *
     */
    protected
    function EventVoteUser()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Пользователь существует?
         */
        if (!($oUser = $this->user->GetUserById(getRequestStr('idUser', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Голосует за себя?
         */
        if ($oUser->getId() == $this->oUserCurrent->getId()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_vote_error_self'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Имеет право на голосование?
         */
        if (!LS::Make(ModuleACL::class)->CanVoteUser($this->oUserCurrent, $oUser)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_vote_error_acl'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        /**
         * Как проголосовал
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array(
            $iValue,
            [
                '1',
                '-1'
            ]
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }

        $iValueOld = $iValue;
        if ($oUserVote = LS::Make(ModuleVote::class)->GetVote($oUser->getId(), 'user', $this->oUserCurrent->getId())) {
            if ($iValue == $oUserVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
            } elseif ($oUserVote->getDirection() != 0) {
                $iValue += $iValue;
            }
            LS::Make(ModuleVote::class)->DeleteVote($oUser->getId(), 'user', $this->oUserCurrent->getId());
        }

        /**
         * Голосуем
         */
        $oUserVote = new EntityVote();
        $oUserVote->setTargetId($oUser->getId());
        $oUserVote->setTargetType('user');
        $oUserVote->setVoterId($this->oUserCurrent->getId());
        $oUserVote->setDirection($iValueOld);
        $oUserVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float)LS::Make(ModuleRating::class)->VoteUser($this->oUserCurrent, $oUser, $iValue);
        $oUserVote->setValue($iVal);

        if ($iValueOld != 0) {
            $oUser->setCountVote($oUser->getCountVote() + 1);
        } else {
            $oUser->setCountVote($oUser->getCountVote() - 1);
        }
        if (LS::Make(ModuleVote::class)->AddVote($oUserVote) and $this->user->Update($oUser)) {
            if ($iValueOld == 0) {
                LS::Make(ModuleVote::class)->DeleteVote($oUser->getId(), 'user', $this->oUserCurrent->getId());
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('user_vote_deleted'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
            } else {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('user_vote_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
            }
            $this->viewer->AssignAjax('iRating', $oUser->getRating());
            $this->viewer->AssignAjax('iSkill', $oUser->getSkill());
            $this->viewer->AssignAjax('iCountVote', $oUser->getCountVote());
            /**
             * Добавляем событие в ленту
             */
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    /**
     * Голосование за вариант ответа в опросе
     *
     */
    protected
    function EventVoteQuestion()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Параметры голосования
         */
        $idAnswer = getRequestStr('idAnswer', null, 'post');
        $idTopic = getRequestStr('idTopic', null, 'post');
        /**
         * Топик существует?
         */
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($idTopic))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Тип топика - опрос?
         */
        if ($oTopic->getType() != 'question') {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Уже голосовал?
         */
        if ($oTopicQuestionVote =
            LS::Make(ModuleTopic::class)->GetTopicQuestionVote($oTopic->getId(), $this->oUserCurrent->getId())
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_question_vote_already'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Вариант ответа
         */
        $aAnswer = $oTopic->getQuestionAnswers();
        if (!isset($aAnswer[$idAnswer]) and $idAnswer != -1) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if ($idAnswer == -1) {
            $oTopic->setQuestionCountVoteAbstain($oTopic->getQuestionCountVoteAbstain() + 1);
        } else {
            $oTopic->increaseQuestionAnswerVote($idAnswer);
        }

        $oTopic->setQuestionCountVote($oTopic->getQuestionCountVote() + 1);
        /**
         * Голосуем(отвечаем на опрос)
         */
        $oTopicQuestionVote = new EntityTopicQuestionVote();
        $oTopicQuestionVote->setTopicId($oTopic->getId());
        $oTopicQuestionVote->setVoterId($this->oUserCurrent->getId());
        $oTopicQuestionVote->setAnswer($idAnswer);
        if (LS::Make(ModuleTopic::class)->AddTopicQuestionVote($oTopicQuestionVote) and LS::Make(ModuleTopic::class)
                ->updateTopic($oTopic)
        ) {
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                LS::Make(ModuleLang::class)->Get('topic_question_vote_ok'),
                LS::Make(ModuleLang::class)->Get('attention')
            );
            $oViewer = $this->viewer->GetLocalViewer();
            $oViewer->Assign('oTopic', $oTopic);
            $this->viewer->AssignAjax('sText', $oViewer->Fetch("question_result.tpl"));
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    /**
     * Сохраняет теги для избранного
     *
     */
    protected
    function EventFavouriteSaveTags()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Объект уже должен быть в избранном
         */
        if ($oFavourite = LS::Make(ModuleFavourite::class)->GetFavourite(
            getRequestStr('target_id'),
            getRequestStr('target_type'),
            $this->oUserCurrent->getId()
        )
        ) {
            /**
             * Обрабатываем теги
             */
            $aTags = explode(',', trim(getRequestStr('tags'), "\r\n\t\0\x0B ."));
            $aTagsNew = [];
            $aTagsNewLow = [];
            $aTagsReturn = [];
            foreach ($aTags as $sTag) {
                $sTag = trim($sTag);
                if (func_check($sTag, 'text', 2, 50) and !in_array(mb_strtolower($sTag, 'UTF-8'), $aTagsNewLow)) {
                    $sTagEsc = htmlspecialchars($sTag);
                    $aTagsNew[] = $sTagEsc;
                    $aTagsReturn[] = [
                        'tag' => $sTagEsc,
                        'url' => $this->oUserCurrent->getUserWebPath().'favourites/'.$oFavourite->getTargetType()
                            .'s/tag/'.$sTagEsc.'/', // костыль для URL с множественным числом
                    ];
                    $aTagsNewLow[] = mb_strtolower($sTag, 'UTF-8');
                }
            }

            if (!count($aTagsNew)) {
                $oFavourite->setTags('');
            } else {
                $oFavourite->setTags(join(',', $aTagsNew));
            }

            $this->viewer->AssignAjax('aTags', $aTagsReturn);
            LS::Make(ModuleFavourite::class)->UpdateFavourite($oFavourite);

            return;
        }

        LS::Make(ModuleMessage::class)->AddErrorSingle(
            LS::Make(ModuleLang::class)->Get('system_error'),
            LS::Make(ModuleLang::class)->Get('error')
        );
    }

    /**
     * Обработка избранного - топик
     *
     */
    protected
    function EventFavouriteTopic()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array(
            $iType,
            [
                '1',
                '0'
            ]
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Пропускаем топик из черновиков
         */
        if (!$oTopic->getPublish()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('error_favorite_topic_is_draft'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Топик уже в избранном?
         */
        $oFavouriteTopic =
            LS::Make(ModuleTopic::class)->GetFavouriteTopic($oTopic->getId(), $this->oUserCurrent->getId());
        if (!$oFavouriteTopic and $iType) {
            $oFavouriteTopicNew = new EntityFavourite(
                [
                    'target_id'      => $oTopic->getId(),
                    'user_id'        => $this->oUserCurrent->getId(),
                    'target_type'    => 'topic',
                    'target_publish' => $oTopic->getPublish()
                ]
            );
            $oTopic->setCountFavourite($oTopic->getCountFavourite() + 1);
            if (LS::Make(ModuleTopic::class)->AddFavouriteTopic($oFavouriteTopicNew) and LS::Make(ModuleTopic::class)
                    ->UpdateTopic($oTopic)
            ) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('topic_favourite_add_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('bState', true);
                $this->viewer->AssignAjax('iCount', $oTopic->getCountFavourite());
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        }

        if (!$oFavouriteTopic and !$iType) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_favourite_add_no'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if ($oFavouriteTopic and $iType) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_favourite_add_already'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if ($oFavouriteTopic and !$iType) {
            $oTopic->setCountFavourite($oTopic->getCountFavourite() - 1);
            if (LS::Make(ModuleTopic::class)->DeleteFavouriteTopic($oFavouriteTopic) and LS::Make(ModuleTopic::class)
                    ->UpdateTopic($oTopic)
            ) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('topic_favourite_del_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('bState', false);
                $this->viewer->AssignAjax('iCount', $oTopic->getCountFavourite());
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        }
    }

    /**
     * Обработка избранного - комментарий
     *
     */
    protected
    function EventFavouriteComment()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array(
            $iType,
            [
                '1',
                '0'
            ]
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Комментарий существует?
         */
        if (!($oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequestStr('idComment', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Запрет на добавление удаленного комментария
         */
        if ($iType === '1' and $oComment->getDelete()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Комментарий уже в избранном?
         */
        $oFavouriteComment =
            LS::Make(ModuleComment::class)->GetFavouriteComment($oComment->getId(), $this->oUserCurrent->getId());
        if (!$oFavouriteComment and $iType) {
            $oFavouriteCommentNew = new EntityFavourite(
                [
                    'target_id'      => $oComment->getId(),
                    'target_type'    => 'comment',
                    'user_id'        => $this->oUserCurrent->getId(),
                    'target_publish' => $oComment->getPublish()
                ]
            );
            $oComment->setCountFavourite($oComment->getCountFavourite() + 1);
            if (LS::Make(ModuleComment::class)->AddFavouriteComment($oFavouriteCommentNew) and LS::Make(
                    ModuleComment::class
                )->UpdateComment($oComment)
            ) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('comment_favourite_add_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('bState', true);
                $this->viewer->AssignAjax('iCount', $oComment->getCountFavourite());
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        }

        if (!$oFavouriteComment and !$iType) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_favourite_add_no'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if ($oFavouriteComment and $iType) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('comment_favourite_add_already'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if ($oFavouriteComment and !$iType) {
            $oComment->setCountFavourite($oComment->getCountFavourite() - 1);
            if (LS::Make(ModuleComment::class)->DeleteFavouriteComment($oFavouriteComment) and LS::Make(
                    ModuleComment::class
                )->UpdateComment($oComment)
            ) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('comment_favourite_del_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('bState', false);
                $this->viewer->AssignAjax('iCount', $oComment->getCountFavourite());
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        }
    }

    /**
     * Обработка избранного - личное сообщение
     *
     */
    protected
    function EventFavouriteTalk()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array(
            $iType,
            [
                '1',
                '0'
            ]
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         *    Сообщение существует?
         */
        if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById(getRequestStr('idTalk', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Сообщение уже в избранном?
         */
        $oFavouriteTalk = LS::Make(ModuleTalk::class)->GetFavouriteTalk($oTalk->getId(), $this->oUserCurrent->getId());
        if (!$oFavouriteTalk and $iType) {
            $oFavouriteTalkNew = new EntityFavourite(
                [
                    'target_id'      => $oTalk->getId(),
                    'target_type'    => 'talk',
                    'user_id'        => $this->oUserCurrent->getId(),
                    'target_publish' => '1'
                ]
            );
            if (LS::Make(ModuleTalk::class)->AddFavouriteTalk($oFavouriteTalkNew)) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('talk_favourite_add_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('bState', true);
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        }

        if (!$oFavouriteTalk and !$iType) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('talk_favourite_add_no'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if ($oFavouriteTalk and $iType) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('talk_favourite_add_already'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if ($oFavouriteTalk and !$iType) {
            if (LS::Make(ModuleTalk::class)->DeleteFavouriteTalk($oFavouriteTalk)) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('talk_favourite_del_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('bState', false);
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        }
    }

    /**
     * Обработка получения последних комментов
     * Используется в блоке "Прямой эфир"
     *
     */
    protected
    function EventStreamComment()
    {
        if ($aComments = LS::Make(ModuleComment::class)->GetCommentsOnline('topic', Config::Get('block.stream.row'))) {
            $oViewer = $this->viewer->GetLocalViewer();
            $oViewer->Assign('aComments', $aComments);
            $sTextResult = $oViewer->Fetch("blocks/block.stream_comment.tpl");
            $this->viewer->AssignAjax('sText', $sTextResult);
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('block_stream_comments_no'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }
    }

    /**
     * Обработка получения последних топиков
     * Используется в блоке "Прямой эфир"
     *
     */
    protected
    function EventStreamTopic()
    {
        if ($oTopics = LS::Make(ModuleTopic::class)->GetTopicsLast(Config::Get('block.stream.row'))) {
            $oViewer = $this->viewer->GetLocalViewer();
            $oViewer->Assign('oTopics', $oTopics);
            $sTextResult = $oViewer->Fetch("blocks/block.stream_topic.tpl");
            $this->viewer->AssignAjax('sText', $sTextResult);
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('block_stream_topics_no'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }
    }

    /**
     * Обработка получения TOP блогов
     * Используется в блоке "TOP блогов"
     *
     */
    protected
    function EventBlogsTop()
    {
        /**
         * Получаем список блогов и формируем ответ
         */
        if ($aResult = LS::Make(ModuleBlog::class)->GetBlogsRating(1, Config::Get('block.blogs.row'))) {
            $aBlogs = $aResult['collection'];
            $oViewer = $this->viewer->GetLocalViewer();
            $oViewer->Assign('aBlogs', $aBlogs);
            $sTextResult = $oViewer->Fetch("blocks/block.blogs_top.tpl");
            $this->viewer->AssignAjax('sText', $sTextResult);
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    /**
     * Обработка получения своих блогов
     * Используется в блоке "TOP блогов"
     *
     */
    protected
    function EventBlogsSelf()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Получаем список блогов и формируем ответ
         */
        if ($aBlogs = LS::Make(ModuleBlog::class)->GetBlogsRatingSelf(
            $this->oUserCurrent->getId(),
            Config::Get('block.blogs.row')
        )
        ) {
            $oViewer = $this->viewer->GetLocalViewer();
            $oViewer->Assign('aBlogs', $aBlogs);
            $sTextResult = $oViewer->Fetch("blocks/block.blogs_top.tpl");
            $this->viewer->AssignAjax('sText', $sTextResult);
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('block_blogs_self_error'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }
    }

    /**
     * Обработка получения подключенных блогов
     * Используется в блоке "TOP блогов"
     *
     */
    protected
    function EventBlogsJoin()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Получаем список блогов и формируем ответ
         */
        if ($aBlogs = LS::Make(ModuleBlog::class)->GetBlogsRatingJoin(
            $this->oUserCurrent->getId(),
            Config::Get('block.blogs.row')
        )
        ) {
            $oViewer = $this->viewer->GetLocalViewer();
            $oViewer->Assign('aBlogs', $aBlogs);
            $sTextResult = $oViewer->Fetch("blocks/block.blogs_top.tpl");
            $this->viewer->AssignAjax('sText', $sTextResult);
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('block_blogs_join_error'),
                LS::Make(ModuleLang::class)->Get('attention')
            );

            return;
        }
    }

    /**
     * Предпросмотр топика
     *
     */
    protected
    function EventPreviewTopic()
    {
        /**
         * Т.к. используется обработка отправки формы, то устанавливаем тип ответа 'jsonIframe' (тот же JSON только обернутый в textarea)
         * Это позволяет избежать ошибок в некоторых браузерах, например, Opera
         */
        $this->viewer->SetResponseAjax('jsonIframe', false);
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Допустимый тип топика?
         */
        if (!LS::Make(ModuleTopic::class)->IsAllowTopicType($sType = getRequestStr('topic_type'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('topic_create_type_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Создаем объект топика для валидации данных
         */
        $oTopic = new EntityTopic();
        $oTopic->_setValidateScenario($sType); // зависит от типа топика
        $oTopic->setTitle(strip_tags(getRequestStr('topic_title')));
        $oTopic->setTextSource(getRequestStr('topic_text'));
        $oTopic->setTags(getRequestStr('topic_tags'));
        $oTopic->setDateAdd(date("Y-m-d H:i:s"));
        $oTopic->setUserId($this->oUserCurrent->getId());
        $oTopic->setType($sType);
        /**
         * Валидируем необходимые поля топика
         */
        $oTopic->_Validate(
            [
                'topic_title',
                'topic_text',
                'topic_tags',
                'topic_type'
            ],
            false
        );
        if ($oTopic->_hasValidateErrors()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($oTopic->_getValidateError());

            return;
        }

        /**
         * Формируем текст топика
         */
        list($sTextShort, $sTextNew, $sTextCut) = LS::Make(ModuleText::class)->Cut($oTopic->getTextSource());
        $oTopic->setCutText($sTextCut);
        $oTopic->setText(LS::Make(ModuleText::class)->Parser($sTextNew));
        $oTopic->setTextShort(LS::Make(ModuleText::class)->Parser($sTextShort));
        /**
         * Рендерим шаблон для предпросмотра топика
         */
        $oViewer = $this->viewer->GetLocalViewer();
        $oViewer->Assign('oTopic', $oTopic);
        $sTemplate = "topic_preview_{$oTopic->getType() }.tpl";
        if (!$this->viewer->TemplateExists($sTemplate)) {
            $sTemplate = 'topic_preview_topic.tpl';
        }

        $sTextResult = $oViewer->Fetch($sTemplate);
        /**
         * Передаем результат в ajax ответ
         */
        $this->viewer->AssignAjax('sText', $sTextResult);

        return;
    }

    /**
     * Предпросмотр текста
     *
     */
    protected
    function EventPreviewText()
    {
        $sText = getRequestStr('text', null, 'post');
        $bSave = getRequest('save', null, 'post');
        /**
         * Экранировать или нет HTML теги
         */
        if ($bSave) {
            $sTextResult = htmlspecialchars($sText);
        } else {
            if (getRequestStr('form_comment_mark') == "on") {
                $sTextResult = LS::Make(ModuleText::class)->Parser(LS::Make(ModuleText::class)->Mark($sText));
            } else {
                $sTextResult = LS::Make(ModuleText::class)->Parser($sText);
            }
        }

        $sTextResult = preg_replace_callback(
            '/@(.*?)\((.*?)\)/',
            function ($matches) {
                $sLogin = $matches[1];
                $sNick = $matches[2];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sNick."</a>";
                if ($oTargetUser = $this->user->getUserByLogin($sLogin)) {
                    return $r;
                }

                return $matches[0];
            },
            $sTextResult
        );
        $sTextResult = preg_replace_callback(
            '/@([a-zA-Zа-яА-Я0-9-_]+)/',
            function ($matches) {
                $sLogin = $matches[1];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sLogin."</a>";
                if ($oTargetUser = $this->user->getUserByLogin($sLogin)) {
                    return $r;
                }

                return $matches[0];
            },
            $sTextResult
        );

        /**
         * Передаем результат в ajax ответ
         */
        $this->viewer->AssignAjax('sText', $sTextResult);
    }

    /**
     * Загрузка изображения
     *
     */
    protected
    function EventUploadImage()
    {
        /**
         * Т.к. используется обработка отправки формы, то устанавливаем тип ответа 'jsonIframe' (тот же JSON только обернутый в textarea)
         * Это позволяет избежать ошибок в некоторых браузерах, например, Opera
         */
        $this->viewer->SetResponseAjax('json', false);
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $sFile = null;
        $sText = '';
        if (isPost('img_url') && $_REQUEST['img_url'] != '' && $_REQUEST['img_url'] != 'http://') {
            /**
             * Загрузка файла по URl
             */
            $sFile = LS::Make(ModuleTopic::class)->UploadTopicImageUrl($_REQUEST['img_url'], $this->oUserCurrent);
            switch (true) {
                case is_string($sFile):
                    break;

                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_READ):
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('uploadimg_url_error_read'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_SIZE):
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('uploadimg_url_error_size'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_TYPE):
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('uploadimg_url_error_type'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                default:
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR):
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('uploadimg_url_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
            }

            if ($sFile) {
                $sText = LS::Make(ModuleImage::class)->BuildHTML($sFile, $_REQUEST);
            }
        } elseif (isPost('img_base64')) {
            /**
             * Загрузка файла из Base64
             */
            $sFile = LS::Make(ModuleTopic::class)->UploadTopicImagebase64($_REQUEST['img_base64'], $this->oUserCurrent);
            switch (true) {
                case is_string($sFile):
                    break;

                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_READ):
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('uploadimg_url_error_read'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_SIZE):
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('uploadimg_url_error_size'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_TYPE):
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('uploadimg_url_error_type'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                default:
                case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR):
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('uploadimg_url_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
            }

            if ($sFile) {
                $sText = LS::Make(ModuleImage::class)->BuildHTML($sFile, $_REQUEST);
            }
        } else {
            function reArrayFiles(&$file_post)
            {
                $file_ary = [];
                $file_count = count($file_post['name']);
                $file_keys = array_keys($file_post);

                for ($i = 0; $i < $file_count; $i++) {
                    foreach ($file_keys as $key) {
                        $file_ary[$i][$key] = $file_post[$key][$i];
                    }
                }

                return $file_ary;
            }

            $sText = "";
            $aFiles = reArrayFiles($_FILES['img_file']);

            foreach ($aFiles as $k => $v) {
                /**
                 * Был выбран файл с компьютера и он успешно зугрузился?
                 */

                if (is_uploaded_file($v['tmp_name'])) {
                    if (!$sFile = LS::Make(ModuleTopic::class)->UploadTopicImageFile($v, $this->oUserCurrent)) {
                        LS::Make(ModuleMessage::class)->AddErrorSingle(
                            LS::Make(ModuleLang::class)->Get('uploadimg_file_error'),
                            LS::Make(ModuleLang::class)->Get('error')
                        );

                        return;
                    }

                    /**
                     * Если файл успешно загружен, формируем HTML вставки и возвращаем в ajax ответе
                     */
                    if ($sFile) {
                        if ($_REQUEST['just_url']) {
                            $sText .= $sFile;
                        } else {
                            $sText .= LS::Make(ModuleImage::class)->BuildHTML($sFile, $_REQUEST);
                        }
                    }
                }
            } //foreach
        }

        $this->viewer->AssignAjax('sText', $sText);
    }

    /**
     * Автоподставновка тегов
     *
     */
    protected
    function EventAutocompleterTag()
    {
        /**
         * Первые буквы тега переданы?
         */
        if (!($sValue = getRequest('value', null, 'post')) or !is_string($sValue)) {
            return;
        }

        $aItems = [];
        /**
         * Формируем список тегов
         */
        $aTags = LS::Make(ModuleTopic::class)->GetTopicTagsByLike($sValue, 10);
        foreach ($aTags as $oTag) {
            $aItems[] = $oTag->getText();
        }

        /**
         * Передаем результат в ajax ответ
         */
        $this->viewer->AssignAjax('aItems', $aItems);
    }

    /**
     * Автоподставновка пользователей
     *
     */
    protected
    function EventAutocompleterUser()
    {
        /**
         * Первые буквы логина переданы?
         */
        if (!($sValue = getRequest('value', null, 'post')) or !is_string($sValue)) {
            return;
        }

        $aItems = [];
        /**
         * Формируем список пользователей
         */
        $aUsers = $this->user->GetUsersByLoginLike($sValue, 10);
        foreach ($aUsers as $oUser) {
            $aItems[] = $oUser->getLogin();
        }

        /**
         * Передаем результат в ajax ответ
         */
        $this->viewer->AssignAjax('aItems', $aItems);
    }

    /**
     * Удаление/восстановление комментария
     *
     */
    protected
    function EventCommentDelete()
    {
        /**
         * Есть права на удаление комментария?
         */
        /**
         * Комментарий существует?
         */
        $idComment = getRequestStr('idComment', null, 'post');
        $sDeleteReason = getRequestStr('sDeleteReason', null, 'post');
        if (!($oComment = LS::Make(ModuleComment::class)->GetCommentById($idComment))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if (!LS::Make(ModuleACL::class)->UserCanDeleteComment($this->oUserCurrent, $oComment, 1)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $lastdeleteUser = $oComment->getDeleteUserId();
        /**
         * Устанавливаем пометку о том, что комментарий удален
         */
        $oComment->setDelete(($oComment->getDelete() + 1) % 2);
        $oComment->setDeleteReason($sDeleteReason);
        $oComment->setDeleteUserId($this->oUserCurrent->getId());
        LS::Make(ModuleHook::class)->Run(
            'comment_delete_before',
            [
                'oComment' => $oComment
            ]
        );
        if (!LS::Make(ModuleComment::class)->UpdateCommentStatus($oComment)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        LS::Make(ModuleHook::class)->Run(
            'comment_delete_after',
            [
                'oComment' => $oComment
            ]
        );
        /**
         * Формируем текст ответа
         */
        if ($bState = (bool)$oComment->getDelete()) {
            $sMsg = LS::Make(ModuleLang::class)->Get('comment_delete_ok');
            $sTextToggle = LS::Make(ModuleLang::class)->Get('comment_repair');
            $sLogText = $this->oUserCurrent->getLogin()." удалил комментарий ".$oComment->getId();
            LS::Make(ModuleLogger::class)->Notice($sLogText);
        } else {
            $sMsg = LS::Make(ModuleLang::class)->Get('comment_repair_ok');
            $sTextToggle = LS::Make(ModuleLang::class)->Get('comment_delete');
            $sLogText = $this->oUserCurrent->getLogin()." восстановил комментарий ".$oComment->getId();
            LS::Make(ModuleLogger::class)->Notice($sLogText);
        }

        /**
         * Отправка уведомления пользователям
         */
        $notificationLink =
            LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId(
            );
        if ((bool)$oComment->getDelete()) {
            $notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
                ."</a> удалил ваш <a href='".$notificationLink."'>комментарий</a>\nПричина: "
                .$oComment->getDeleteReason();
            $notificationType = 7;
        } else {
            $notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
                ."</a> восстановил ваш <a href='".$notificationLink."'>комментарий</a>";
            $notificationType = 8;
        }
        $notificationText = "";
        $notification = new EntityNotification(
            [
                'user_id'           => $oComment->getUserId(),
                'text'              => $notificationText,
                'title'             => $notificationTitle,
                'link'              => $notificationLink,
                'rating'            => 0,
                'notification_type' => $notificationType,
                'target_type'       => 'comment',
                'target_id'         => $oComment->getId(),
                'sender_user_id'    => $this->oUserCurrent->getId(),
                'group_target_type' => 'topic',
                'group_target_id'   => $oComment->getTargetId()
            ]
        );
        if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
            LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
        }

        if ($lastdeleteUser && $this->oUserCurrent->getId() != $lastdeleteUser) {
            $notificationLink =
                LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId())->getUrl()."#comment"
                .$oComment->getId();
            $notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
                ."</a> восстановил удаленный вами <a href='".$notificationLink."'>комментарий</a>";
            $notificationText = "";
            $notification = new EntityNotification(
                [
                    'user_id'           => $lastdeleteUser,
                    'text'              => $notificationText,
                    'title'             => $notificationTitle,
                    'link'              => $notificationLink,
                    'rating'            => 0,
                    'notification_type' => 9,
                    'target_type'       => 'comment',
                    'target_id'         => $oComment->getId(),
                    'sender_user_id'    => $this->oUserCurrent->getId(),
                    'group_target_type' => 'topic',
                    'group_target_id'   => $oComment->getTargetId()
                ]
            );
            if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
                LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
            }
        }

        /**
         * Обновление события в ленте активности
         */
        LS::Make(ModuleStream::class)->write(
            $oComment->getUserId(),
            'add_comment',
            $oComment->getId(),
            !$oComment->getDelete()
        );
        /**
         * Показываем сообщение и передаем переменные в ajax ответ
         */
        LS::Make(ModuleMessage::class)->AddNoticeSingle($sMsg, LS::Make(ModuleLang::class)->Get('attention'));
        $this->viewer->AssignAjax('bState', $bState);
        $this->viewer->AssignAjax('sTextToggle', $sTextToggle);

    }

    protected
    function EventInviteUser()
    {
        $a = $_POST["to"];
        $oUserCurrent = $this->user->_GetUserCurrent();
        $oBlog = LS::Make(ModuleBlog::class)->GetBlogById($_POST["blog"]);
        LS::Make(ModuleTalk::class)->SendTalk(
            "Просьба об инвайте",
            "Пользователь <a href='"."/profile/".$oUserCurrent->getLogin()."/' class='user'>"
            ."<i class='icon-user'></i>".$oUserCurrent->getLogin()."</a> просит пригласить его в блог <a href='"
            .$oBlog->getUrlFull()."'>".$oBlog->getTitle()."</a>.",
            $oUserCurrent->getId(),
            $a
        );
    }

    protected
    function EventTopicLockControl()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $isAllowLockControlTopic = LS::Make(ModuleACL::class)->IsAllowLockTopicControl($oTopic, $this->oUserCurrent);
        if (!$isAllowLockControlTopic) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $bLockState = getRequestStr('bState', null, 'post') == '1';
        $bStateOld = $oTopic->isControlLocked();
        $oTopic->setLockControl($bLockState);
        if ($bStateOld == $bLockState || LS::Make(ModuleTopic::class)->UpdateControlLock($oTopic)) {
            $sNotice = $bLockState ? 'topic_control_locked' : 'topic_control_unlocked';
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                LS::Make(ModuleLang::class)->Get($sNotice),
                LS::Make(ModuleLang::class)->Get('attention')
            );
            $this->viewer->AssignAjax('bState', $oTopic->isControlLocked());
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    protected
    function EventGetObjectVotes()
    {
        $targetId = (int)getRequestStr('targetId', null, 'post');
        $targetType = getRequestStr('targetType', null, 'post');
        switch ($targetType) {
            case 'comment':
                $oTarget = LS::Make(ModuleComment::class)->GetCommentById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.comment.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.comment.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.comment.oe_end');
                $date_sort = Config::Get('acl.vote_list.comment.date_sort');
                break;
            case 'topic':
                $oTarget = LS::Make(ModuleTopic::class)->GetTopicById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.topic.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.topic.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.topic.oe_end');
                $date_sort = Config::Get('acl.vote_list.topic.date_sort');
                break;
            case 'blog':
                $oTarget = LS::Make(ModuleBlog::class)->GetBlogById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.blog.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.blog.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.blog.oe_end');
                $date_sort = Config::Get('acl.vote_list.blog.date_sort');
                break;
            case 'user':
                $oTarget = $this->user->GetUserById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.user.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.user.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.user.oe_end');
                $date_sort = Config::Get('acl.vote_list.user.date_sort');
                break;
            default:
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
        }

        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent && $ne_enable_level < 8) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Объект существует?
         */
        if (!$oTarget) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if (!LS::Make(ModuleACL::class)->CheckSimpleAccessLevel(
            $ne_enable_level,
            $this->oUserCurrent,
            $oTarget,
            $targetType
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $aVotes = LS::Make(ModuleVote::class)->GetVoteById($targetId, $targetType);
        $aResult = [];
        foreach ($aVotes as $oVote) {
            $oUser = $this->user->GetUserById($oVote->getVoterId());
            $bShowUser = $oUser
                && (strtotime($oVote->getDate()) > $oe_end
                    || LS::Make(ModuleACL::class)->CheckSimpleAccessLevel(
                        $oe_enable_level,
                        $this->oUserCurrent,
                        $oTarget,
                        $targetType
                    ));
            $aResult[] = [
                'voterName'   => $bShowUser ? $oUser->getLogin() : null,
                'voterAvatar' => $bShowUser ? $oUser->getProfileAvatarPath() : null,
                'value'       => (float)$oVote->getDirection(),
                'date'        => (string)$oVote->getDate().'+03:00',
            ];
        }

        usort($aResult, $date_sort < 0 ? '_gov_s_date_desc' : '_gov_s_date_asc');
        $this->viewer->AssignAjax('aVotes', $aResult);
    }


    /**
     * Allow|forbid ignore user
     */
    protected function EventForbidIgnoreUser()
    {
        // check auth
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        // allow only for administrator
        if (!$this->oUserCurrent->isAdministrator()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        // search for user
        if (!$oUser = $this->user->GetUserById(getRequest('idUser'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_not_found'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $aForbidIgnore = $this->user->GetForbidIgnoredUsers();
        if (in_array($oUser->getId(), $aForbidIgnore)) {
            // remove user from forbid ignore list
            if ($this->user->AllowIgnoreUser($oUser->getId())) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('allow_ignore_user_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('sText', LS::Make(ModuleLang::class)->Get('forbid_ignore_user'));
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
            }
        } else {
            // add user to forbid ignore list
            if ($this->user->ForbidIgnoreUser($oUser->getId())) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('forbid_ignore_user_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('sText', LS::Make(ModuleLang::class)->Get('allow_ignore_user'));
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
            }
        }
    }

    /**
     * Ignore|disignore user
     */
    protected function EventIgnoreUser()
    {
        // check auth
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        // search for ignored user
        if (!$oUserIgnored = $this->user->GetUserById(getRequest('idUser'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_not_found'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        // is user try to ignore self
        if ($oUserIgnored->getId() == $this->oUserCurrent->getId()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('ignore_dissalow_own'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        $sType = getRequest('type');

        if ($sType == ModuleUser::TYPE_IGNORE_COMMENTS || $sType == ModuleUser::TYPE_IGNORE_TOPICS) {
            if ($this->user->IsUserIgnoredByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                // remove user from ignore list
                if ($this->user->UnIgnoreUserByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                    LS::Make(ModuleMessage::class)->AddNoticeSingle(
                        LS::Make(ModuleLang::class)->Get('disignore_user_ok_'.$sType),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );
                    $this->viewer->AssignAjax('sText', LS::Make(ModuleLang::class)->Get('ignore_user_'.$sType));
                } else {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('system_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );
                }
            } else {
                $aForbidIgnore = $this->user->GetForbidIgnoredUsers();
                //check ignored user in forbid ignored list
                if (in_array($oUserIgnored->getId(), $aForbidIgnore)) {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('ignore_dissalow_this'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                }

                //add user to ignore list
                if ($this->user->IgnoreUserByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                    LS::Make(ModuleMessage::class)->AddNoticeSingle(
                        LS::Make(ModuleLang::class)->Get('ignore_user_ok_'.$sType),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );
                    $this->viewer->AssignAjax('sText', LS::Make(ModuleLang::class)->Get('disignore_user_'.$sType));
                } else {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('system_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );
                }
            }
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    protected function EventGetHistory()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        $this->viewer->SetResponseAjax('json');

        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequest('reply'));

        if (!$oComment) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        if ($oComment->getTargetType() == 'talk') {
            if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById($oComment->getTargetId()))) {
                echo "NO TARGET";
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            /**
             * Пользователь есть в переписке?
             */
            if (!($oTalkUser =
                LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            /**
             * Пользователь активен в переписке?
             */
            if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        } else {
            if ($oComment->getTargetType() != 'topic' or !($oTopic = $oComment->getTarget())) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTopic->getUserId(
                        ) and !$this->oUserCurrent->isAdministrator()))
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            /**
             * Проверяет коммент на удаленность и отдает его только автору, и тем, у кого есть права на удаление.
             */
            if ((!$this->oUserCurrent
                || ($oComment->getDelete()
                    && !(LS::Make(ModuleACL::class)->UserCanDeleteComment(
                            $this->oUserCurrent,
                            $oComment,
                            1
                        )
                        || $this->oUserCurrent->getId() == $oComment->getUserId())))
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('not_access'),
                    LS::Make(ModuleLang::class)->Get('not_access')
                );
                Router::Action('error');

                return;
            }
            /**
             * Проверяет коммент на доступность из закрытых блогов.
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
        }

        $aData = LS::Make(ModuleEditComment::class)->GetDataItemsByCommentId($oComment->getId());

        foreach ($aData as $oData) {
            $oUser = $this->user->GetUserById($oData->getUserId());
            $oData->setText(LS::Make(ModuleText::class)->Parser($oData->getCommentTextSource()));
            $oData->setUserLogin($oUser->getLogin());
        }

        $oViewerLocal = $this->viewer->GetLocalViewer();
        $oViewerLocal->Assign('aHistory', $aData);
        $this->viewer->AssignAjax('sContent', $oViewerLocal->Fetch('history.tpl'));
    }

    protected function EventGetSource()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        $this->viewer->SetResponseAjax('json');

        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequest('idComment'));

        if (!$oComment) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $sCheckResult = LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($sCheckResult);

            return;
        }

        $oEditData = LS::Make(ModuleEditComment::class)->GetLastEditData($oComment->getId());

        if ($oEditData) {
            $sCommentSource = $oEditData->getCommentTextSource();
        } elseif (!Config::Get('view.tinymce')) {
            $sCommentSource = str_replace(["<br>", "<br/>"], [""], $oComment->getText());
        } else {
            $sCommentSource = $oComment->getText();
        }

        $this->viewer->AssignAjax('sCommentSource', $sCommentSource);
        $this->viewer->AssignAjax('bHasHistory', !is_null($oEditData));
    }

    protected function EventEdit()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        /**
         * Устанавливаем формат Ajax ответа
         */
        $this->viewer->SetResponseAjax('json');

        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequest('reply'));

        if (!$oComment) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $sCheckResult = LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($sCheckResult);

            return;
        }

        $bMark = getRequestStr('form_comment_mark') == "on";
        if ($bMark) {
            $sText =
                LS::Make(ModuleText::class)->Parser(LS::Make(ModuleText::class)->Mark(getRequestStr('comment_text')));
        } else {
            $sText = LS::Make(ModuleText::class)->Parser(getRequestStr('comment_text'));
        }

        $sText = preg_replace_callback(
            '/@(.*?)\((.*?)\)/',
            function ($matches) use ($oComment) {
                $sLogin = $matches[1];
                $sNick = $matches[2];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sNick."</a>";
                if ($oTargetUser = $this->user->getUserByLogin($sLogin)) {
                    LS::Make(ModuleCast::class)->sendCastNotifyToUser(
                        "comment",
                        $oComment,
                        LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId()),
                        $oTargetUser
                    );

                    return $r;
                }

                return $matches[0];
            },
            $sText
        );
        $sText = preg_replace_callback(
            '/@([a-zA-Zа-яА-Я0-9-_]+)/',
            function ($matches) use ($oComment) {
                $sLogin = $matches[1];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sLogin."</a>";
                if ($oTargetUser = $this->user->getUserByLogin($sLogin)) {
                    LS::Make(ModuleCast::class)->sendCastNotifyToUser(
                        "comment",
                        $oComment,
                        LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId()),
                        $oTargetUser
                    );

                    return $r;
                }

                return $matches[0];
            },
            $sText
        );

        if (mb_strlen($sText, 'utf-8') > Config::Get('module.comment.max_length')) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get(
                    'editcomment.err_max_comment_length',
                    ['maxlength' => Config::Get('max_comment_length')]
                )
            );

            return;
        }

        $sDE = date("Y-m-d H:i:s");

        $bEdited = false;
        if ($oComment->getText() == $sText) {
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                LS::Make(ModuleLang::class)->Get('editcomment.notice_nothing_changed')
            );
            $bEdited = false;
            $this->viewer->AssignAjax('bEdited', $bEdited);
        } else {
            if (Config::Get('module.editcomment.change_online')) {
                $oComment->setDate($sDE);
            }
            $oComment->setEditCount($oComment->getEditCount() + 1);
            $oComment->setEditDate($sDE);
            $oViewerLocal = $this->viewer->GetLocalViewer();
            $oViewerLocal->Assign('oComment', $oComment);
            $oViewerLocal->Assign('oUserCurrent', $this->oUserCurrent);

            if (Config::Get('module.editcomment.add_edit_date')) {
                $oComment->setText($sText.$oViewerLocal->Fetch('inject_comment_edited.tpl'));
            } else {
                $oComment->setText($sText);
            }
            $oComment->setText(
                LS::Make(ModuleText::class)->CommentParser($oComment, getRequestStr('form_comment_mark') == "on", true)
            );
            $oComment->setTextHash(md5($oComment->getText()));

            if (LS::Make(ModuleComment::class)->UpdateComment($oComment)) {
                if (Config::Get('module.editcomment.change_online')) {
                    $oCommentOnline = new EntityCommentOnline();
                    $oCommentOnline->setTargetId($oComment->getTargetId());
                    $oCommentOnline->setTargetType($oComment->getTargetType());
                    $oCommentOnline->setTargetParentId($oComment->getTargetParentId());
                    $oCommentOnline->setCommentId($oComment->getId());

                    LS::Make(ModuleComment::class)->AddCommentOnline($oCommentOnline);
                }

                $this->oUserCurrent->setDateCommentLast($sDE);
                $this->user->Update($this->oUserCurrent);

                $oData = new EntityEditCommentData();
                $oData->setCommentTextSource(getRequest('comment_text'));
                $oData->setCommentId($oComment->getId());
                $oData->setUserId($this->oUserCurrent->getId());
                $oData->setDateAdd($sDE);

                if (!$oData->save()) {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('error'));

                    return;
                }

                $bEdited = true;
                $this->viewer->AssignAjax('bEdited', $bEdited);
                $this->viewer->AssignAjax(
                    'bCanEditMore',
                    LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX) === true
                );
                $this->viewer->AssignAjax('sCommentText', $oComment->getText());
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('error'));
            }
        }
        if ($bEdited) {
            if ($oComment->getTargetType() == 'topic') {

                /**
                 * Отправка уведомления пользователям
                 */
                $notificationLink =
                    LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId())->getUrl()."#comment"
                    .$oComment->getId();
                $notificationTitle =
                    "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()."</a>"
                    ." отредактировал ваш <a href='".$notificationLink
                    ."'>комментарий</a> в посте <a href='/blog/undefined/".$oComment->getTargetId()."'>"
                    .$oComment->getTarget()->getTitle()."</a>";
                $notificationText = "";
                $notification = new EntityNotification(
                    [
                        'user_id'           => $oComment->getUserId(),
                        'text'              => $notificationText,
                        'title'             => $notificationTitle,
                        'link'              => $notificationLink,
                        'rating'            => 0,
                        'notification_type' => 6,
                        'target_type'       => 'comment',
                        'target_id'         => $oComment->getId(),
                        'sender_user_id'    => $this->oUserCurrent->getId(),
                        'group_target_type' => 'topic',
                        'group_target_id'   => $oComment->getTargetId()
                    ]
                );
                if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
                    LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
                }

            } elseif ($oComment->getTargetType() == 'talk') {
                if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById($oComment->getTargetId()))) {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('error'));

                    return;
                }
                /**
                 * Отправка уведомления пользователям
                 */
                $notificationLink = "/talk/".$oComment->getTargetId()."#comment".$oComment->getId();
                $notificationTitle =
                    "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()."</a>"
                    ." отредактировал ваш <a href='".$notificationLink."'>комментарий</a> в личке ".$oTalk->getTitle();
                $notificationText = "";
                $notification = new EntityNotification(
                    [
                        'user_id'           => $oComment->getUserId(),
                        'text'              => $notificationText,
                        'title'             => $notificationTitle,
                        'link'              => $notificationLink,
                        'rating'            => 0,
                        'notification_type' => 6,
                        'target_type'       => 'comment',
                        'target_id'         => $oComment->getId(),
                        'sender_user_id'    => $this->oUserCurrent->getId(),
                        'group_target_type' => 'talk',
                        'group_target_id'   => $oComment->getTargetId()
                    ]
                );
                if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
                    LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
                }
            }

            $sLogText = $this->oUserCurrent->getLogin()." редактировал коммент ".$oComment->getId()." ".$ip;
            LS::Make(ModuleLogger::class)->Notice($sLogText);
        }
        $this->viewer->AssignAjax(
            'bCanEditMore',
            LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX) === true
        );
    }

    protected function EventGetComment()
    {
        $idComment = getRequestStr('idComment', null, 'post');
        if (!($oComment = LS::Make(ModuleComment::class)->GetCommentById($idComment))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        if ($oComment->getTargetType() != 'topic' or !($oTopic = $oComment->getTarget())) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTopic->getUserId()
                    and !$this->oUserCurrent->isAdministrator()))
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяет коммент на удаленность и отдает его только автору, и тем, у кого есть права на удаление.
         */
        if ((!$this->oUserCurrent
            || ($oComment->getDelete()
                && !(LS::Make(ModuleACL::class)->UserCanDeleteComment(
                        $this->oUserCurrent,
                        $oComment,
                        1
                    )
                    || $this->oUserCurrent->getId() == $oComment->getUserId())))
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('not_access')
            );
            Router::Action('error');

            return;
        }
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
        $bIgnoreDelete = false;
        if ($this->oUserCurrent) {
            if ($this->oUserCurrent->isAdministrator() || $this->oUserCurrent->isGlobalModerator()) {
                $bIgnoreDelete = true;
            }
        }
        $aResult =
            LS::Make(ModuleComment::class)->ConvertCommentToArray($oComment, $oTopic->getDateRead(), $bIgnoreDelete);
        $this->viewer->AssignAjax("aComment", $aResult);
        $this->viewer->DisplayAjax();
    }
}
