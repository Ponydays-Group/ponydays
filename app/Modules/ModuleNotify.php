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

namespace App\Modules;

use App\Entities\EntityBlog;
use App\Entities\EntityComment;
use App\Entities\EntityNotifyTask;
use App\Mappers\MapperNotify;
use App\Entities\EntityTalk;
use App\Entities\EntityTopic;
use App\Entities\EntityUserInvite;
use App\Entities\EntityUserReminder;
use App\Entities\EntityUser;
use App\Entities\EntityWall;
use Engine\Config;
use Engine\Engine;
use Engine\LS;
use Engine\Module;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMail;
use Engine\Modules\ModuleViewer;

/**
 * Модуль рассылок уведомлений пользователям
 *
 * @package modules.notify
 * @since 1.0
 */
class ModuleNotify extends Module {
	/**
	 * Статусы степени обработки заданий отложенной публикации в базе данных
	 */
	const NOTIFY_TASK_STATUS_NULL=1;
	/**
	 * Объект локального вьювера для рендеринга сообщений
	 *
	 * @var \Engine\Modules\ModuleViewer
	 */
	protected $oViewerLocal=null;
	/**
	 * Массив заданий на удаленную публикацию
	 *
	 * @var array
	 */
	protected $aTask=array();
	/**
	 * Объект маппера
	 *
	 * @var \App\Mappers\MapperNotify
	 */
	protected $oMapper=null;

	/**
	 * Инициализация модуля
	 * Создаём локальный экземпляр модуля Viewer
	 * Момент довольно спорный, но позволяет избавить основной шаблон от мусора уведомлений
	 *
	 */
	public function Init() {
		$this->oViewerLocal=LS::Make(ModuleViewer::class)->GetLocalViewer();
		$this->oMapper=Engine::MakeMapper(MapperNotify::class);
	}
	/**
	 * Отправляет юзеру уведомление о новом комментарии в его топике
	 *
	 * @param EntityUser                $oUserTo      Объект пользователя кому отправляем
	 * @param \App\Entities\EntityTopic $oTopic       Объект топика
	 * @param EntityComment             $oComment     Объект комментария
	 * @param \App\Entities\EntityUser  $oUserComment Объект пользователя, написавшего комментарий
	 *
	 * @return bool
	 */
	public function SendCommentNewToAuthorTopic(EntityUser $oUserTo, EntityTopic $oTopic, EntityComment $oComment, EntityUser $oUserComment) {
		/**
		 * Проверяем можно ли юзеру рассылать уведомление
		 */
		if (!$oUserTo->getSettingsNoticeNewComment()) {
			return false;
		}
		$this->Send(
			$oUserTo,
			'notify.comment_new.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_comment_new'),
			array(
				'oUserTo' => $oUserTo,
				'oTopic' => $oTopic,
				'oComment' => $oComment,
				'oUserComment' => $oUserComment,
			)
		);
		return true;
	}
	/**
	 * Отправляет юзеру уведомление об ответе на его комментарий
	 *
	 * @param \App\Entities\EntityUser $oUserTo      Объект пользователя кому отправляем
	 * @param EntityTopic              $oTopic       Объект топика
	 * @param EntityComment            $oComment     Объект комментария
	 * @param \App\Entities\EntityUser $oUserComment Объект пользователя, написавшего комментарий
	 *
	 * @return bool
	 */
	public function SendCommentReplyToAuthorParentComment(EntityUser $oUserTo, EntityTopic $oTopic, EntityComment $oComment, EntityUser $oUserComment) {
		/**
		 * Проверяем можно ли юзеру рассылать уведомление
		 */
		if (!$oUserTo->getSettingsNoticeReplyComment()) {
			return false;
		}
		$this->Send(
			$oUserTo,
			'notify.comment_reply.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_comment_reply'),
			array(
				'oUserTo' => $oUserTo,
				'oTopic' => $oTopic,
				'oComment' => $oComment,
				'oUserComment' => $oUserComment,
			)
		);
		return true;
	}
	/**
	 * Отправляет юзеру уведомление о новом топике в блоге, в котором он состоит
	 *
	 * @param EntityUser               $oUserTo    Объект пользователя кому отправляем
	 * @param EntityTopic              $oTopic     Объект топика
	 * @param \App\Entities\EntityBlog $oBlog      Объект блога
	 * @param EntityUser               $oUserTopic Объект пользователя, написавшего топик
	 *
	 * @return bool
	 */
	public function SendTopicNewToSubscribeBlog(EntityUser $oUserTo, EntityTopic $oTopic,
        EntityBlog $oBlog, EntityUser $oUserTopic) {
		/**
		 * Проверяем можно ли юзеру рассылать уведомление
		 */
		if (!$oUserTo->getSettingsNoticeNewTopic()) {
			return false;
		}
		$this->Send(
			$oUserTo,
			'notify.topic_new.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_topic_new').' «'.htmlspecialchars($oBlog->getTitle()).'»',
			array(
				'oUserTo' => $oUserTo,
				'oTopic' => $oTopic,
				'oBlog' => $oBlog,
				'oUserTopic' => $oUserTopic,
			)
		);
		return true;
	}
	/**
	 * Отправляет уведомление с новым линком активации
	 *
	 * @param \App\Entities\EntityUser $oUser Объект пользователя
	 */
	public function SendReactivationCode(EntityUser $oUser) {
		$this->Send(
			$oUser,
			'notify.reactivation.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_reactvation'),
			array(
				'oUser' => $oUser,
			)
		);
	}
	/**
	 * Отправляет уведомление при регистрации с активацией
	 *
	 * @param \App\Entities\EntityUser $oUser     Объект пользователя
	 * @param string                   $sPassword Пароль пользователя
	 */
	public function SendRegistrationActivate(EntityUser $oUser,$sPassword) {
		$this->Send(
			$oUser,
			'notify.registration_activate.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_registration_activate'),
			array(
				'oUser' => $oUser,
				'sPassword' => $sPassword,
			)
		);
	}
	/**
	 * Отправляет уведомление о регистрации
	 *
	 * @param \App\Entities\EntityUser $oUser     Объект пользователя
	 * @param string                   $sPassword Пароль пользователя
	 */
	public function SendRegistration(EntityUser $oUser,$sPassword) {
		$this->Send(
			$oUser,
			'notify.registration.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_registration'),
			array(
				'oUser' => $oUser,
				'sPassword' => $sPassword,
			)
		);
	}
	/**
	 * Отправляет инвайт
	 *
	 * @param \App\Entities\EntityUser $oUserFrom Пароль пользователя, который отправляет инвайт
	 * @param string                   $sMailTo   Емайл на который отправляем инвайт
	 * @param EntityUserInvite         $oInvite   Объект инвайта
	 */
	public function SendInvite(EntityUser $oUserFrom,$sMailTo,EntityUserInvite $oInvite) {
		$this->Send(
			$sMailTo,
			'notify.invite.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_invite'),
			array(
				'sMailTo' => $sMailTo,
				'oUserFrom' => $oUserFrom,
				'oInvite' => $oInvite,
			)
		);
	}
	/**
	 * Отправляет уведомление при новом личном сообщении
	 *
	 * @param \App\Entities\EntityUser $oUserTo   Объект пользователя, которому отправляем сообщение
	 * @param \App\Entities\EntityUser $oUserFrom Объект пользователя, который отправляет сообщение
	 * @param \App\Entities\EntityTalk $oTalk     Объект сообщения
	 *
	 * @return bool
	 */
	public function SendTalkNew(EntityUser $oUserTo,EntityUser $oUserFrom,EntityTalk $oTalk) {
		/**
		 * Проверяем можно ли юзеру рассылать уведомление
		 */
		if (!$oUserTo->getSettingsNoticeNewTalk()) {
			return false;
		}
		$this->Send(
			$oUserTo,
			'notify.talk_new.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_talk_new'),
			array(
				'oUserTo' => $oUserTo,
				'oUserFrom' => $oUserFrom,
				'oTalk' => $oTalk,
			)
		);
		return true;
	}
	/**
	 * Отправляет уведомление о новом сообщение в личке
	 *
	 * @param \App\Entities\EntityUser $oUserTo      Объект пользователя, которому отправляем уведомление
	 * @param \App\Entities\EntityUser $oUserFrom    Объект пользователя, которыф написал комментарий
	 * @param \App\Entities\EntityTalk $oTalk        Объект сообщения
	 * @param EntityComment            $oTalkComment Объект комментария
	 *
	 * @return bool
	 */
	public function SendTalkCommentNew(EntityUser $oUserTo,EntityUser $oUserFrom,EntityTalk $oTalk,EntityComment $oTalkComment) {
		/**
		 * Проверяем можно ли юзеру рассылать уведомление
		 */
		if (!$oUserTo->getSettingsNoticeNewTalk()) {
			return false;
		}
		$this->Send(
			$oUserTo,
			'notify.talk_comment_new.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_talk_comment_new'),
			array(
				'oUserTo' => $oUserTo,
				'oUserFrom' => $oUserFrom,
				'oTalk' => $oTalk,
				'oTalkComment' => $oTalkComment,
			)
		);
		return true;
	}
	/**
	 * Отправляет пользователю сообщение о добавлении его в друзья
	 *
	 * @param \App\Entities\EntityUser $oUserTo   Объект пользователя
	 * @param \App\Entities\EntityUser $oUserFrom Объект пользователя, которого добавляем в друзья
	 * @param string                   $sText     Текст сообщения
	 * @param string                   $sPath     URL для подтверждения дружбы
	 *
	 * @return bool
	 */
	public function SendUserFriendNew(EntityUser $oUserTo,EntityUser $oUserFrom, $sText,$sPath) {
		/**
		 * Проверяем можно ли юзеру рассылать уведомление
		 */
		if (!$oUserTo->getSettingsNoticeNewFriend()) {
			return false;
		}
		$this->Send(
			$oUserTo,
			'notify.user_friend_new.tpl',
			LS::Make(ModuleLang::class)->Get('notify_subject_user_friend_new'),
			array(
				'oUserTo' => $oUserTo,
				'oUserFrom' => $oUserFrom,
				'sText' => $sText,
				'sPath' => $sPath,
			)
		);
		return true;
	}
	/**
	 * Отправляет пользователю сообщение о приглашение его в закрытый блог
	 *
	 * @param EntityUser               $oUserTo   Объект пользователя, который отправляет приглашение
     * @param \App\Entities\EntityUser $oUserFrom Объект пользователя, которого приглашаем
	 * @param \App\Entities\EntityBlog $oBlog     Объект блога
	 * @param $sPath
	 */
	public function SendBlogUserInvite(EntityUser $oUserTo,EntityUser $oUserFrom,
        EntityBlog $oBlog,$sPath) {
		$this->Send(
			$oUserTo,
			'notify.blog_invite_new.tpl',
            LS::Make(ModuleLang::class)->Get('notify_subject_blog_invite_new'),
			array(
				'oUserTo' => $oUserTo,
				'oUserFrom' => $oUserFrom,
				'oBlog' => $oBlog,
				'sPath' => $sPath,
			)
		);
	}
	/**
	 * Уведомление при восстановлении пароля
	 *
	 * @param EntityUser                       $oUser     Объект пользователя
	 * @param \App\Entities\EntityUserReminder $oReminder объект напоминания пароля
	 */
	public function SendReminderCode(EntityUser $oUser,EntityUserReminder $oReminder) {
		$this->Send(
			$oUser,
			'notify.reminder_code.tpl',
            LS::Make(ModuleLang::class)->Get('notify_subject_reminder_code'),
			array(
				'oUser' => $oUser,
				'oReminder' => $oReminder,
			)
		);
	}
	/**
	 * Уведомление с новым паролем после его восставновления
	 *
     * @param \App\Entities\EntityUser $oUser        Объект пользователя
	 * @param string                   $sNewPassword Новый пароль
	 */
	public function SendReminderPassword(EntityUser $oUser,$sNewPassword) {
		$this->Send(
			$oUser,
			'notify.reminder_password.tpl',
            LS::Make(ModuleLang::class)->Get('notify_subject_reminder_password'),
			array(
				'oUser' => $oUser,
				'sNewPassword' => $sNewPassword,
			)
		);
	}
	/**
	 * Уведомление при ответе на сообщение на стене
	 *
	 * @param \App\Entities\EntityWall $oWallParent Объект сообщения на стене, на которое отвечаем
	 * @param \App\Entities\EntityWall $oWall       Объект нового сообщения на стене
     * @param \App\Entities\EntityUser $oUser       Объект пользователя
	 */
	public function SendWallReply(EntityWall $oWallParent, EntityWall $oWall, EntityUser $oUser) {
		$this->Send(
			$oWallParent->getUser(),
			'notify.wall.reply.tpl',
            LS::Make(ModuleLang::class)->Get('notify_subject_wall_reply'),
			array(
				'oWallParent' => $oWallParent,
				'oUserTo' => $oWallParent->getUser(),
				'oWall' => $oWall,
				'oUser' => $oUser,
				'oUserWall' => $oWall->getWallUser(), // кому принадлежит стена
			)
		);
	}
	/**
	 * Уведомление о новом сообщение на стене
	 *
	 * @param \App\Entities\EntityWall $oWall Объект нового сообщения на стене
	 * @param \App\Entities\EntityUser $oUser Объект пользователя
	 */
	public function SendWallNew(EntityWall $oWall, EntityUser $oUser) {
		$this->Send(
			$oWall->getWallUser(),
			'notify.wall.new.tpl',
            LS::Make(ModuleLang::class)->Get('notify_subject_wall_new'),
			array(
				'oUserTo' => $oWall->getWallUser(),
				'oWall' => $oWall,
				'oUser' => $oUser,
				'oUserWall' => $oWall->getWallUser(), // кому принадлежит стена
			)
		);
	}
	/**
	 * Универсальный метод отправки уведомлений на email
	 *
	 * @param EntityUser|string $oUserTo     Кому отправляем (пользователь или email)
	 * @param string            $sTemplate   Шаблон для отправки
	 * @param string            $sSubject    Тема письма
	 * @param array             $aAssign     Ассоциативный массив для загрузки переменных в шаблон письма
	 * @param string|null       $sPluginName Плагин из которого происходит отправка
	 */
	public function Send($oUserTo,$sTemplate,$sSubject,$aAssign=array(),$sPluginName=null) {
		if ($oUserTo instanceof EntityUser) {
			$sMail=$oUserTo->getMail();
			$sName=$oUserTo->getLogin();
		} else {
			$sMail=$oUserTo;
			$sName='';
		}
		/**
		 * Передаём в шаблон переменные
		 */
		foreach ($aAssign as $k=>$v) {
			$this->oViewerLocal->Assign($k,$v);
		}
		/**
		 * Формируем шаблон
		 */
		$sBody=$this->oViewerLocal->Fetch('notify/russian/'.$sTemplate);
		/**
		 * Если в конфигураторе указан отложенный метод отправки,
		 * то добавляем задание в массив. В противном случае,
		 * сразу отсылаем на email
		 */
		if(Config::Get('module.notify.delayed')) {
			$oNotifyTask = new EntityNotifyTask(
				array(
					'user_mail'      => $sMail,
					'user_login'     => $sName,
					'notify_text'    => $sBody,
					'notify_subject' => $sSubject,
					'date_created'   => date("Y-m-d H:i:s"),
					'notify_task_status' => self::NOTIFY_TASK_STATUS_NULL,
				)
			);
			if(Config::Get('module.notify.insert_single')) {
				$this->aTask[] = $oNotifyTask;
			} else {
				$this->oMapper->AddTask($oNotifyTask);
			}
		} else {
			/**
			 * Отправляем мыло
			 */
			/** @var ModuleMail $mail */
			$mail = LS::Make(ModuleMail::class);
			$mail->SetAdress($sMail,$sName);
			$mail->SetSubject($sSubject);
			$mail->SetBody($sBody);
			$mail->setHTML();
			$mail->Send();
		}
	}
	/**
	 * При завершении работы модуля проверяем наличие
	 * отложенных заданий в массиве и при необходимости
	 * передаем их в меппер
	 */
	public function Shutdown() {
		if(!empty($this->aTask) && Config::Get('module.notify.delayed')) {
			$this->oMapper->AddTaskArray($this->aTask);
			$this->aTask=array();
		}
	}
	/**
	 * Получает массив заданий на публикацию из базы с указанным количественным ограничением (выборка FIFO)
	 *
	 * @param  int	$iLimit	Количество
	 * @return array
	 */
	public function GetTasksDelayed($iLimit=10) {
		return ($aResult=$this->oMapper->GetTasks($iLimit))
			? $aResult
			: array();
	}
	/**
	 * Отправляет на e-mail
	 *
	 * @param \App\Entities\EntityNotifyTask $oTask Объект задания на отправку
	 */
	public function SendTask($oTask) {
        /** @var ModuleMail $mail */
        $mail = LS::Make(ModuleMail::class);
        $mail->SetAdress($oTask->getUserMail(),$oTask->getUserLogin());
		$mail->SetSubject($oTask->getNotifySubject());
		$mail->SetBody($oTask->getNotifyText());
		$mail->setHTML();
		$mail->Send();
	}
	/**
	 * Удаляет отложенное Notify-задание из базы
	 *
	 * @param  \App\Entities\EntityNotifyTask $oTask Объект задания на отправку
	 *
	 * @return bool
	 */
	public function DeleteTask($oTask) {
		return $this->oMapper->DeleteTask($oTask);
	}
	/**
	 * Удаляет отложенные Notify-задания по списку идентификаторов
	 *
	 * @param  array $aArrayId	Список ID заданий на отправку
	 * @return bool
	 */
	public function DeleteTaskByArrayId($aArrayId) {
		return $this->oMapper->DeleteTaskByArrayId($aArrayId);
	}
}
