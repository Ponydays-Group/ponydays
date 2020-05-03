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
/**
 * Экшен обработки ajax запросов
 * Ответ отдает в JSON фомате
 *
 * @package actions
 * @since 1.0
 */
class ActionAjax extends Action
{
    /**
     * Текущий пользователь
     *
     * @var ModuleUser_EntityUser|null
     */
    protected $oUserCurrent = null;
    /**
     * Инициализация
     */
    public
    function Init()
    {
        /**
         * Устанавливаем формат ответа
         */
        $this->Viewer_SetResponseAjax('json');
        /**
         * Получаем текущего пользователя
         */
        $this->oUserCurrent = $this->User_GetUserCurrent();
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
        if (!$this->oUserCurrent || !($this->oUserCurrent->isAdministrator() || $this->oUserCurrent->isGlobalModerator())) {
            return Router::Action('error');
        }

        $iUserId = (int)getRequest('iUserId');

        if ((int)$this->oUserCurrent->getId()==$iUserId) {
            return Router::Action('error');
        }

        if ((int)getRequest('iUnban')) {
            $this->User_Unban($iUserId);
			$sLogText = $this->oUserCurrent->getLogin()." разбанил пользователя ".$iUserId;
			$this->Logger_Notice($sLogText);

			$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> разбанил вас на сайте";
			$notification = Engine::GetEntity(
				'Notification',
				array(
					'user_id' => $iUserId,
					'text' => "",
					'title' => $notificationTitle,
					'link' => "",
					'rating' => 0,
					'notification_type' => 16,
					'target_type' => "global",
					'target_id' => -1,
					'sender_user_id' => $this->oUserCurrent->getId(),
					'group_target_type' => 'global',
					'group_target_id' => -1
				)
			);
			if($notificationCreated = $this->Notification_createNotification($notification)){
				$this->Nower_PostNotification($notificationCreated);
			}
            return;
        }

        $sBanComment = getRequest('sBanComment');
        $iBanHours = getRequest('iBanHours');

        if ((int)$iBanHours) {
            $t = time()+((int)$iBanHours*60*60);
            $this->User_Ban($iUserId, $this->oUserCurrent->getId(), date("Y-m-d H:i:s", $t), 0, $sBanComment);
        } else {
            $this->User_Ban($iUserId, $this->oUserCurrent->getId(), null, 1, $sBanComment);
        }

        $sLogText = $this->oUserCurrent->getLogin()." забанил пользователя ".$iUserId;
        $this->Logger_Notice($sLogText);

		$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> забанил вас на сайте";
		$notification = Engine::GetEntity(
			'Notification',
			array(
				'user_id' => $iUserId,
				'text' => "",
				'title' => $notificationTitle,
				'link' => "",
				'rating' => 0,
				'notification_type' => 16,
				'target_type' => "global",
				'target_id' => -1,
				'sender_user_id' => $this->oUserCurrent->getId(),
				'group_target_type' => 'global',
				'group_target_id' => -1
			)
		);
		if($notificationCreated = $this->Notification_createNotification($notification)){
			$this->Nower_PostNotification($notificationCreated);
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
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'));
            return;
        }

        if (!($oBlog = $this->Blog_GetBlogById(getRequest('iBlogId'))) or $oBlog->getType() == 'personal') {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'));
            return;
        }

        /**
         * Получаем локальный вьюер для рендеринга шаблона
         */
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('oBlog', $oBlog);
        if ($oBlog->getType() != 'close' or $oBlog->getUserIsJoin()) {
            /**
             * Получаем последний топик
             */
            $aResult = $this->Topic_GetTopicsByFilter(array(
                'blog_id' => $oBlog->getId() ,
                'topic_publish' => 1
            ) , 1, 1);
            $oViewer->Assign('oTopicLast', reset($aResult['collection']));
        }

        $oViewer->Assign('oUserCurrent', $this->oUserCurrent);
        /**
         * Устанавливаем переменные для ajax ответа
         */
        $this->Viewer_AssignAjax('sText', $oViewer->Fetch("infobox.info.blog.tpl"));
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
        if (!($oCountry = $this->Geo_GetGeoObject('country', $iCountryId))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'));
            return;
        }

        /**
         * Получаем список регионов
         */
        $aResult = $this->Geo_GetRegions(array(
            'country_id' => $oCountry->getId()
        ) , array(
            'sort' => 'asc'
        ) , 1, $iLimit);
        $aRegions = array();
        foreach($aResult['collection'] as $oObject) {
            $aRegions[] = array(
                'id' => $oObject->getId() ,
                'name' => $oObject->getName() ,
            );
        }

        /**
         * Устанавливаем переменные для ajax ответа
         */
        $this->Viewer_AssignAjax('aRegions', $aRegions);
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
        if (!($oRegion = $this->Geo_GetGeoObject('region', $iRegionId))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'));
            return;
        }

        /**
         * Получаем города
         */
        $aResult = $this->Geo_GetCities(array(
            'region_id' => $oRegion->getId()
        ) , array(
            'sort' => 'asc'
        ) , 1, $iLimit);
        $aCities = array();
        foreach($aResult['collection'] as $oObject) {
            $aCities[] = array(
                'id' => $oObject->getId() ,
                'name' => $oObject->getName() ,
            );
        }

        /**
         * Устанавливаем переменные для ajax ответа
         */
        $this->Viewer_AssignAjax('aCities', $aCities);
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }

        /**
         * Комментарий существует?
         */
        if (!($oComment = $this->Comment_GetCommentById(getRequestStr('idComment', null, 'post')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_vote_error_noexists'), $this->Lang_Get('error'));
            return;
        }

        /**
         * Голосует автор комментария?
         */
        if ($oComment->getUserId() == $this->oUserCurrent->getId()) {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_vote_error_self'), $this->Lang_Get('attention'));
            return;
        }

        /**
         * Время голосования истекло?
         */
        if (Config::Get('acl.vote.comment.limit_time') != 0 && strtotime($oComment->getDate()) <= time() - Config::Get('acl.vote.comment.limit_time')) {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_vote_error_time'), $this->Lang_Get('attention'));
            return;
        }

        /**
         * Пользователь имеет право голоса?
         */
        if (!$this->ACL_CanVoteComment($this->oUserCurrent, $oComment)) {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_vote_error'), $this->Lang_Get('attention'));
            return;
        }

        /**
         * Как именно голосует пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array($iValue, array(
            '1',
            '-1'
        ))) {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_vote_error_value'), $this->Lang_Get('attention'));
            return;
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        $iVoteType = 0; //0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
        $iCountVote = 1;
        if ($oTopicCommentVote = $this->Vote_GetVote($oComment->getId(), 'comment', $this->oUserCurrent->getId())) {
            if ($iValue == $oTopicCommentVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
                $iVoteType = 2;
                $iCountVote = -1;
            } else if ($oTopicCommentVote->getDirection() != 0){
                $iValue += $iValue;
                $iVoteType = 1;
                $iCountVote = 0;
            }
            $this->ModuleVote_DeleteVote($oComment->getId(), 'comment', $this->oUserCurrent->getId());
        }

        $oTopicCommentVote = Engine::GetEntity('Vote');
        $oTopicCommentVote->setTargetId($oComment->getId());
        $oTopicCommentVote->setTargetType('comment');
        $oTopicCommentVote->setVoterId($this->oUserCurrent->getId());
        $oTopicCommentVote->setDirection($iValueOld);
        $oTopicCommentVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float)$this->Rating_VoteComment($this->oUserCurrent, $oComment, $iValue, $iValueOld, $iCountVote, $iVoteType);
        $oTopicCommentVote->setValue($iVal);

        if ($this->Vote_AddVote($oTopicCommentVote) and $this->Comment_UpdateComment($oComment)) {
            if ($iValueOld == 0) {
                $this->ModuleVote_DeleteVote($oComment->getId(), 'comment', $this->oUserCurrent->getId());
                $this->Message_AddNoticeSingle($this->Lang_Get('comment_vote_deleted'), $this->Lang_Get('attention'));
            } else {
                $this->Message_AddNoticeSingle($this->Lang_Get('comment_vote_ok'), $this->Lang_Get('attention'));
            }
            $this->Viewer_AssignAjax('iRating', $oComment->getRating());
            $this->Viewer_AssignAjax('iCountVote', $oComment->getCountVote());
            /**
             * Добавляем событие в ленту
             */
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_vote_error'), $this->Lang_Get('error'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = $this->Topic_GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Голосует автор топика?
         */
        if ($oTopic->getUserId() == $this->oUserCurrent->getId()) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_vote_error_self') , $this->Lang_Get('attention'));
            return;
        }

        /**
         * Время голосования истекло?
         */
        if (strtotime($oTopic->getDateAdd()) <= time() - Config::Get('acl.vote.topic.limit_time')) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_vote_error_time') , $this->Lang_Get('attention'));
            return;
        }

        /**
         * Как проголосовал пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array($iValue, array(
            '1',
            '-1',
            '0'
        ))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('attention'));
            return;
        }

        /**
         * Права на голосование
         */
        if (!$this->ACL_CanVoteTopic($this->oUserCurrent, $oTopic) and $iValue) {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_vote_error') , $this->Lang_Get('attention'));
            return;
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        $iCountVote = 1;
        $iVoteType = 0; //0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
        if ($oTopicVote = $this->Vote_GetVote($oTopic->getId() , 'topic', $this->oUserCurrent->getId())) {
            if ($iValue == $oTopicVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
                $iVoteType = 2;
                $iCountVote = -1;
            } else if ($oTopicVote->getDirection() != 0){
                $iValue += $iValue;
                $iVoteType = 1;
                $iCountVote = 0;
            }
            $this->ModuleVote_DeleteVote($oTopic->getId(), 'topic', $this->oUserCurrent->getId());
        }

        $oTopicVote = Engine::GetEntity('Vote');
        $oTopicVote->setTargetId($oTopic->getId());
        $oTopicVote->setTargetType('topic');
        $oTopicVote->setVoterId($this->oUserCurrent->getId());
        $oTopicVote->setDirection($iValueOld);
        $oTopicVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float)$this->Rating_VoteTopic($this->oUserCurrent, $oTopic, $iValue, $iValueOld, $iCountVote, $iVoteType);
        $oTopicVote->setValue($iVal);
        if ($iValue == 1) {
            $oTopic->setCountVoteUp($oTopic->getCountVoteUp() + 1);
        }
        elseif ($iValue == - 1) {
            $oTopic->setCountVoteDown($oTopic->getCountVoteDown() + 1);
        }
        elseif ($iValue == 0) {
            $oTopic->setCountVoteAbstain($oTopic->getCountVoteAbstain() + 1);
        }

        if ($this->Vote_AddVote($oTopicVote) and $this->Topic_UpdateTopic($oTopic)) {
            if ($iValue) {
                if ($iValueOld == 0) {
                    $this->ModuleVote_DeleteVote($oTopic->getId(), 'topic', $this->oUserCurrent->getId());
                    $this->Message_AddNoticeSingle($this->Lang_Get('topic_vote_deleted'), $this->Lang_Get('attention'));
                } else {
                    $this->Message_AddNoticeSingle($this->Lang_Get('topic_vote_ok') , $this->Lang_Get('attention'));
                }
            }
            else {
                $this->Message_AddNoticeSingle($this->Lang_Get('topic_vote_ok_abstain') , $this->Lang_Get('attention'));
            }

            $this->Viewer_AssignAjax('iRating', $oTopic->getRating());
            $this->Viewer_AssignAjax('iCountVote', $oTopic->getCountVote());
            /**
             * Добавляем событие в ленту
             */
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Блог существует?
         */
        if (!($oBlog = $this->Blog_GetBlogById(getRequestStr('idBlog', null, 'post')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Голосует за свой блог?
         */
        if ($oBlog->getOwnerId() == $this->oUserCurrent->getId()) {
            $this->Message_AddErrorSingle($this->Lang_Get('blog_vote_error_self') , $this->Lang_Get('attention'));
            return;
        }

        /**
         * Имеет право на голосование?
         */
        switch ($this->ACL_CanVoteBlog($this->oUserCurrent, $oBlog)) {
            case ModuleACL::CAN_VOTE_BLOG_ERROR_CLOSE:
                $this->Message_AddErrorSingle($this->Lang_Get('blog_vote_error_close') , $this->Lang_Get('attention'));
                return;
                break;

            default:
            case ModuleACL::CAN_VOTE_BLOG_FALSE:
                $this->Message_AddErrorSingle($this->Lang_Get('blog_vote_error_acl') , $this->Lang_Get('attention'));
                return;
                break;
        }

        /**
         * Как именно голосует пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (in_array($iValue, array(
            '1',
            '-1'
        ))) {
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('attention'));
            return;
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        if ($oBlogVote = $this->Vote_GetVote($oBlog->getId() , 'blog', $this->oUserCurrent->getId())) {
            if ($iValue == $oBlogVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
            } else if ($oBlogVote->getDirection() != 0){
                $iValue += $iValue;
            }
            $this->ModuleVote_DeleteVote($oBlog->getId(), 'comment', $this->oUserCurrent->getId());
        }
        $oBlogVote = Engine::GetEntity('Vote');
        $oBlogVote->setTargetId($oBlog->getId());
        $oBlogVote->setTargetType('blog');
        $oBlogVote->setVoterId($this->oUserCurrent->getId());
        $oBlogVote->setDirection($iValueOld);
        $oBlogVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float)$this->Rating_VoteBlog($this->oUserCurrent, $oBlog, $iValue);
        $oBlogVote->setValue($iVal);
        $oBlog->setCountVote($oBlog->getCountVote() + 1);
        if ($this->Vote_AddVote($oBlogVote) and $this->Blog_UpdateBlog($oBlog)) {
            $this->Viewer_AssignAjax('iCountVote', $oBlog->getCountVote());
            $this->Viewer_AssignAjax('iRating', $oBlog->getRating());
            $this->Message_AddNoticeSingle($this->Lang_Get('blog_vote_ok') , $this->Lang_Get('attention'));
            /**
             * Добавляем событие в ленту
             */
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('attention'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Пользователь существует?
         */
        if (!($oUser = $this->User_GetUserById(getRequestStr('idUser', null, 'post')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Голосует за себя?
         */
        if ($oUser->getId() == $this->oUserCurrent->getId()) {
            $this->Message_AddErrorSingle($this->Lang_Get('user_vote_error_self') , $this->Lang_Get('attention'));
            return;
        }

        /**
         * Имеет право на голосование?
         */
        if (!$this->ACL_CanVoteUser($this->oUserCurrent, $oUser)) {
            $this->Message_AddErrorSingle($this->Lang_Get('user_vote_error_acl') , $this->Lang_Get('attention'));
            return;
        }

        /**
         * Как проголосовал
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array($iValue, array(
            '1',
            '-1'
        ))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('attention'));
            return;
        }

        $iValueOld = $iValue;
        if ($oUserVote = $this->Vote_GetVote($oUser->getId() , 'user', $this->oUserCurrent->getId())) {
            if ($iValue == $oUserVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
            } else if ($oUserVote->getDirection() != 0){
                $iValue += $iValue;
            }
            $this->ModuleVote_DeleteVote($oUser->getId() , 'user', $this->oUserCurrent->getId());
        }

        /**
         * Голосуем
         */
        $oUserVote = Engine::GetEntity('Vote');
        $oUserVote->setTargetId($oUser->getId());
        $oUserVote->setTargetType('user');
        $oUserVote->setVoterId($this->oUserCurrent->getId());
        $oUserVote->setDirection($iValueOld);
        $oUserVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float)$this->Rating_VoteUser($this->oUserCurrent, $oUser, $iValue);
        $oUserVote->setValue($iVal);

        if ($iValueOld != 0) {
            $oUser->setCountVote($oUser->getCountVote() + 1);
        } else {
            $oUser->setCountVote($oUser->getCountVote() - 1);
        }
        if ($this->Vote_AddVote($oUserVote) and $this->User_Update($oUser)) {
            if ($iValueOld == 0) {
                $this->ModuleVote_DeleteVote($oUser->getId() , 'user', $this->oUserCurrent->getId());
                $this->Message_AddNoticeSingle($this->Lang_Get('user_vote_deleted'), $this->Lang_Get('attention'));
            } else {
                $this->Message_AddNoticeSingle($this->Lang_Get('user_vote_ok'), $this->Lang_Get('attention'));
            }
            $this->Viewer_AssignAjax('iRating', $oUser->getRating());
            $this->Viewer_AssignAjax('iSkill', $oUser->getSkill());
            $this->Viewer_AssignAjax('iCountVote', $oUser->getCountVote());
            /**
             * Добавляем событие в ленту
             */
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
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
        if (!($oTopic = $this->Topic_GetTopicById($idTopic))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Тип топика - опрос?
         */
        if ($oTopic->getType() != 'question') {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Уже голосовал?
         */
        if ($oTopicQuestionVote = $this->Topic_GetTopicQuestionVote($oTopic->getId() , $this->oUserCurrent->getId())) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_question_vote_already') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Вариант ответа
         */
        $aAnswer = $oTopic->getQuestionAnswers();
        if (!isset($aAnswer[$idAnswer]) and $idAnswer != - 1) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        if ($idAnswer == - 1) {
            $oTopic->setQuestionCountVoteAbstain($oTopic->getQuestionCountVoteAbstain() + 1);
        }
        else {
            $oTopic->increaseQuestionAnswerVote($idAnswer);
        }

        $oTopic->setQuestionCountVote($oTopic->getQuestionCountVote() + 1);
        /**
         * Голосуем(отвечаем на опрос)
         */
        $oTopicQuestionVote = Engine::GetEntity('Topic_TopicQuestionVote');
        $oTopicQuestionVote->setTopicId($oTopic->getId());
        $oTopicQuestionVote->setVoterId($this->oUserCurrent->getId());
        $oTopicQuestionVote->setAnswer($idAnswer);
        if ($this->Topic_AddTopicQuestionVote($oTopicQuestionVote) and $this->Topic_updateTopic($oTopic)) {
            $this->Message_AddNoticeSingle($this->Lang_Get('topic_question_vote_ok') , $this->Lang_Get('attention'));
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('oTopic', $oTopic);
            $this->Viewer_AssignAjax('sText', $oViewer->Fetch("question_result.tpl"));
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Объект уже должен быть в избранном
         */
        if ($oFavourite = $this->Favourite_GetFavourite(getRequestStr('target_id') , getRequestStr('target_type') , $this->oUserCurrent->getId())) {
            /**
             * Обрабатываем теги
             */
            $aTags = explode(',', trim(getRequestStr('tags') , "\r\n\t\0\x0B ."));
            $aTagsNew = array();
            $aTagsNewLow = array();
            $aTagsReturn = array();
            foreach($aTags as $sTag) {
                $sTag = trim($sTag);
                if (func_check($sTag, 'text', 2, 50) and !in_array(mb_strtolower($sTag, 'UTF-8') , $aTagsNewLow)) {
                    $sTagEsc = htmlspecialchars($sTag);
                    $aTagsNew[] = $sTagEsc;
                    $aTagsReturn[] = array(
                        'tag' => $sTagEsc,
                        'url' => $this->oUserCurrent->getUserWebPath() . 'favourites/' . $oFavourite->getTargetType() . 's/tag/' . $sTagEsc . '/', // костыль для URL с множественным числом
                    );
                    $aTagsNewLow[] = mb_strtolower($sTag, 'UTF-8');
                }
            }

            if (!count($aTagsNew)) {
                $oFavourite->setTags('');
            }
            else {
                $oFavourite->setTags(join(',', $aTagsNew));
            }

            $this->Viewer_AssignAjax('aTags', $aTagsReturn);
            $this->Favourite_UpdateFavourite($oFavourite);
            return;
        }

        $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array($iType, array(
            '1',
            '0'
        ))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = $this->Topic_GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Пропускаем топик из черновиков
         */
        if (!$oTopic->getPublish()) {
            $this->Message_AddErrorSingle($this->Lang_Get('error_favorite_topic_is_draft') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Топик уже в избранном?
         */
        $oFavouriteTopic = $this->Topic_GetFavouriteTopic($oTopic->getId() , $this->oUserCurrent->getId());
        if (!$oFavouriteTopic and $iType) {
            $oFavouriteTopicNew = Engine::GetEntity('Favourite', array(
                'target_id' => $oTopic->getId() ,
                'user_id' => $this->oUserCurrent->getId() ,
                'target_type' => 'topic',
                'target_publish' => $oTopic->getPublish()
            ));
            $oTopic->setCountFavourite($oTopic->getCountFavourite() + 1);
            if ($this->Topic_AddFavouriteTopic($oFavouriteTopicNew) and $this->Topic_UpdateTopic($oTopic)) {
                $this->Message_AddNoticeSingle($this->Lang_Get('topic_favourite_add_ok') , $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('bState', true);
                $this->Viewer_AssignAjax('iCount', $oTopic->getCountFavourite());
            }
            else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
                return;
            }
        }

        if (!$oFavouriteTopic and !$iType) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_favourite_add_no') , $this->Lang_Get('error'));
            return;
        }

        if ($oFavouriteTopic and $iType) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_favourite_add_already') , $this->Lang_Get('error'));
            return;
        }

        if ($oFavouriteTopic and !$iType) {
            $oTopic->setCountFavourite($oTopic->getCountFavourite() - 1);
            if ($this->Topic_DeleteFavouriteTopic($oFavouriteTopic) and $this->Topic_UpdateTopic($oTopic)) {
                $this->Message_AddNoticeSingle($this->Lang_Get('topic_favourite_del_ok') , $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('bState', false);
                $this->Viewer_AssignAjax('iCount', $oTopic->getCountFavourite());
            }
            else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array($iType, array(
            '1',
            '0'
        ))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Комментарий существует?
         */
        if (!($oComment = $this->Comment_GetCommentById(getRequestStr('idComment', null, 'post')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Запрет на добавление удаленного комментария
         */
        if ($iType === '1' and $oComment->getDelete()) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }

        /**
         * Комментарий уже в избранном?
         */
        $oFavouriteComment = $this->Comment_GetFavouriteComment($oComment->getId() , $this->oUserCurrent->getId());
        if (!$oFavouriteComment and $iType) {
            $oFavouriteCommentNew = Engine::GetEntity('Favourite', array(
                'target_id' => $oComment->getId() ,
                'target_type' => 'comment',
                'user_id' => $this->oUserCurrent->getId() ,
                'target_publish' => $oComment->getPublish()
            ));
            $oComment->setCountFavourite($oComment->getCountFavourite() + 1);
            if ($this->Comment_AddFavouriteComment($oFavouriteCommentNew) and $this->Comment_UpdateComment($oComment)) {
                $this->Message_AddNoticeSingle($this->Lang_Get('comment_favourite_add_ok') , $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('bState', true);
                $this->Viewer_AssignAjax('iCount', $oComment->getCountFavourite());
            }
            else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
                return;
            }
        }

        if (!$oFavouriteComment and !$iType) {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_favourite_add_no') , $this->Lang_Get('error'));
            return;
        }

        if ($oFavouriteComment and $iType) {
            $this->Message_AddErrorSingle($this->Lang_Get('comment_favourite_add_already') , $this->Lang_Get('error'));
            return;
        }

        if ($oFavouriteComment and !$iType) {
            $oComment->setCountFavourite($oComment->getCountFavourite() - 1);
            if ($this->Comment_DeleteFavouriteComment($oFavouriteComment) and $this->Comment_UpdateComment($oComment)) {
                $this->Message_AddNoticeSingle($this->Lang_Get('comment_favourite_del_ok') , $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('bState', false);
                $this->Viewer_AssignAjax('iCount', $oComment->getCountFavourite());
            }
            else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array($iType, array(
            '1',
            '0'
        ))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         *	Сообщение существует?
         */
        if (!($oTalk = $this->Talk_GetTalkById(getRequestStr('idTalk', null, 'post')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Сообщение уже в избранном?
         */
        $oFavouriteTalk = $this->Talk_GetFavouriteTalk($oTalk->getId() , $this->oUserCurrent->getId());
        if (!$oFavouriteTalk and $iType) {
            $oFavouriteTalkNew = Engine::GetEntity('Favourite', array(
                'target_id' => $oTalk->getId() ,
                'target_type' => 'talk',
                'user_id' => $this->oUserCurrent->getId() ,
                'target_publish' => '1'
            ));
            if ($this->Talk_AddFavouriteTalk($oFavouriteTalkNew)) {
                $this->Message_AddNoticeSingle($this->Lang_Get('talk_favourite_add_ok') , $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('bState', true);
            }
            else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
                return;
            }
        }

        if (!$oFavouriteTalk and !$iType) {
            $this->Message_AddErrorSingle($this->Lang_Get('talk_favourite_add_no') , $this->Lang_Get('error'));
            return;
        }

        if ($oFavouriteTalk and $iType) {
            $this->Message_AddErrorSingle($this->Lang_Get('talk_favourite_add_already') , $this->Lang_Get('error'));
            return;
        }

        if ($oFavouriteTalk and !$iType) {
            if ($this->Talk_DeleteFavouriteTalk($oFavouriteTalk)) {
                $this->Message_AddNoticeSingle($this->Lang_Get('talk_favourite_del_ok') , $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('bState', false);
            }
            else {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
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
        if ($aComments = $this->Comment_GetCommentsOnline('topic', Config::Get('block.stream.row'))) {
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('aComments', $aComments);
            $sTextResult = $oViewer->Fetch("blocks/block.stream_comment.tpl");
            $this->Viewer_AssignAjax('sText', $sTextResult);
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('block_stream_comments_no') , $this->Lang_Get('attention'));
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
        if ($oTopics = $this->Topic_GetTopicsLast(Config::Get('block.stream.row'))) {
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('oTopics', $oTopics);
            $sTextResult = $oViewer->Fetch("blocks/block.stream_topic.tpl");
            $this->Viewer_AssignAjax('sText', $sTextResult);
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('block_stream_topics_no') , $this->Lang_Get('attention'));
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
        if ($aResult = $this->Blog_GetBlogsRating(1, Config::Get('block.blogs.row'))) {
            $aBlogs = $aResult['collection'];
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('aBlogs', $aBlogs);
            $sTextResult = $oViewer->Fetch("blocks/block.blogs_top.tpl");
            $this->Viewer_AssignAjax('sText', $sTextResult);
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Получаем список блогов и формируем ответ
         */
        if ($aBlogs = $this->Blog_GetBlogsRatingSelf($this->oUserCurrent->getId() , Config::Get('block.blogs.row'))) {
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('aBlogs', $aBlogs);
            $sTextResult = $oViewer->Fetch("blocks/block.blogs_top.tpl");
            $this->Viewer_AssignAjax('sText', $sTextResult);
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('block_blogs_self_error') , $this->Lang_Get('attention'));
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Получаем список блогов и формируем ответ
         */
        if ($aBlogs = $this->Blog_GetBlogsRatingJoin($this->oUserCurrent->getId() , Config::Get('block.blogs.row'))) {
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('aBlogs', $aBlogs);
            $sTextResult = $oViewer->Fetch("blocks/block.blogs_top.tpl");
            $this->Viewer_AssignAjax('sText', $sTextResult);
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('block_blogs_join_error') , $this->Lang_Get('attention'));
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
        $this->Viewer_SetResponseAjax('jsonIframe', false);
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Допустимый тип топика?
         */
        if (!$this->Topic_IsAllowTopicType($sType = getRequestStr('topic_type'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_create_type_error') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Создаем объект топика для валидации данных
         */
        $oTopic = Engine::GetEntity('ModuleTopic_EntityTopic');
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
        $oTopic->_Validate(array(
            'topic_title',
            'topic_text',
            'topic_tags',
            'topic_type'
        ) , false);
        if ($oTopic->_hasValidateErrors()) {
            $this->Message_AddErrorSingle($oTopic->_getValidateError());
            return false;
        }

        /**
         * Формируем текст топика
         */
        list($sTextShort, $sTextNew, $sTextCut) = $this->Text_Cut($oTopic->getTextSource());
        $oTopic->setCutText($sTextCut);
        $oTopic->setText($this->Text_Parser($sTextNew));
        $oTopic->setTextShort($this->Text_Parser($sTextShort));
        /**
         * Рендерим шаблон для предпросмотра топика
         */
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('oTopic', $oTopic);
        $sTemplate = "topic_preview_{$oTopic->getType() }.tpl";
        if (!$this->Viewer_TemplateExists($sTemplate)) {
            $sTemplate = 'topic_preview_topic.tpl';
        }

        $sTextResult = $oViewer->Fetch($sTemplate);
        /**
         * Передаем результат в ajax ответ
         */
        $this->Viewer_AssignAjax('sText', $sTextResult);
        return true;
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
        }
        else {
            if (getRequestStr('form_comment_mark')=="on")
                $sTextResult = $this->Text_Parser($this->Text_Mark($sText));
            else
                $sTextResult = $this->Text_Parser($sText);
        }

        $sTextResult = preg_replace_callback('/@(.*?)\((.*?)\)/',
            function ($matches) {
                $sLogin = $matches[1];
                $sNick = $matches[2];
                $r = "<a href=\"/profile/" . $sLogin . "/\" class=\"ls-user\">&#64;" . $sNick . "</a>";
                if ($oTargetUser = $this->User_getUserByLogin($sLogin)) {
                    return $r;
                }
                return $matches[0];
            }, $sTextResult);
        $sTextResult = preg_replace_callback('/@([a-zA-Zа-яА-Я0-9-_]+)/',
            function ($matches) {
                $sLogin = $matches[1];
                $r = "<a href=\"/profile/" . $sLogin . "/\" class=\"ls-user\">&#64;" . $sLogin . "</a>";
                if ($oTargetUser = $this->User_getUserByLogin($sLogin)) {
                    return $r;
                }
                return $matches[0];
            }, $sTextResult);

        /**
         * Передаем результат в ajax ответ
         */
        $this->Viewer_AssignAjax('sText', $sTextResult);
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
        $this->Viewer_SetResponseAjax('json', false);
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        $sFile = null;
        if (isPost('img_url') && $_REQUEST['img_url'] != '' && $_REQUEST['img_url'] != 'http://') {
            /**
             * Загрузка файла по URl
             */
            $sFile = $this->Topic_UploadTopicImageUrl($_REQUEST['img_url'], $this->oUserCurrent);
            switch (true) {
            case is_string($sFile):
                break;

            case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_READ):
                $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_url_error_read') , $this->Lang_Get('error'));
                return;
            case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_SIZE):
                $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_url_error_size') , $this->Lang_Get('error'));
                return;
            case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_TYPE):
                $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_url_error_type') , $this->Lang_Get('error'));
                return;
            default:
            case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR):
                $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_url_error') , $this->Lang_Get('error'));
                return;
            }

            if ($sFile) {
                $sText = $this->Image_BuildHTML($sFile, $_REQUEST);
            }
        } elseif (isPost('img_base64')) {
            /**
             * Загрузка файла из Base64
             */
            $sFile = $this->Topic_UploadTopicImagebase64($_REQUEST['img_base64'], $this->oUserCurrent);
            switch (true) {
            case is_string($sFile):
                break;

            case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_READ):
                $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_url_error_read') , $this->Lang_Get('error'));
                return;
            case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_SIZE):
                $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_url_error_size') , $this->Lang_Get('error'));
                return;
            case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR_TYPE):
                $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_url_error_type') , $this->Lang_Get('error'));
                return;
            default:
            case ($sFile == ModuleImage::UPLOAD_IMAGE_ERROR):
                $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_url_error') , $this->Lang_Get('error'));
                return;
            }

            if ($sFile) {
                $sText = $this->Image_BuildHTML($sFile, $_REQUEST);
            }
        } else {
            function reArrayFiles(&$file_post) {
                $file_ary = array();
                $file_count = count($file_post['name']);
                $file_keys = array_keys($file_post);
    
                for ($i=0; $i<$file_count; $i++) {
                    foreach ($file_keys as $key) {
                        $file_ary[$i][$key] = $file_post[$key][$i];
                    }
                }

                return $file_ary;
            }

            $sText = "";
            $aFiles = reArrayFiles($_FILES['img_file']);

            foreach($aFiles as $k => $v) {
                /**
                 * Был выбран файл с компьютера и он успешно зугрузился?
                 */
                
                if (is_uploaded_file($v['tmp_name'])) {
                    if (!$sFile = $this->Topic_UploadTopicImageFile($v, $this->oUserCurrent)) {
                        $this->Message_AddErrorSingle($this->Lang_Get('uploadimg_file_error') , $this->Lang_Get('error'));
                        return;
                    }

                    /**
                     * Если файл успешно загружен, формируем HTML вставки и возвращаем в ajax ответе
                     */
                    if ($sFile) {
                    	if ($_REQUEST['just_url']) {
                    		$sText.= $sFile;
						} else {
                        	$sText.= $this->Image_BuildHTML($sFile, $_REQUEST);
                    	}
                    }
                }
            } //foreach
        }

        $this->Viewer_AssignAjax('sText', $sText);
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

        $aItems = array();
        /**
         * Формируем список тегов
         */
        $aTags = $this->Topic_GetTopicTagsByLike($sValue, 10);
        foreach($aTags as $oTag) {
            $aItems[] = $oTag->getText();
        }

        /**
         * Передаем результат в ajax ответ
         */
        $this->Viewer_AssignAjax('aItems', $aItems);
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

        $aItems = array();
        /**
         * Формируем список пользователей
         */
        $aUsers = $this->User_GetUsersByLoginLike($sValue, 10);
        foreach($aUsers as $oUser) {
            $aItems[] = $oUser->getLogin();
        }

        /**
         * Передаем результат в ajax ответ
         */
        $this->Viewer_AssignAjax('aItems', $aItems);
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
        if (!($oComment = $this->Comment_GetCommentById($idComment))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        if (!$this->ACL_UserCanDeleteComment($this->oUserCurrent, $oComment, 1)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access') , $this->Lang_Get('error'));
            return;
        }

        $lastdeleteUser = $oComment->getDeleteUserId();
        /**
         * Устанавливаем пометку о том, что комментарий удален
         */
        $oComment->setDelete(($oComment->getDelete() + 1) % 2);
        $oComment->setDeleteReason($sDeleteReason);
        $oComment->setDeleteUserId($this->oUserCurrent->getId());
        $this->Hook_Run('comment_delete_before', array(
            'oComment' => $oComment
        ));
        if (!$this->Comment_UpdateCommentStatus($oComment)) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        $this->Hook_Run('comment_delete_after', array(
            'oComment' => $oComment
        ));
        /**
         * Формируем текст ответа
         */
        if ($bState = (bool)$oComment->getDelete()) {
            $sMsg = $this->Lang_Get('comment_delete_ok');
            $sTextToggle = $this->Lang_Get('comment_repair');
			$sLogText = $this->oUserCurrent->getLogin()." удалил комментарий ".$oComment->getId();
			$this->Logger_Notice($sLogText);
        }
        else {
            $sMsg = $this->Lang_Get('comment_repair_ok');
            $sTextToggle = $this->Lang_Get('comment_delete');
			$sLogText = $this->oUserCurrent->getLogin()." восстановил комментарий ".$oComment->getId();
			$this->Logger_Notice($sLogText);
        }

		/**
		 * Отправка уведомления пользователям
		 */
		$notificationLink = $this->Topic_GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId();
		if ((bool)$oComment->getDelete()) {
			$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> удалил ваш <a href='".$notificationLink."'>комментарий</a>\nПричина: ".$oComment->getDeleteReason();
			$notificationType = 7;
		} else {
			$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> восстановил ваш <a href='".$notificationLink."'>комментарий</a>";
			$notificationType = 8;
		}
		$notificationText = "";
		$notification = Engine::GetEntity(
			'Notification',
			array(
				'user_id' => $oComment->getUserId(),
				'text' => $notificationText,
				'title' => $notificationTitle,
				'link' => $notificationLink,
				'rating' => 0,
				'notification_type' => $notificationType,
				'target_type' => 'comment',
				'target_id' => $oComment->getId(),
				'sender_user_id' => $this->oUserCurrent->getId(),
				'group_target_type' => 'topic',
				'group_target_id' => $oComment->getTargetId()
			)
		);
		if($notificationCreated = $this->Notification_createNotification($notification)){
			$this->Nower_PostNotificationWithComment($notificationCreated, $oComment);
		}

		if ($lastdeleteUser && $this->oUserCurrent->getId() != $lastdeleteUser) {
			$notificationLink = $this->Topic_GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId();
			$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> восстановил удаленный вами <a href='".$notificationLink."'>комментарий</a>";
			$notificationText = "";
			$notification = Engine::GetEntity(
				'Notification',
				array(
					'user_id' => $lastdeleteUser,
					'text' => $notificationText,
					'title' => $notificationTitle,
					'link' => $notificationLink,
					'rating' => 0,
					'notification_type' => 9,
					'target_type' => 'comment',
					'target_id' => $oComment->getId(),
					'sender_user_id' => $this->oUserCurrent->getId(),
					'group_target_type' => 'topic',
					'group_target_id' => $oComment->getTargetId()
				)
			);
			if($notificationCreated = $this->Notification_createNotification($notification)){
				$this->Nower_PostNotificationWithComment($notificationCreated, $oComment);
			}
		}

        /**
         * Обновление события в ленте активности
         */
        $this->Stream_write($oComment->getUserId() , 'add_comment', $oComment->getId() , !$oComment->getDelete());
        /**
         * Показываем сообщение и передаем переменные в ajax ответ
         */
        $this->Message_AddNoticeSingle($sMsg, $this->Lang_Get('attention'));
        $this->Viewer_AssignAjax('bState', $bState);
        $this->Viewer_AssignAjax('sTextToggle', $sTextToggle);

    }

    protected
    function EventInviteUser()
    {
        $a = $_POST["to"];
        $oUserCurrent = $this->ModuleUser_GetUserCurrent();
        $oBlog = $this->ModuleBlog_GetBlogById($_POST["blog"]);
        $this->ModuleTalk_SendTalk("Просьба об инвайте", "Пользователь <a href='" . "/profile/" . $oUserCurrent->getLogin() . "/' class='user'>" . "<i class='icon-user'></i>" . $oUserCurrent->getLogin() . "</a> просит пригласить его в блог <a href='" . $oBlog->getUrlFull() . "'>" . $oBlog->getTitle() . "</a>.", $oUserCurrent->getId() , $a);
    }

    protected
    function EventTopicLockControl()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = $this->Topic_GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        $isAllowLockControlTopic = $this->ACL_IsAllowLockTopicControl($oTopic, $this->oUserCurrent);
        if (!$isAllowLockControlTopic) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access') , $this->Lang_Get('error'));
            return;
        }

        $bLockState = getRequestStr('bState', null, 'post') == '1';
        $bStateOld = $oTopic->isControlLocked();
        $oTopic->setLockControl($bLockState);
        if ($bStateOld == $bLockState || $this->Topic_UpdateControlLock($oTopic)) {
            $sNotice = $bLockState ? 'topic_control_locked' : 'topic_control_unlocked';
            $this->Message_AddNoticeSingle($this->Lang_Get($sNotice) , $this->Lang_Get('attention'));
            $this->Viewer_AssignAjax('bState', $oTopic->isControlLocked());
        }
        else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }
    }

    protected
    function EventGetObjectVotes()
    {
        $targetId = (int)getRequestStr('targetId', null, 'post');
        $targetType = getRequestStr('targetType', null, 'post');
        switch($targetType) {
            case 'comment':
                $oTarget = $this->Comment_GetCommentById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.comment.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.comment.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.comment.oe_end');
                $date_sort = Config::Get('acl.vote_list.comment.date_sort');
                break;
            case 'topic':
                $oTarget = $this->Topic_GetTopicById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.topic.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.topic.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.topic.oe_end');
                $date_sort = Config::Get('acl.vote_list.topic.date_sort');
                break;
            case 'blog':
                $oTarget = $this->Blog_GetBlogById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.blog.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.blog.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.blog.oe_end');
                $date_sort = Config::Get('acl.vote_list.blog.date_sort');
                break;
            case 'user':
                $oTarget = $this->User_GetUserById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.user.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.user.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.user.oe_end');
                $date_sort = Config::Get('acl.vote_list.user.date_sort');
                break;
            default:
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
                return;
        }

        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent && $ne_enable_level < 8) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization') , $this->Lang_Get('error'));
            return;
        }

        /**
         * Объект существует?
         */
        if (!$oTarget) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
            return;
        }

        if (!$this->ACL_CheckSimpleAccessLevel($ne_enable_level, $this->oUserCurrent, $oTarget, $targetType)) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access') , $this->Lang_Get('error'));
            return;
        }

        $aVotes = $this->Vote_GetVoteById($targetId, $targetType);
        $aResult = array();
        foreach($aVotes as $oVote) {
            $oUser = $this->User_GetUserById($oVote->getVoterId());
            $bShowUser = $oUser && (strtotime($oVote->getDate()) > $oe_end || $this->ACL_CheckSimpleAccessLevel($oe_enable_level, $this->oUserCurrent, $oTarget, $targetType));
            $aResult[] = array(
                'voterName' => $bShowUser ? $oUser->getLogin() : null,
                'voterAvatar' => $bShowUser ? $oUser->getProfileAvatarPath() : null,
                'value' => (float)$oVote->getDirection() ,
                'date' => (string)$oVote->getDate() . '+03:00',
            );
        }

        usort($aResult, $date_sort<0 ? '_gov_s_date_desc' : '_gov_s_date_asc');
        $this->Viewer_AssignAjax('aVotes', $aResult);
    }
    
    
    /**
     * Allow|forbid ignore user
     */
    protected function EventForbidIgnoreUser()
    {
        // check auth
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        // allow only for administrator
        if (!$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return;
        }
        // search for user
        if (!$oUser = $this->User_GetUserById(getRequest('idUser'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('user_not_found'), $this->Lang_Get('error'));
            return;
        }

        $aForbidIgnore = $this->User_GetForbidIgnoredUsers();
        if (in_array($oUser->getId(), $aForbidIgnore)) {
            // remove user from forbid ignore list
            if ($this->User_AllowIgnoreUser($oUser->getId())) {
                $this->Message_AddNoticeSingle($this->Lang_Get('allow_ignore_user_ok'), $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('sText', $this->Lang_Get('forbid_ignore_user'));
            } else {
                $this->Message_AddErrorSingle(
                    $this->Lang_Get('system_error'), $this->Lang_Get('error')
                );
            }
        } else {
            // add user to forbid ignore list
            if ($this->User_ForbidIgnoreUser($oUser->getId())) {
                $this->Message_AddNoticeSingle($this->Lang_Get('forbid_ignore_user_ok'), $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('sText', $this->Lang_Get('allow_ignore_user'));
            } else {
                $this->Message_AddErrorSingle(
                    $this->Lang_Get('system_error'), $this->Lang_Get('error')
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
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }

        // search for ignored user
        if (!$oUserIgnored = $this->User_GetUserById(getRequest('idUser'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('user_not_found'), $this->Lang_Get('error'));
            return;
        }

        // is user try to ignore self
        if ($oUserIgnored->getId() == $this->oUserCurrent->getId()) {
            $this->Message_AddErrorSingle($this->Lang_Get('ignore_dissalow_own'), $this->Lang_Get('error'));
            return;
        }
        $sType = getRequest('type');

        if ($sType == ModuleUser::TYPE_IGNORE_COMMENTS || $sType == ModuleUser::TYPE_IGNORE_TOPICS) {
            if ($this->User_IsUserIgnoredByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                // remove user from ignore list
                if ($this->User_UnIgnoreUserByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                    $this->Message_AddNoticeSingle($this->Lang_Get('disignore_user_ok_' . $sType), $this->Lang_Get('attention'));
                    $this->Viewer_AssignAjax('sText', $this->Lang_Get('ignore_user_' . $sType));
                } else {
                    $this->Message_AddErrorSingle(
                        $this->Lang_Get('system_error'), $this->Lang_Get('error')
                    );
                }
            } else {
                $aForbidIgnore = $this->User_GetForbidIgnoredUsers();
                //check ignored user in forbid ignored list
                if (in_array($oUserIgnored->getId(), $aForbidIgnore)) {
                    $this->Message_AddErrorSingle($this->Lang_Get('ignore_dissalow_this'), $this->Lang_Get('error'));
                    return;
                }

                //add user to ignore list
                if ($this->User_IgnoreUserByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                    $this->Message_AddNoticeSingle($this->Lang_Get('ignore_user_ok_' . $sType), $this->Lang_Get('attention'));
                    $this->Viewer_AssignAjax('sText', $this->Lang_Get('disignore_user_' . $sType));
                } else {
                    $this->Message_AddErrorSingle(
                        $this->Lang_Get('system_error'), $this->Lang_Get('error')
                    );
                }
            }
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }
    }
    
    protected function EventGetHistory()
    {
        /**
		 * Устанавливаем формат Ajax ответа
		 */
        $this->Viewer_SetResponseAjax('json');
        
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $oComment=$this->Comment_GetCommentById(getRequest('reply'));
        
        if (!$oComment)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }

        if ($oComment->getTargetType() =='talk') {
            if (!($oTalk = $this->Talk_GetTalkById($oComment->getTargetId()))) {
                echo "NO TARGET";
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }
            /**
             * Пользователь есть в переписке?
             */
            if (!($oTalkUser = $this->Talk_GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }
            /**
             * Пользователь активен в переписке?
             */
            if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }
        } else {
            if ($oComment->getTargetType() != 'topic' or !($oTopic = $oComment->getTarget())) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }
            if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTopic->getUserId() and !$this->oUserCurrent->isAdministrator()))) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
                return;
            }
            /**
             * Проверяет коммент на удаленность и отдает его только автору, и тем, у кого есть права на удаление.
             */
            if ((!$this->oUserCurrent || ($oComment->getDelete() && !($this->ACL_UserCanDeleteComment($this->oUserCurrent, $oComment, 1) || $this->oUserCurrent->getId() == $oComment->getUserId())))) {
                $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('not_access'));
                return Router::Action('error');
            }
            /**
             * Проверяет коммент на доступность из закрытых блогов.
             */
            if (in_array($oTopic->getBlog()->getType(), array('close', 'invite'))
                and (!$this->oUserCurrent
                    || !in_array(
                        $oTopic->getBlog()->getId(),
                        $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent)
                    )
                )
            ) {
                $this->Message_AddErrorSingle($this->Lang_Get('blog_close_show'), $this->Lang_Get('not_access'));
                return Router::Action('error');
            }
        }
        
        $aData=$this->Editcomment_GetDataItemsByCommentId($oComment->getId(), array('#order'=>array('date_add'=>'desc')));

        foreach ($aData as $oData) {
			$oUser = $this->User_GetUserById($oData->getUserId());
			$oData->setText($this->Text_Parser($oData->getCommentTextSource()));
			$oData->setUserLogin($oUser->getLogin());
		}
        
        $oViewerLocal=$this->Viewer_GetLocalViewer();
        $oViewerLocal->Assign('aHistory', $aData);
        $this->Viewer_AssignAjax('sContent', $oViewerLocal->Fetch('history.tpl'));
    }

    protected function EventGetSource()
    {
        /**
		 * Устанавливаем формат Ajax ответа
		 */
        $this->Viewer_SetResponseAjax('json');
        
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $oComment=$this->Comment_GetCommentById(getRequest('idComment'));
        
        if (!$oComment)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $sCheckResult=$this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true)
        {
            $this->Message_AddErrorSingle($sCheckResult);
            return;
        }
        
        $oEditData=$this->Editcomment_GetLastEditData($oComment->getId());
        
        if ($oEditData)
            $sCommentSource=$oEditData->getCommentTextSource();
        else 
            if (!Config::Get('view.tinymce'))
                $sCommentSource=str_replace(array("<br>", "<br/>"), array(""), $oComment->getText());
            else
                $sCommentSource=$oComment->getText();
        
        $this->Viewer_AssignAjax('sCommentSource', $sCommentSource);
        $this->Viewer_AssignAjax('bHasHistory', !is_null($oEditData));
    }

    protected function EventEdit()
    {
        $ip=$_SERVER['REMOTE_ADDR'];
        /**
		 * Устанавливаем формат Ajax ответа
		 */
        $this->Viewer_SetResponseAjax('json');
        
        if (!$this->oUserCurrent)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $oComment=$this->Comment_GetCommentById(getRequest('reply'));
        
        if (!$oComment)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'));
            return;
        }
        
        $sCheckResult=$this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true)
        {
            $this->Message_AddErrorSingle($sCheckResult);
            return;
        }

        $bMark = getRequestStr('form_comment_mark')=="on";
        if ($bMark)
            $sText = $this->Text_Parser($this->Text_Mark(getRequestStr('comment_text')));
        else
            $sText = $this->Text_Parser(getRequestStr('comment_text'));

        $sText = preg_replace_callback('/@(.*?)\((.*?)\)/',
            function ($matches) use ($oComment) {
                $sLogin = $matches[1];
                $sNick = $matches[2];
                $r = "<a href=\"/profile/" . $sLogin . "/\" class=\"ls-user\">&#64;" . $sNick . "</a>";
                if ($oTargetUser = $this->User_getUserByLogin($sLogin)) {
                    $this->Cast_sendCastNotifyToUser("comment", $oComment, $this->Topic_GetTopicById($oComment->getTargetId()), $oTargetUser);
                    return $r;
                }
                return $matches[0];
            }, $sText);
        $sText = preg_replace_callback('/@([a-zA-Zа-яА-Я0-9-_]+)/',
            function ($matches) use ($oComment) {
                $sLogin = $matches[1];
                $r = "<a href=\"/profile/" . $sLogin . "/\" class=\"ls-user\">&#64;" . $sLogin . "</a>";
                if ($oTargetUser = $this->User_getUserByLogin($sLogin)) {
                    $this->Cast_sendCastNotifyToUser("comment", $oComment, $this->Topic_GetTopicById($oComment->getTargetId()), $oTargetUser);
                    return $r;
                }
                return $matches[0];
            }, $sText);
        
        if (mb_strlen($sText, 'utf-8') > Config::Get('module.comment.max_length'))
        {
            $this->Message_AddErrorSingle($this->Lang_Get('editcomment.err_max_comment_length', array('maxlength'=>Config::Get('max_comment_length'))));
            return;
        }
        
        $sDE=date("Y-m-d H:i:s");

        $bEdited = false;
        if ($oComment->getText() == $sText)
        {
            $this->Message_AddNoticeSingle($this->Lang_Get('editcomment.notice_nothing_changed'));
			$bEdited = false;
            $this->Viewer_AssignAjax('bEdited', $bEdited);
        }
        else
        {
            if (Config::Get('change_online'))
                $oComment->setDate($sDE);
            $oComment->setEditCount($oComment->getEditCount() + 1);
            $oComment->setEditDate($sDE);
            $oViewerLocal=$this->Viewer_GetLocalViewer();
            $oViewerLocal->Assign('oComment', $oComment);
            $oViewerLocal->Assign('oUserCurrent', $this->oUserCurrent);
            
            if (Config::Get('add_edit_date'))
                $oComment->setText($sText . $oViewerLocal->Fetch('inject_comment_edited.tpl'));
            else
                $oComment->setText($sText);
            $oComment->setText($this->Text_CommentParser($oComment,getRequestStr('form_comment_mark')=="on",true));
            $oComment->setTextHash(md5($oComment->getText()));
            
            if ($this->Comment_UpdateComment($oComment))
            {
                if (Config::Get('change_online'))
                {
                    $oCommentOnline=Engine::GetEntity('Comment_CommentOnline');
                    $oCommentOnline->setTargetId($oComment->getTargetId());
                    $oCommentOnline->setTargetType($oComment->getTargetType());
                    $oCommentOnline->setTargetParentId($oComment->getTargetParentId());
                    $oCommentOnline->setCommentId($oComment->getId());
                    
                    $this->Comment_AddCommentOnline($oCommentOnline);
                }
                
                $this->oUserCurrent->setDateCommentLast($sDE);
                $this->User_Update($this->oUserCurrent);
                
                $oData=Engine::GetEntity('ModuleEditcomment_EntityData');
                $oData->setCommentTextSource(getRequest('comment_text'));
                $oData->setCommentId($oComment->getId());
                $oData->setUserId($this->oUserCurrent->getId());
                $oData->setDateAdd($sDE);
                
                if (!$oData->save())
                {
                    $this->Message_AddErrorSingle($this->Lang_Get('error'));
                    return;
                }
                elseif (Config::Get('max_history_depth') > 0)
                {
                    $aTemp=$this->Editcomment_GetDataItemsByFilter(array('comment_id'=>$oComment->getId(), '#page'=>array(1, 0)));
                    if ($aTemp['count'] > Config::Get('max_history_depth'))
                    {
                        $aOldData=$this->Editcomment_GetDataItemsByFilter(array('comment_id'=>$oComment->getId(), '#order'=>array('date_add'=>'asc'), '#limit'=>array(0, $aTemp['count'] - Config::Get('max_history_depth'))));
                        foreach ($aOldData as $oOldData)
                            $oOldData->delete();
                    }
                }
				$bEdited = true;
                $this->Viewer_AssignAjax('bEdited', $bEdited);
                $this->Viewer_AssignAjax('bCanEditMore', $this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX) === true);
                $this->Viewer_AssignAjax('sCommentText', $oComment->getText());
            }
            else
                $this->Message_AddErrorSingle($this->Lang_Get('error'));
        }
		if ($bEdited) {
			if ($oComment->getTargetType() == 'topic') {

				/**
				 * Отправка уведомления пользователям
				 */
				$notificationLink = $this->Topic_GetTopicById($oComment->getTargetId())->getUrl(). "#comment" . $oComment->getId();
				$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a>" . " отредактировал ваш <a href='".$notificationLink."'>комментарий</a> в посте <a href='/blog/undefined/" . $oComment->getTargetId()."'>".$oComment->getTarget()->getTitle()."</a>";
				$notificationText = "";
				$notification = Engine::GetEntity(
					'Notification',
					array(
						'user_id' => $oComment->getUserId(),
						'text' => $notificationText,
						'title' => $notificationTitle,
						'link' => $notificationLink,
						'rating' => 0,
						'notification_type' => 6,
						'target_type' => 'comment',
						'target_id' => $oComment->getId(),
						'sender_user_id' => $this->oUserCurrent->getId(),
						'group_target_type' => 'topic',
						'group_target_id' => $oComment->getTargetId()
					)
				);
				if($notificationCreated = $this->Notification_createNotification($notification)){
					$this->Nower_PostNotificationWithComment($notificationCreated, $oComment);
				}

			} else if ($oComment->getTargetType() == 'talk') {
				if (!($oTalk = $this->Talk_GetTalkById($oComment->getTargetId()))) {
					$this->Message_AddErrorSingle($this->Lang_Get('error'));
					return;
				}
				/**
				 * Отправка уведомления пользователям
				 */
				$notificationLink = "/talk/" . $oComment->getTargetId() . "#comment" . $oComment->getId();
				$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a>" . " отредактировал ваш <a href='".$notificationLink."'>комментарий</a> в личке " . $oTalk->getTitle();
				$notificationText = "";
				$notification = Engine::GetEntity(
					'Notification',
					array(
						'user_id' => $oComment->getUserId(),
						'text' => $notificationText,
						'title' => $notificationTitle,
						'link' => $notificationLink,
						'rating' => 0,
						'notification_type' => 6,
						'target_type' => 'comment',
						'target_id' => $oComment->getId(),
						'sender_user_id' => $this->oUserCurrent->getId(),
						'group_target_type' => 'talk',
						'group_target_id' => $oComment->getTargetId()
					)
				);
				if($notificationCreated = $this->Notification_createNotification($notification)){
					$this->Nower_PostNotificationWithComment($notificationCreated, $oComment);
				}
			}

			$sLogText = $this->oUserCurrent->getLogin() . " редактировал коммент " . $oComment->getId() . " " . $ip;
			$this->Logger_Notice($sLogText);
		}
        $this->Viewer_AssignAjax('bCanEditMore', $this->ACL_UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX) === true);
    }

    protected function EventGetComment() {
    	$idComment=getRequestStr('idComment', null, 'post');
		if(!($oComment=$this->Comment_GetCommentById($idComment))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
			return;
		}
		if ($oComment->getTargetType()!='topic' or !($oTopic=$oComment->getTarget())) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
			return;
		}
		if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId()!=$oTopic->getUserId() and !$this->oUserCurrent->isAdministrator()))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error') , $this->Lang_Get('error'));
			return;
		}
		/**
         * Проверяет коммент на удаленность и отдает его только автору, и тем, у кого есть права на удаление.
		*/
        if ((!$this->oUserCurrent || ($oComment->getDelete() && !($this->ACL_UserCanDeleteComment($this->oUserCurrent, $oComment,1) || $this->oUserCurrent->getId()==$oComment->getUserId())))) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('not_access'));
            return Router::Action('error');
        }
		if(in_array($oTopic->getBlog()->getType(), array('close', 'invite'))
			and (!$this->oUserCurrent
				|| !in_array(
					$oTopic->getBlog()->getId(),
					$this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent)
				)
			)
		) {
			$this->Message_AddErrorSingle($this->Lang_Get('blog_close_show'),$this->Lang_Get('not_access'));
			return Router::Action('error');
		}
		$bIgnoreDelete=false;
		if ($this->oUserCurrent) {
    		if ($this->oUserCurrent->isAdministrator() || $this->oUserCurrent->isGlobalModerator()) {
    			$bIgnoreDelete=true;
    		}
    	}
		$aResult=$this->Comment_ConvertCommentToArray($oComment, $oTopic->getDateRead(), $bIgnoreDelete);
		$this->Viewer_AssignAjax("aComment", $aResult);
		$this->Viewer_DisplayAjax();
	}
}

function _gov_s_date_asc($a, $b)
{
    $a_time = strtotime($a['date']);
    $b_time = strtotime($b['date']);
    if ($a_time > $b_time) return 1;
    if ($a_time < $b_time) return -1;
    return 0;
}

function _gov_s_date_desc($a, $b)
{
    return -_gov_s_date_asc($a, $b);
}
