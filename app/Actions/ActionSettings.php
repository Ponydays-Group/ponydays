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

use App\Modules\ACL\ModuleACL;
use App\Modules\Comment\ModuleComment;
use App\Modules\Crypto\ModuleCrypto;
use App\Modules\Geo\ModuleGeo;
use App\Modules\Notify\ModuleNotify;
use App\Modules\Topic\ModuleTopic;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use App\Modules\Wall\ModuleWall;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Hook\ModuleHook;
use Engine\Modules\Image\ModuleImage;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Security\ModuleSecurity;
use Engine\Modules\Session\ModuleSession;
use Engine\Modules\Text\ModuleText;
use Engine\Modules\Viewer\ModuleViewer;
use Engine\Router;

/**
 * Экшен обрабтки настроек профиля юзера (/settings/)
 *
 * @package actions
 * @since 1.0
 */
class ActionSettings extends Action {
	/**
	 * Какое меню активно
	 *
	 * @var string
	 */
	protected $sMenuItemSelect='settings';
	/**
	 * Какое подменю активно
	 *
	 * @var string
	 */
	protected $sMenuSubItemSelect='profile';
	/**
	 * Текущий юзер
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		/**
		 * Проверяем авторизован ли юзер
		 */
		if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'),LS::Make(ModuleLang::class)->Get('error'));
			return Router::Action('error');
		}
		/**
		 * Получаем текущего юзера
		 */
		$this->oUserCurrent=LS::Make(ModuleUser::class)->GetUserCurrent();
		$this->SetDefaultEvent('profile');
		/**
		 * Устанавливаем title страницы
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('settings_menu'));
	}
	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent() {
		$this->AddEventPreg('/^profile$/i','/^upload-avatar/i','/^$/i','EventUploadAvatar');
		$this->AddEventPreg('/^profile$/i','/^resize-avatar/i','/^$/i','EventResizeAvatar');
		$this->AddEventPreg('/^profile$/i','/^remove-avatar/i','/^$/i','EventRemoveAvatar');
		$this->AddEventPreg('/^profile$/i','/^cancel-avatar/i','/^$/i','EventCancelAvatar');
		$this->AddEventPreg('/^profile$/i','/^upload-foto/i','/^$/i','EventUploadFoto');
		$this->AddEventPreg('/^profile$/i','/^resize-foto/i','/^$/i','EventResizeFoto');
		$this->AddEventPreg('/^profile$/i','/^remove-foto/i','/^$/i','EventRemoveFoto');
		$this->AddEventPreg('/^profile$/i','/^cancel-foto/i','/^$/i','EventCancelFoto');
		$this->AddEvent('profile','EventProfile');
		$this->AddEvent('tuning','EventTuning');
		$this->AddEvent('account','EventAccount');
		$this->AddEvent('behavior','EventBehavior');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Загрузка временной картинки фото для последущего ресайза
	 */
	protected function EventUploadFoto() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('jsonIframe',false);

		if(!isset($_FILES['foto']['tmp_name'])) {
			return false;
		}
		/**
		 * Копируем загруженный файл
		 */
		$sFileTmp=Config::Get('sys.cache.dir').func_generator();
		if (!move_uploaded_file($_FILES['foto']['tmp_name'],$sFileTmp)) {
			return false;
		}
		/**
		 * Ресайзим и сохраняем именьшенную копию
		 * Храним две копии - мелкую для показа пользователю и крупную в качестве исходной для ресайза
		 */
		$sDir=Config::Get('path.uploads.images')."/tmp/fotos/{$this->oUserCurrent->getId()}";
		if ($sFile=LS::Make(ModuleImage::class)->Resize($sFileTmp,$sDir,'original',Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),10000,null,true)) {
			if ($sFilePreview=LS::Make(ModuleImage::class)->Resize($sFileTmp,$sDir,'preview',Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),400,null,true)) {
				/**
				 * Сохраняем в сессии временный файл с изображением
				 */
                $oImage = LS::Make(ModuleImage::class)->CreateImageObject($sFile);
                $iHSource = $oImage->get_image_params('height');
				LS::Make(ModuleSession::class)->Set('sFotoFileTmp',$sFile);
				LS::Make(ModuleSession::class)->Set('sFotoFilePreviewTmp',$sFilePreview);
                LS::Make(ModuleViewer::class)->AssignAjax('sTmpFile',LS::Make(ModuleImage::class)->GetWebPath($sFilePreview));
                LS::Make(ModuleViewer::class)->AssignAjax('iHeight',400*(200/1340));
				unlink($sFileTmp);
				return;
			}
		}
		LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleImage::class)->GetLastError(),LS::Make(ModuleLang::class)->Get('error'));
		unlink($sFileTmp);
	}
	/**
	 * Вырезает из временной фотки область нужного размера, ту что задал пользователь
	 */
	protected function EventResizeFoto() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Достаем из сессии временный файл
		 */
		$sFile=LS::Make(ModuleSession::class)->Get('sFotoFileTmp');
		$sFilePreview=LS::Make(ModuleSession::class)->Get('sFotoFilePreviewTmp');
		if (!file_exists($sFile)) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			return;
		}
		/**
		 * Определяем размер большого фото для подсчета множителя пропорции
		 */
		$fRation=1;
		if ($aSizeFile=getimagesize($sFile) and isset($aSizeFile[0])) {
			$fRation=$aSizeFile[0]/400; // 200 - размер превью по которой пользователь определяет область для ресайза
			if ($fRation<1)
				$fRation=1;
		}
		/**
		 * Получаем размер области из параметров
		 */
		$aSize=array();
		$aSizeTmp=getRequest('size');
		if (isset($aSizeTmp['x']) and is_numeric($aSizeTmp['x'])
			and isset($aSizeTmp['y']) and is_numeric($aSizeTmp['y'])
				and isset($aSizeTmp['x2']) and is_numeric($aSizeTmp['x2'])
					and isset($aSizeTmp['y2']) and is_numeric($aSizeTmp['y2'])) {
			$aSize=array('x1'=>round($fRation*$aSizeTmp['x']),'y1'=>round($fRation*$aSizeTmp['y']),'x2'=>round($fRation*$aSizeTmp['x2']),'y2'=>round($fRation*$aSizeTmp['y2']));
		}
		/**
		 * Вырезаем аватарку
		 */
		if ($sFileWeb=LS::Make(ModuleUser::class)->UploadFoto($sFile,$this->oUserCurrent,$aSize)) {
			/**
			 * Удаляем старые аватарки
			 */
			$this->oUserCurrent->setProfileFoto($sFileWeb);
			LS::Make(ModuleUser::class)->Update($this->oUserCurrent);
			LS::Make(ModuleImage::class)->RemoveFile($sFilePreview);
			/**
			 * Удаляем из сессии
			 */
			LS::Make(ModuleSession::class)->Drop('sFotoFileTmp');
			LS::Make(ModuleSession::class)->Drop('sFotoFilePreviewTmp');
			LS::Make(ModuleViewer::class)->AssignAjax('sFile',$this->oUserCurrent->getProfileFoto());
			LS::Make(ModuleViewer::class)->AssignAjax('sTitleUpload',LS::Make(ModuleLang::class)->Get('settings_profile_photo_change'));
		} else {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_profile_avatar_error'),LS::Make(ModuleLang::class)->Get('error'));
		}
	}
	/**
	 * Удаляет фото
	 */
	protected function EventRemoveFoto() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Удаляем
		 */
		LS::Make(ModuleUser::class)->DeleteFoto($this->oUserCurrent);
		$this->oUserCurrent->setProfileFoto(null);
		LS::Make(ModuleUser::class)->Update($this->oUserCurrent);
		/**
		 * Возвращает дефолтную аватарку
		 */
		LS::Make(ModuleViewer::class)->AssignAjax('sFile',$this->oUserCurrent->getProfileFotoDefault());
		LS::Make(ModuleViewer::class)->AssignAjax('sTitleUpload',LS::Make(ModuleLang::class)->Get('settings_profile_photo_upload'));
	}
	/**
	 * Отмена ресайза фотки, необходимо удалить временный файл
	 */
	protected function EventCancelFoto() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Достаем из сессии файл и удаляем
		 */
		$sFile=LS::Make(ModuleSession::class)->Get('sFotoFileTmp');
		LS::Make(ModuleImage::class)->RemoveFile($sFile);

		$sFile=LS::Make(ModuleSession::class)->Get('sFotoFilePreviewTmp');
		LS::Make(ModuleImage::class)->RemoveFile($sFile);
		/**
		 * Удаляем из сессии
		 */
		LS::Make(ModuleSession::class)->Drop('sFotoFileTmp');
		LS::Make(ModuleSession::class)->Drop('sFotoFilePreviewTmp');
	}
	/**
	 * Загрузка временной картинки для аватара
	 */
	protected function EventUploadAvatar() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('jsonIframe',false);

		if(!isset($_FILES['avatar']['tmp_name'])) {
			return false;
		}
		/**
		 * Копируем загруженный файл
		 */
		$sFileTmp=Config::Get('sys.cache.dir').func_generator();
		if (!move_uploaded_file($_FILES['avatar']['tmp_name'],$sFileTmp)) {
			return false;
		}
		/**
		 * Ресайзим и сохраняем уменьшенную копию
		 */
		$sDir=Config::Get('path.uploads.images')."/tmp/avatars/{$this->oUserCurrent->getId()}";
		if ($sFileAvatar=LS::Make(ModuleImage::class)->Resize($sFileTmp,$sDir,'original',Config::Get('view.img_max_width'),Config::Get('view.img_max_height'),200,null,true)) {
			/**
			 * Зписываем в сессию
			 */
			LS::Make(ModuleSession::class)->Set('sAvatarFileTmp',$sFileAvatar);
			LS::Make(ModuleViewer::class)->AssignAjax('sTmpFile',LS::Make(ModuleImage::class)->GetWebPath($sFileAvatar));
		} else {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleImage::class)->GetLastError(),LS::Make(ModuleLang::class)->Get('error'));
		}
		unlink($sFileTmp);
	}
	/**
	 * Вырезает из временной аватарки область нужного размера, ту что задал пользователь
	 */
	protected function EventResizeAvatar() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Получаем файл из сессии
		 */
		$sFileAvatar=LS::Make(ModuleSession::class)->Get('sAvatarFileTmp');
		if (!file_exists($sFileAvatar)) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			return;
		}
		/**
		 * Получаем размер области из параметров
		 */
		$aSize=array();
		$aSizeTmp=getRequest('size');
		if (isset($aSizeTmp['x']) and is_numeric($aSizeTmp['x'])
			and isset($aSizeTmp['y']) and is_numeric($aSizeTmp['y'])
				and isset($aSizeTmp['x2']) and is_numeric($aSizeTmp['x2'])
					and isset($aSizeTmp['y2']) and is_numeric($aSizeTmp['y2'])) {
			$aSize=array('x1'=>$aSizeTmp['x'],'y1'=>$aSizeTmp['y'],'x2'=>$aSizeTmp['x2'],'y2'=>$aSizeTmp['y2']);
		}
		/**
		 * Вырезаем аватарку
		 */
		if ($sFileWeb=LS::Make(ModuleUser::class)->UploadAvatar($sFileAvatar,$this->oUserCurrent,$aSize)) {
			/**
			 * Удаляем старые аватарки
			 */
			if ($sFileWeb!=$this->oUserCurrent->getProfileAvatar()) {
				LS::Make(ModuleUser::class)->DeleteAvatar($this->oUserCurrent);
			}
			$this->oUserCurrent->setProfileAvatar($sFileWeb);

			LS::Make(ModuleUser::class)->Update($this->oUserCurrent);
			LS::Make(ModuleSession::class)->Drop('sAvatarFileTmp');
			LS::Make(ModuleViewer::class)->AssignAjax('sFile',$this->oUserCurrent->getProfileAvatarPath(100));
			LS::Make(ModuleViewer::class)->AssignAjax('sTitleUpload',LS::Make(ModuleLang::class)->Get('settings_profile_avatar_change'));
		} else {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_profile_avatar_error'),LS::Make(ModuleLang::class)->Get('error'));
		}
	}
	/**
	 * Удаляет аватар
	 */
	protected function EventRemoveAvatar() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Удаляем
		 */
		LS::Make(ModuleUser::class)->DeleteAvatar($this->oUserCurrent);
		$this->oUserCurrent->setProfileAvatar(null);
		LS::Make(ModuleUser::class)->Update($this->oUserCurrent);
		/**
		 * Возвращает дефолтную аватарку
		 */
		LS::Make(ModuleViewer::class)->AssignAjax('sFile',$this->oUserCurrent->getProfileAvatarPath(100));
		LS::Make(ModuleViewer::class)->AssignAjax('sTitleUpload',LS::Make(ModuleLang::class)->Get('settings_profile_avatar_upload'));
	}
	/**
	 * Отмена ресайза аватарки, необходимо удалить временный файл
	 */
	protected function EventCancelAvatar() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Достаем из сессии файл и удаляем
		 */
		$sFileAvatar=LS::Make(ModuleSession::class)->Get('sAvatarFileTmp');
		LS::Make(ModuleImage::class)->RemoveFile($sFileAvatar);
		LS::Make(ModuleSession::class)->Drop('sAvatarFileTmp');
	}
	/**
	 * Дополнительные настройки сайта
	 */
	protected function EventTuning() {
		$this->sMenuItemSelect='settings';
		$this->sMenuSubItemSelect='tuning';

		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('settings_menu_tuning'));
		$aTimezoneList=array('-12','-11','-10','-9.5','-9','-8','-7','-6','-5','-4.5','-4','-3.5','-3','-2','-1','0','1','2','3','3.5','4','4.5','5','5.5','5.75','6','6.5','7','8','8.75','9','9.5','10','10.5','11','11.5','12','12.75','13','14');
		LS::Make(ModuleViewer::class)->Assign('aTimezoneList',$aTimezoneList);
		/**
		 * Если отправили форму с настройками - сохраняем
		 */
		if (isPost('submit_settings_tuning')) {
			LS::Make(ModuleSecurity::class)->ValidateSendForm();

			if (in_array(getRequestStr('settings_general_timezone'),$aTimezoneList)) {
				$this->oUserCurrent->setSettingsTimezone(getRequestStr('settings_general_timezone'));
			}

			$this->oUserCurrent->setSettingsNoticeNewTopic( getRequest('settings_notice_new_topic') ? 1 : 0 );
			$this->oUserCurrent->setSettingsNoticeNewComment( getRequest('settings_notice_new_comment') ? 1 : 0 );
			$this->oUserCurrent->setSettingsNoticeNewTalk( getRequest('settings_notice_new_talk') ? 1 : 0 );
			$this->oUserCurrent->setSettingsNoticeReplyComment( getRequest('settings_notice_reply_comment') ? 1 : 0 );
			$this->oUserCurrent->setSettingsNoticeNewFriend( getRequest('settings_notice_new_friend') ? 1 : 0 );
			$this->oUserCurrent->setProfileDate(date("Y-m-d H:i:s"));
			/**
			 * Запускаем выполнение хуков
			 */
			LS::Make(ModuleHook::class)->Run('settings_tuning_save_before', array('oUser'=>$this->oUserCurrent));
			if (LS::Make(ModuleUser::class)->Update($this->oUserCurrent)) {
				LS::Make(ModuleMessage::class)->AddNoticeSingle(LS::Make(ModuleLang::class)->Get('settings_tuning_submit_ok'));
				LS::Make(ModuleHook::class)->Run('settings_tuning_save_after', array('oUser'=>$this->oUserCurrent));
			} else {
				LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			}
		} else {
			if (is_null($this->oUserCurrent->getSettingsTimezone())) {
				$_REQUEST['settings_general_timezone']=(strtotime(date("Y-m-d H:i:s"))-strtotime(gmdate("Y-m-d H:i:s")))/3600 - date('I');
			} else {
				$_REQUEST['settings_general_timezone']=$this->oUserCurrent->getSettingsTimezone();
			}
		}
	}
	/**
	 * Показ и обработка формы приглаешний
	 *
	 */
	protected function EventInvite() {
		/**
		 * Только при активном режиме инвайтов
		 */
		if (!Config::Get('general.reg.invite')) {
			return parent::EventNotFound();
		}

		$this->sMenuItemSelect='invite';
		$this->sMenuSubItemSelect='';
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('settings_menu_invite'));
		/**
		 * Если отправили форму
		 */
		if (isPost('submit_invite')) {
			LS::Make(ModuleSecurity::class)->ValidateSendForm();

			$bError=false;
			/**
			 * Есть права на отправку инфайтов?
			 */
			if (!LS::Make(ModuleACL::class)->CanSendInvite($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_invite_available_no'),LS::Make(ModuleLang::class)->Get('error'));
				$bError=true;
			}
			/**
			 * Емайл корректен?
			 */
			if (!func_check(getRequestStr('invite_mail'),'mail')) {
				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_invite_mail_error'),LS::Make(ModuleLang::class)->Get('error'));
				$bError=true;
			}
			/**
			 * Запускаем выполнение хуков
			 */
			LS::Make(ModuleHook::class)->Run('settings_invate_send_before', array('oUser'=>$this->oUserCurrent));
			/**
			 * Если нет ошибок, то отправляем инвайт
			 */
			if (!$bError) {
				$oInvite=LS::Make(ModuleUser::class)->GenerateInvite($this->oUserCurrent);
				LS::Make(ModuleNotify::class)->SendInvite($this->oUserCurrent,getRequestStr('invite_mail'),$oInvite);
				LS::Make(ModuleMessage::class)->AddNoticeSingle(LS::Make(ModuleLang::class)->Get('settings_invite_submit_ok'));
				LS::Make(ModuleHook::class)->Run('settings_invate_send_after', array('oUser'=>$this->oUserCurrent));
			}
		}

		LS::Make(ModuleViewer::class)->Assign('iCountInviteAvailable',LS::Make(ModuleUser::class)->GetCountInviteAvailable($this->oUserCurrent));
		LS::Make(ModuleViewer::class)->Assign('iCountInviteUsed',LS::Make(ModuleUser::class)->GetCountInviteUsed($this->oUserCurrent->getId()));
	}
	/**
	 * Форма смены пароля, емайла
	 */
	protected function EventAccount() {
		/**
		 * Устанавливаем title страницы
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('settings_menu_profile'));
		$this->sMenuSubItemSelect='account';
		/**
		 * Если нажали кнопку "Сохранить"
		 */
		if (isPost('submit_account_edit')) {
			LS::Make(ModuleSecurity::class)->ValidateSendForm();

			$bError=false;
			/**
			 * Проверка мыла
			 */
			if (func_check(getRequestStr('mail'),'mail')) {
				if ($oUserMail=LS::Make(ModuleUser::class)->GetUserByMail(getRequestStr('mail')) and $oUserMail->getId()!=$this->oUserCurrent->getId()) {
					LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_profile_mail_error_used'),LS::Make(ModuleLang::class)->Get('error'));
					$bError=true;
				}
			} else {
				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_profile_mail_error'),LS::Make(ModuleLang::class)->Get('error'));
				$bError=true;
			}
			/**
			 * Проверка на смену пароля
			 */
			if (getRequestStr('password','')!='') {
				if (func_check(getRequestStr('password'),'password',5)) {
					if (getRequestStr('password')==getRequestStr('password_confirm')) {
						if (LS::Make(ModuleCrypto::class)->PasswordVerify(getRequestStr('password_now'), $this->oUserCurrent->getPassword())) {
							$this->oUserCurrent->setPassword(LS::Make(ModuleCrypto::class)->PasswordHash(getRequestStr('password')));
                            LS::Make(ModuleUser::class)->_Authorization($this->oUserCurrent);
						} else {
							$bError=true;
							LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_profile_password_current_error'),LS::Make(ModuleLang::class)->Get('error'));
						}
					} else {
						$bError=true;
						LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_profile_password_confirm_error'),LS::Make(ModuleLang::class)->Get('error'));
					}
				} else {
					$bError=true;
					LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('settings_profile_password_new_error'),LS::Make(ModuleLang::class)->Get('error'));
				}
			}
			/**
			 * Ставим дату последнего изменения
			 */
			$this->oUserCurrent->setProfileDate(date("Y-m-d H:i:s"));
			/**
			 * Запускаем выполнение хуков
			 */
			LS::Make(ModuleHook::class)->Run('settings_account_save_before', array('oUser'=>$this->oUserCurrent,'bError'=>&$bError));
			/**
			 * Сохраняем изменения
			 */
			if (!$bError) {
				if (LS::Make(ModuleUser::class)->Update($this->oUserCurrent)) {
					LS::Make(ModuleMessage::class)->AddNoticeSingle(LS::Make(ModuleLang::class)->Get('settings_account_submit_ok'));
					/**
					 * Подтверждение смены емайла
					 */
					if (getRequestStr('mail') and getRequestStr('mail')!=$this->oUserCurrent->getMail()) {
						if ($oChangemail=LS::Make(ModuleUser::class)->MakeUserChangemail($this->oUserCurrent,getRequestStr('mail'))) {
							if ($oChangemail->getMailFrom()) {
								LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('settings_profile_mail_change_from_notice'));
							} else {
								LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('settings_profile_mail_change_to_notice'));
							}
						}
					}

					LS::Make(ModuleHook::class)->Run('settings_account_save_after', array('oUser'=>$this->oUserCurrent));
				} else {
					LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
				}
			}
		}
	}
	/**
	 * Выводит форму для редактирования профиля и обрабатывает её
	 *
	 */
	protected function EventProfile() {
		/**
		 * Устанавливаем title страницы
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('settings_menu_profile'));
		LS::Make(ModuleViewer::class)->Assign('aUserFields',LS::Make(ModuleUser::class)->getUserFields(''));
		LS::Make(ModuleViewer::class)->Assign('aUserFieldsContact',LS::Make(ModuleUser::class)->getUserFields(array('contact','social')));
		/**
		 * Загружаем в шаблон JS текстовки
		 */
		LS::Make(ModuleLang::class)->AddLangJs(array(
								  'settings_profile_field_error_max'
							  ));
		/**
		 * Если нажали кнопку "Сохранить"
		 */
		if (isPost('submit_profile_edit')) {
			LS::Make(ModuleSecurity::class)->ValidateSendForm();

			$bError=false;
			/**
			 * Заполняем профиль из полей формы
			 */
			/**
			 * Определяем гео-объект
			 */
			if (getRequest('geo_city')) {
				$oGeoObject=LS::Make(ModuleGeo::class)->GetGeoObject('city',getRequestStr('geo_city'));
			} elseif (getRequest('geo_region')) {
				$oGeoObject=LS::Make(ModuleGeo::class)->GetGeoObject('region',getRequestStr('geo_region'));
			} elseif (getRequest('geo_country')) {
				$oGeoObject=LS::Make(ModuleGeo::class)->GetGeoObject('country',getRequestStr('geo_country'));
			} else {
				$oGeoObject=null;
			}
			/**
			 * Проверяем имя
			 */
			if (func_check(getRequestStr('profile_name'),'text',2,Config::Get('module.user.name_max'))) {
				$this->oUserCurrent->setProfileName(getRequestStr('profile_name'));
			} else {
				$this->oUserCurrent->setProfileName(null);
			}
			/**
			 * Проверяем пол
			 */
			if (in_array(getRequestStr('profile_sex'),array('man','woman','other'))) {
				$this->oUserCurrent->setProfileSex(getRequestStr('profile_sex'));
			} else {
				$this->oUserCurrent->setProfileSex('other');
			}
			/**
			 * Проверяем дату рождения
			 */
			if (func_check(getRequestStr('profile_birthday_day'),'id',1,2) and func_check(getRequestStr('profile_birthday_month'),'id',1,2) and func_check(getRequestStr('profile_birthday_year'),'id',4,4)) {
				$this->oUserCurrent->setProfileBirthday(date("Y-m-d H:i:s",mktime(0,0,0,getRequestStr('profile_birthday_month'),getRequestStr('profile_birthday_day'),getRequestStr('profile_birthday_year'))));
			} else {
				$this->oUserCurrent->setProfileBirthday(null);
			}
			/**
			 * Проверяем информацию о себе
			 */
			if (func_check(getRequestStr('profile_about'),'text',1,3000)) {
				$this->oUserCurrent->setProfileAbout(LS::Make(ModuleText::class)->Parser(getRequestStr('profile_about')));
			} else {
				$this->oUserCurrent->setProfileAbout(null);
			}
			/**
			 * Ставим дату последнего изменения профиля
			 */
			$this->oUserCurrent->setProfileDate(date("Y-m-d H:i:s"));
			/**
			 * Запускаем выполнение хуков
			 */
			LS::Make(ModuleHook::class)->Run('settings_profile_save_before', array('oUser'=>$this->oUserCurrent,'bError'=>&$bError));
			/**
			 * Сохраняем изменения профиля
			 */
			if (!$bError) {
				if (LS::Make(ModuleUser::class)->Update($this->oUserCurrent)) {
					/**
					 * Создаем связь с гео-объектом
					 */
					if ($oGeoObject) {
						LS::Make(ModuleGeo::class)->CreateTarget($oGeoObject,'user',$this->oUserCurrent->getId());
						if ($oCountry=$oGeoObject->getCountry()) {
							$this->oUserCurrent->setProfileCountry($oCountry->getName());
						} else {
							$this->oUserCurrent->setProfileCountry(null);
						}
						if ($oRegion=$oGeoObject->getRegion()) {
							$this->oUserCurrent->setProfileRegion($oRegion->getName());
						} else {
							$this->oUserCurrent->setProfileRegion(null);
						}
						if ($oCity=$oGeoObject->getCity()) {
							$this->oUserCurrent->setProfileCity($oCity->getName());
						} else {
							$this->oUserCurrent->setProfileCity(null);
						}
					} else {
						LS::Make(ModuleGeo::class)->DeleteTargetsByTarget('user',$this->oUserCurrent->getId());
						$this->oUserCurrent->setProfileCountry(null);
						$this->oUserCurrent->setProfileRegion(null);
						$this->oUserCurrent->setProfileCity(null);
					}
					LS::Make(ModuleUser::class)->Update($this->oUserCurrent);

					/**
					 * Обрабатываем дополнительные поля, type = ''
					 */
					$aFields = LS::Make(ModuleUser::class)->getUserFields('');
					$aData = array();
					foreach ($aFields as $iId => $aField) {
						if (isset($_REQUEST['profile_user_field_'.$iId])) {
							$aData[$iId] = getRequestStr('profile_user_field_'.$iId);
						}
					}
					LS::Make(ModuleUser::class)->setUserFieldsValues($this->oUserCurrent->getId(), $aData);
					/**
					 * Динамические поля контактов, type = array('contact','social')
					 */
					$aType=array('contact','social');
					$aFields = LS::Make(ModuleUser::class)->getUserFields($aType);
					/**
					 * Удаляем все поля с этим типом
					 */
					LS::Make(ModuleUser::class)->DeleteUserFieldValues($this->oUserCurrent->getId(),$aType);
					$aFieldsContactType=getRequest('profile_user_field_type');
					$aFieldsContactValue=getRequest('profile_user_field_value');
					if (is_array($aFieldsContactType)) {
						foreach($aFieldsContactType as $k=>$v) {
							$v=(string)$v;
							if (isset($aFields[$v]) and isset($aFieldsContactValue[$k]) and is_string($aFieldsContactValue[$k])) {
								LS::Make(ModuleUser::class)->setUserFieldsValues($this->oUserCurrent->getId(), array($v=>$aFieldsContactValue[$k]), Config::Get('module.user.userfield_max_identical'));
							}
						}
					}
					LS::Make(ModuleMessage::class)->AddNoticeSingle(LS::Make(ModuleLang::class)->Get('settings_profile_submit_ok'));
					LS::Make(ModuleHook::class)->Run('settings_profile_save_after', array('oUser'=>$this->oUserCurrent));
				} else {
					LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
				}
			}
		}
		/**
		 * Загружаем гео-объект привязки
		 */
		$oGeoTarget=LS::Make(ModuleGeo::class)->GetTargetByTarget('user',$this->oUserCurrent->getId());
		LS::Make(ModuleViewer::class)->Assign('oGeoTarget',$oGeoTarget);
		/**
		 * Загружаем в шаблон список стран, регионов, городов
		 */
		$aCountries=LS::Make(ModuleGeo::class)->GetCountries(array(),array('sort'=>'asc'),1,300);
		LS::Make(ModuleViewer::class)->Assign('aGeoCountries',$aCountries['collection']);
		if ($oGeoTarget) {
			if ($oGeoTarget->getCountryId()) {
				$aRegions=LS::Make(ModuleGeo::class)->GetRegions(array('country_id'=>$oGeoTarget->getCountryId()),array('sort'=>'asc'),1,500);
				LS::Make(ModuleViewer::class)->Assign('aGeoRegions',$aRegions['collection']);
			}
			if ($oGeoTarget->getRegionId()) {
				$aCities=LS::Make(ModuleGeo::class)->GetCities(array('region_id'=>$oGeoTarget->getRegionId()),array('sort'=>'asc'),1,500);
				LS::Make(ModuleViewer::class)->Assign('aGeoCities',$aCities['collection']);
			}
		}

	}
	
	protected function EventBehavior() {
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('settings_menu_behavior'));
		$this->sMenuSubItemSelect='behavior';
	}
	/**
	 * Выполняется при завершении работы экшена
	 *
	 */
	public function EventShutdown() {
		$iCountTopicFavourite=LS::Make(ModuleTopic::class)->GetCountTopicsFavouriteByUserId($this->oUserCurrent->getId());
		$iCountTopicUser=LS::Make(ModuleTopic::class)->GetCountTopicsPersonalByUser($this->oUserCurrent->getId(),1);
		$iCountCommentUser=LS::Make(ModuleComment::class)->GetCountCommentsByUserId($this->oUserCurrent->getId(),'topic');
		$iCountCommentFavourite=LS::Make(ModuleComment::class)->GetCountCommentsFavouriteByUserId($this->oUserCurrent->getId());
		$iCountNoteUser=LS::Make(ModuleUser::class)->GetCountUserNotesByUserId($this->oUserCurrent->getId());

		LS::Make(ModuleViewer::class)->Assign('oUserProfile',$this->oUserCurrent);
		LS::Make(ModuleViewer::class)->Assign('iCountWallUser',LS::Make(ModuleWall::class)->GetCountWall(array('wall_user_id'=>$this->oUserCurrent->getId(),'pid'=>null)));
		/**
		 * Общее число публикация и избранного
		 */
		LS::Make(ModuleViewer::class)->Assign('iCountCreated',$iCountNoteUser+$iCountTopicUser+$iCountCommentUser);
		LS::Make(ModuleViewer::class)->Assign('iCountFavourite',$iCountCommentFavourite+$iCountTopicFavourite);
		LS::Make(ModuleViewer::class)->Assign('iCountFriendsUser',LS::Make(ModuleUser::class)->GetCountUsersFriend($this->oUserCurrent->getId()));

		/**
		 * Загружаем в шаблон необходимые переменные
		 */
		LS::Make(ModuleViewer::class)->Assign('sMenuItemSelect',$this->sMenuItemSelect);
		LS::Make(ModuleViewer::class)->Assign('sMenuSubItemSelect',$this->sMenuSubItemSelect);

		LS::Make(ModuleHook::class)->Run('action_shutdown_settings');
	}
}
