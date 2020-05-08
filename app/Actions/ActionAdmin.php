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

use App\Modules\Blog\ModuleBlog;
use App\Modules\Comment\ModuleComment;
use App\Modules\Topic\ModuleTopic;
use App\Modules\User\Entity\ModuleUser_EntityField;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Cache\ModuleCache;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Security\ModuleSecurity;
use Engine\Modules\Viewer\ModuleViewer;

/**
 * Экшен обработки УРЛа вида /admin/
 *
 * @package actions
 * @since 1.0
 */
class ActionAdmin extends Action {
	/**
	 * Текущий пользователь
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent=null;
	/**
	 * Главное меню
	 *
	 * @var string
	 */
	protected $sMenuHeadItemSelect='admin';

	/**
	 * Инициализация
	 */
	public function Init() {
		/**
		 * Если нет прав доступа - перекидываем на 404 страницу
		 */
        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);
		if(!$user->IsAuthorization() or !$oUserCurrent=$user->GetUserCurrent() or !$oUserCurrent->isAdministrator()) {
			parent::EventNotFound();
		} else {
            $this->SetDefaultEvent('index');

            $this->oUserCurrent = $oUserCurrent;
        }
	}
	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent() {
		$this->AddEvent('index','EventIndex');
		$this->AddEvent('restorecomment','EventRestoreComment');
		$this->AddEvent('userfields','EventUserfields');
		$this->AddEvent('recalcfavourite','EventRecalculateFavourite');
		$this->AddEvent('recalcvote','EventRecalculateVote');
		$this->AddEvent('recalctopic','EventRecalculateTopic');
        $this->AddEvent('jsonconfiglocal','EventJsonConfigLocal');
        $this->AddEvent('config','EventAdminConfig');
        $this->AddEvent('save','EventAdminConfigSave');
        $this->AddEvent('user','EventSaveUser');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

    protected function EventUsers() {
    }

    protected function EventSaveUser() {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);
        if (!$oUser = $user->GetUserById(getRequest('user_id'))) {
            return;
        }
        $sRank = getRequest('user_rank');
        $sMail = getRequest('user_mail');
        $sLogin = getRequest('user_login');
        $oUser->setRank($sRank);
        $oUser->setMail($sMail);
        $oUser->setLogin($sLogin);
        $iPrivs = 0;
        if(getRequest('user_privileges_moderator') == 'on') {
        	$iPrivs |= ModuleUser::USER_PRIV_MODERATOR;
		}
        if(getRequest('user_privileges_quotes') == 'on') {
        	$iPrivs |= ModuleUser::USER_PRIV_QUOTES;
		}
        $user->SetUserPrivileges($oUser->getId(), $iPrivs);
        $user->Update($oUser);
    }

    protected function EventAdminConfigSave() {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        $values = getRequest('values');
        //TODO
        Config::LoadFromFile(dirname(__FILE__)."/../../config/local.config.json",true,'adminsave');
        foreach ($values as $key=>$val) {
            if ($val=="1") {
                $val = true;
            }
            if ($val=="0") {
                $val = false;
            }
            Config::Set($key, $val,'adminsave');
        }
        file_put_contents(dirname(__FILE__)."/../../config/local.config.json",json_encode(Config::getInstance('adminsave')->aConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

	protected function EventAdminConfig() {
	    $params = [
	        "sep1" => ["type"=>"separator", "description"=>"Настройки сайта"],
            "general.close" => [
                "type" => "bool",
                "description" => "Закрытый режим работы сайта",
            ],
            "general.reg.invite" => [
                "type" => "bool",
                "description" => "Регистрация по инвайтам",
            ],
            "general.reg.activation" => [
                "type" => "bool",
                "description" => "Активация по почте",
            ],
            "view.name" => [
                "type" => "string",
                "description" => "Название сайта",
            ],
            "path.root.web" => [
                "type" => "string",
                "description" => "URL сайта",
            ],
            "sep2" => ["type"=>"separator", "description"=>"Картинки"],
            "module.image.use_anon" => [
                "type" => "bool",
                "description" => "Использовать анонимайзер при загрузке изображений",
            ],
            "sep" => ["type"=>"separator", "description"=>"БД"],
            "db.params.host" => [
                "type" => "string",
                "description" => "Хост БД",
            ],
            "db.params.port" => [
                "type" => "int",
                "description" => "Порт БД",
            ],
            "db.params.user" => [
                "type" => "string",
                "description" => "Пользователь БД",
            ],
            "db.params.pass" => [
                "type" => "password",
                "description" => "Пароль БД",
            ],
            "db.params.type" => [
                "type" => "string",
                "description" => "Тип БД",
            ],
            "db.params.dbname" => [
                "type" => "string",
                "description" => "Название базы",
            ],
            "sep3" => ["type"=>"separator", "description"=>"Модераторы"],
            "moderator" => [
                "type" => "list",
                "description" => "Модераторы",
            ],
            "sep4" => ["type"=>"separator", "description"=>"Почтовик"],
            "sys.mail.from_email" => [
                "type" => "string",
                "description" => "Адрес для почтовика",
            ],
            "sys.mail.from_name" => [
                "type" => "string",
                "description" => "Имя отправителя в почтовике",
            ],
            "sep5" => ["type"=>"separator", "description"=>"Комментарии"],
            "module.comment.bad" => [
                "type" => "int",
                "description" => "Порог скрытия комментария",
            ],
        ];
	    LS::Make(ModuleViewer::class)->Assign("aConfig", $params);
    }

	/**
	 * Отображение главной страницы админки
	 * Нет никакой логики, просто отображение дефолтного шаблона евента index.tpl
	 */
	protected function EventIndex() {

	}
	
	protected function EventJsonConfigLocal() {
		$config = array();
		//TODO:
		require("/var/www/ponydays-dev/config/config.local.php");
		LS::Make(ModuleViewer::class)->Assign('config',json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
	/**
	 * Перестроение дерева комментариев, актуально при $config['module']['comment']['use_nested'] = true;
	 *
	 */
	protected function EventRestoreComment() {
		LS::Make(ModuleSecurity::class)->ValidateSendForm();
		set_time_limit(0);
		LS::Make(ModuleComment::class)->RestoreTree();
		LS::Make(ModuleCache::class)->Clean();

		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('admin_comment_restore_tree'),LS::Make(ModuleLang::class)->Get('attention'));
		$this->SetTemplateAction('index');
	}
	/**
	 * Пересчет счетчика избранных
	 *
	 */
	protected function EventRecalculateFavourite() {
        LS::Make(ModuleSecurity::class)->ValidateSendForm();
		set_time_limit(0);
        LS::Make(ModuleComment::class)->RecalculateFavourite();
        LS::Make(ModuleTopic::class)->RecalculateFavourite();
        LS::Make(ModuleCache::class)->Clean();

        LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('admin_favourites_recalculated'),LS::Make(ModuleLang::class)->Get('attention'));
		$this->SetTemplateAction('index');
	}
	/**
	 * Пересчет счетчика голосований
	 */
	protected function EventRecalculateVote() {
        LS::Make(ModuleSecurity::class)->ValidateSendForm();
		set_time_limit(0);
        LS::Make(ModuleTopic::class)->RecalculateVote();
        LS::Make(ModuleCache::class)->Clean();

        LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('admin_votes_recalculated'),LS::Make(ModuleLang::class)->Get('attention'));
		$this->SetTemplateAction('index');
	}
	/**
	 * Пересчет количества топиков в блогах
	 */
	protected function EventRecalculateTopic() {
        LS::Make(ModuleSecurity::class)->ValidateSendForm();
		set_time_limit(0);
        LS::Make(ModuleBlog::class)->RecalculateCountTopic();
        LS::Make(ModuleCache::class)->Clean();

        LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('admin_topics_recalculated'),LS::Make(ModuleLang::class)->Get('attention'));
		$this->SetTemplateAction('index');
	}
	/**
	 * Управление полями пользователя
	 *
	 */
	protected function EventUserFields()
	{
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);
        /** @var ModuleMessage $message */
        $message = LS::Make(ModuleMessage::class);
        /** @var ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
		switch(getRequestStr('action')) {
			/**
			 * Создание нового поля
			 */
			case 'add':
				/**
				 * Обрабатываем как ajax запрос (json)
				 */
				$viewer->SetResponseAjax('json');
				if (!$this->checkUserField()) {
					return;
				}
				$oField = new ModuleUser_EntityField();
				$oField->setName(getRequestStr('name'));
				$oField->setTitle(getRequestStr('title'));
				$oField->setPattern(getRequestStr('pattern'));
				if (in_array(getRequestStr('type'),$user->GetUserFieldTypes())) {
					$oField->setType(getRequestStr('type'));
				} else {
					$oField->setType('');
				}

				$iId = $user->addUserField($oField);
				if(!$iId) {
					$message->AddError($lang->Get('system_error'),$lang->Get('error'));
					return;
				}
				/**
				 * Прогружаем переменные в ajax ответ
				 */
				$viewer->AssignAjax('id', $iId);
				$viewer->AssignAjax('lang_delete', $lang->Get('user_field_delete'));
				$viewer->AssignAjax('lang_edit', $lang->Get('user_field_update'));
				$message->AddNotice($lang->Get('user_field_added'),$lang->Get('attention'));
				break;
			/**
			 * Удаление поля
			 */
			case 'delete':
				/**
				 * Обрабатываем как ajax запрос (json)
				 */
				$viewer->SetResponseAjax('json');
				if (!getRequestStr('id')) {
					$message->AddError($lang->Get('system_error'),$lang->Get('error'));
					return;
				}
				$user->deleteUserField(getRequestStr('id'));
				$message->AddNotice($lang->Get('user_field_deleted'),$lang->Get('attention'));
				break;
			/**
			 * Изменение поля
			 */
			case 'update':
				/**
				 * Обрабатываем как ajax запрос (json)
				 */
				$viewer->SetResponseAjax('json');
				if (!getRequestStr('id')) {
					$message->AddError($lang->Get('system_error'),$lang->Get('error'));
					return;
				}
				if (!$user->userFieldExistsById(getRequestStr('id'))) {
					$message->AddError($lang->Get('system_error'),$lang->Get('error'));
					return;
				}
				if (!$this->checkUserField()) {
					return;
				}
				$oField = new ModuleUser_EntityField;
				$oField->setId(getRequestStr('id'));
				$oField->setName(getRequestStr('name'));
				$oField->setTitle(getRequestStr('title'));
				$oField->setPattern(getRequestStr('pattern'));
				if (in_array(getRequestStr('type'),$user->GetUserFieldTypes())) {
					$oField->setType(getRequestStr('type'));
				} else {
					$oField->setType('');
				}

				if ($user->updateUserField($oField)) {
					$message->AddError($lang->Get('system_error'),$lang->Get('error'));
					return;
				}
				$message->AddNotice($lang->Get('user_field_updated'),$lang->Get('attention'));
				break;
			/**
			 * Показываем страницу со списком полей
			 */
			default:
				/**
				 * Загружаем в шаблон JS текстовки
				 */
				$lang->AddLangJs(array('user_field_delete_confirm'));
				/**
				 * Получаем список всех полей
				 */
				$viewer->Assign('aUserFields',$user->getUserFields());
				$viewer->Assign('aUserFieldTypes',$user->GetUserFieldTypes());
				$this->SetTemplateAction('user_fields');
		}
	}
	/**
	 * Проверка поля пользователя на корректность из реквеста
	 *
	 * @return bool
	 */
	public function checkUserField()
	{
        /** @var ModuleMessage $message */
        $message = LS::Make(ModuleMessage::class);
        /** @var ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
		if (!getRequestStr('title')) {
			$message->AddError($lang->Get('user_field_error_add_no_title'),$lang->Get('error'));
			return false;
		}
		if (!getRequestStr('name')) {
			$message->AddError($lang->Get('user_field_error_add_no_name'),$lang->Get('error'));
			return false;
		}
		/**
		 * Не допускаем дубликатов по имени
		 */
		if (LS::Make(ModuleUser::class)->userFieldExistsByName(getRequestStr('name'), getRequestStr('id'))) {
			$message->AddError($lang->Get('user_field_error_name_exists'),$lang->Get('error'));
			return false;
		}
		return true;
	}
	/**
	 * Выполняется при завершении работы экшена
	 *
	 */
	public function EventShutdown() {
		/**
		 * Загружаем в шаблон необходимые переменные
		 */
		LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect',$this->sMenuHeadItemSelect);
	}
}
