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
	 *
	 * @return string
	 */
	public function Init() {
		/**
		 * Если нет прав доступа - перекидываем на 404 страницу
		 */
		if(!$this->User_IsAuthorization() or !$oUserCurrent=$this->User_GetUserCurrent() or !$oUserCurrent->isAdministrator()) {
			return parent::EventNotFound();
		}
		$this->SetDefaultEvent('index');

		$this->oUserCurrent=$oUserCurrent;
	}
	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent() {
		$this->AddEvent('index','EventIndex');
		$this->AddEvent('plugins','EventPlugins');
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
        $this->Viewer_SetResponseAjax('json');
        if (!$oUser = $this->User_GetUserById(getRequest('user_id'))) {
            return false;
        }
        $sRank = getRequest('user_rank');
        $sMail = getRequest('user_mail');
        $oUser->setRank($sRank);
        $oUser->setMail($sMail);
        $this->User_Update($oUser);
    }

    protected function EventAdminConfigSave() {
        $this->Viewer_SetResponseAjax('json');
        $values = getRequest('values');
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
	    $this->Viewer_Assign("aConfig", $params);
    }

	/**
	 * Отображение главной страницы админки
	 * Нет никакой логики, просто отображение дефолтного шаблона евента index.tpl
	 */
	protected function EventIndex() {

	}
	
	protected function EventJsonConfigLocal() {
		$config = array();
		require("/var/www/ponydays-dev/config/config.local.php");
		$this->Viewer_Assign('config',json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
	/**
	 * Перестроение дерева комментариев, актуально при $config['module']['comment']['use_nested'] = true;
	 *
	 */
	protected function EventRestoreComment() {
		$this->Security_ValidateSendForm();
		set_time_limit(0);
		$this->Comment_RestoreTree();
		$this->Cache_Clean();

		$this->Message_AddNotice($this->Lang_Get('admin_comment_restore_tree'),$this->Lang_Get('attention'));
		$this->SetTemplateAction('index');
	}
	/**
	 * Пересчет счетчика избранных
	 *
	 */
	protected function EventRecalculateFavourite() {
		$this->Security_ValidateSendForm();
		set_time_limit(0);
		$this->Comment_RecalculateFavourite();
		$this->Topic_RecalculateFavourite();
		$this->Cache_Clean();

		$this->Message_AddNotice($this->Lang_Get('admin_favourites_recalculated'),$this->Lang_Get('attention'));
		$this->SetTemplateAction('index');
	}
	/**
	 * Пересчет счетчика голосований
	 */
	protected function EventRecalculateVote() {
		$this->Security_ValidateSendForm();
		set_time_limit(0);
		$this->Topic_RecalculateVote();
		$this->Cache_Clean();

		$this->Message_AddNotice($this->Lang_Get('admin_votes_recalculated'),$this->Lang_Get('attention'));
		$this->SetTemplateAction('index');
	}
	/**
	 * Пересчет количества топиков в блогах
	 */
	protected function EventRecalculateTopic() {
		$this->Security_ValidateSendForm();
		set_time_limit(0);
		$this->Blog_RecalculateCountTopic();
		$this->Cache_Clean();

		$this->Message_AddNotice($this->Lang_Get('admin_topics_recalculated'),$this->Lang_Get('attention'));
		$this->SetTemplateAction('index');
	}
	/**
	 * Страница со списком плагинов
	 *
	 */
	protected function EventPlugins() {
		$this->sMenuHeadItemSelect='plugins';
		/**
		 * Обработка удаления плагинов
		 */
		if (isPost('submit_plugins_del')) {
			$this->Security_ValidateSendForm();

			$aPluginsDelete=getRequest('plugin_del');
			if (is_array($aPluginsDelete)) {
				$this->Plugin_Delete(array_keys($aPluginsDelete));
			}
		}
		/**
		 * Получаем название плагина и действие
		 */
		if($sPlugin=getRequestStr('plugin',null,'get') and $sAction=getRequestStr('action',null,'get')) {
			return $this->SubmitManagePlugin($sPlugin,$sAction);
		}
		/**
		 * Получаем список блогов
		 */
		$aPlugins=$this->Plugin_GetList(array('order'=>'name'));
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign("aPlugins",$aPlugins);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugins_administartion_title'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('plugins');
	}
	/**
	 * Управление полями пользователя
	 *
	 */
	protected function EventUserFields()
	{
		switch(getRequestStr('action')) {
			/**
			 * Создание нового поля
			 */
			case 'add':
				/**
				 * Обрабатываем как ajax запрос (json)
				 */
				$this->Viewer_SetResponseAjax('json');
				if (!$this->checkUserField()) {
					return;
				}
				$oField = Engine::GetEntity('User_Field');
				$oField->setName(getRequestStr('name'));
				$oField->setTitle(getRequestStr('title'));
				$oField->setPattern(getRequestStr('pattern'));
				if (in_array(getRequestStr('type'),$this->User_GetUserFieldTypes())) {
					$oField->setType(getRequestStr('type'));
				} else {
					$oField->setType('');
				}

				$iId = $this->User_addUserField($oField);
				if(!$iId) {
					$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
					return;
				}
				/**
				 * Прогружаем переменные в ajax ответ
				 */
				$this->Viewer_AssignAjax('id', $iId);
				$this->Viewer_AssignAjax('lang_delete', $this->Lang_Get('user_field_delete'));
				$this->Viewer_AssignAjax('lang_edit', $this->Lang_Get('user_field_update'));
				$this->Message_AddNotice($this->Lang_Get('user_field_added'),$this->Lang_Get('attention'));
				break;
			/**
			 * Удаление поля
			 */
			case 'delete':
				/**
				 * Обрабатываем как ajax запрос (json)
				 */
				$this->Viewer_SetResponseAjax('json');
				if (!getRequestStr('id')) {
					$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
					return;
				}
				$this->User_deleteUserField(getRequestStr('id'));
				$this->Message_AddNotice($this->Lang_Get('user_field_deleted'),$this->Lang_Get('attention'));
				break;
			/**
			 * Изменение поля
			 */
			case 'update':
				/**
				 * Обрабатываем как ajax запрос (json)
				 */
				$this->Viewer_SetResponseAjax('json');
				if (!getRequestStr('id')) {
					$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
					return;
				}
				if (!$this->User_userFieldExistsById(getRequestStr('id'))) {
					$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
					return false;
				}
				if (!$this->checkUserField()) {
					return;
				}
				$oField = Engine::GetEntity('User_Field');
				$oField->setId(getRequestStr('id'));
				$oField->setName(getRequestStr('name'));
				$oField->setTitle(getRequestStr('title'));
				$oField->setPattern(getRequestStr('pattern'));
				if (in_array(getRequestStr('type'),$this->User_GetUserFieldTypes())) {
					$oField->setType(getRequestStr('type'));
				} else {
					$oField->setType('');
				}

				if ($this->User_updateUserField($oField)) {
					$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
					return;
				}
				$this->Message_AddNotice($this->Lang_Get('user_field_updated'),$this->Lang_Get('attention'));
				break;
			/**
			 * Показываем страницу со списком полей
			 */
			default:
				/**
				 * Загружаем в шаблон JS текстовки
				 */
				$this->Lang_AddLangJs(array('user_field_delete_confirm'));
				/**
				 * Получаем список всех полей
				 */
				$this->Viewer_Assign('aUserFields',$this->User_getUserFields());
				$this->Viewer_Assign('aUserFieldTypes',$this->User_GetUserFieldTypes());
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
		if (!getRequestStr('title')) {
			$this->Message_AddError($this->Lang_Get('user_field_error_add_no_title'),$this->Lang_Get('error'));
			return false;
		}
		if (!getRequestStr('name')) {
			$this->Message_AddError($this->Lang_Get('user_field_error_add_no_name'),$this->Lang_Get('error'));
			return false;
		}
		/**
		 * Не допускаем дубликатов по имени
		 */
		if ($this->User_userFieldExistsByName(getRequestStr('name'), getRequestStr('id'))) {
			$this->Message_AddError($this->Lang_Get('user_field_error_name_exists'),$this->Lang_Get('error'));
			return false;
		}
		return true;
	}
	/**
	 * Активация\деактивация плагина
	 *
	 * @param string $sPlugin	Имя плагина
	 * @param string $sAction	Действие
	 */
	protected function SubmitManagePlugin($sPlugin,$sAction) {
		$this->Security_ValidateSendForm();
		if(!in_array($sAction,array('activate','deactivate'))) {
			$this->Message_AddError($this->Lang_Get('plugins_unknown_action'),$this->Lang_Get('error'),true);
			Router::Location(Router::GetPath('plugins'));
		}
		/**
		 * Активируем\деактивируем плагин
		 */
		if($bResult=$this->Plugin_Toggle($sPlugin,$sAction)) {
			$this->Message_AddNotice($this->Lang_Get('plugins_action_ok'),$this->Lang_Get('attention'),true);
		} else {
			if(!($aMessages=$this->Message_GetErrorSession()) or !count($aMessages)) $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'),true);
		}
		/**
		 * Возвращаем на страницу управления плагинами
		 */
		Router::Location(Router::GetPath('admin').'plugins/');
	}
	/**
	 * Выполняется при завершении работы экшена
	 *
	 */
	public function EventShutdown() {
		/**
		 * Загружаем в шаблон необходимые переменные
		 */
		$this->Viewer_Assign('sMenuHeadItemSelect',$this->sMenuHeadItemSelect);
	}
}
?>
