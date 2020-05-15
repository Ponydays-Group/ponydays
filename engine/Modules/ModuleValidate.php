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

namespace Engine\Modules;

use Engine\LsObject;
use Engine\Module;
use Engine\Modules\Validate\ValidatorBoolean;
use Engine\Modules\Validate\ValidatorCaptcha;
use Engine\Modules\Validate\ValidatorCompare;
use Engine\Modules\Validate\ValidatorDate;
use Engine\Modules\Validate\ValidatorEmail;
use Engine\Modules\Validate\ValidatorInline;
use Engine\Modules\Validate\ValidatorNumber;
use Engine\Modules\Validate\ValidatorRegexp;
use Engine\Modules\Validate\ValidatorRequired;
use Engine\Modules\Validate\ValidatorString;
use Engine\Modules\Validate\ValidatorTags;
use Engine\Modules\Validate\ValidatorType;
use Engine\Modules\Validate\ValidatorUrl;

/**
 * Модуль Validate
 * Выполняет валидацию данных по определенным правилам. Поддерживает как обычную валидацию данных:
 * <pre>
 * if (!LS::Make(ModuleValidate::class)->Validate('url','http://livestreet.ru')) {
 * 	var_dump(LS::Make(ModuleValidate::class)->GetErrors());
 * }
 * </pre>
 * так и валидацию данных сущности:
 * <pre>
 * class PluginTest_ModuleMain_EntityTest extends Entity {
 *	// Определяем правила валидации
 * 	protected $aValidateRules=array(
 * 		array('login, name','string','max'=>7,'min'=>'3'),
 * 		array('title','my','on'=>'register'),
 * 	);
 *
 * 	public function ValidateMy($sValue,$aParams) {
 * 		if ($sValue!='Мега заголовок') {
 * 			return 'Ошибочный заголовок';
 * 		}
 * 		return true;
 * 	}
 * }
 *
 * // Валидация
 * $oObject=Engine::GetEntity('PluginTest_ModuleMain_EntityTest');
 * $oObject->setLogin('bolshoi login');
 * $oObject->setTitle('zagolovok');
 *
 * if ($oObject->_Validate()) {
 * 	var_dump("OK");
 * } else {
 * 	var_dump($oObject->_getValidateErrors());
 * }
 * </pre>
 *
 * @package engine.modules.validate
 * @since 1.0
 */
class ModuleValidate extends Module {
	/**
	 * Список ошибок при валидации, заполняется только если использовать валидацию напрямую без сущности
	 *
	 * @var array
	 */
	protected $aErrors=array();

	protected static $validatorClasses = [
        'boolean' => ValidatorBoolean::class,
        'captcha' => ValidatorCaptcha::class,
        'compare' => ValidatorCompare::class,
        'date'    => ValidatorDate::class,
        'email'   => ValidatorEmail::class,
        'inline'  => ValidatorInline::class,
        'number'  => ValidatorNumber::class,
        'regexp'  => ValidatorRegexp::class,
        'required'=> ValidatorRequired::class,
        'string'  => ValidatorString::class,
        'tags'    => ValidatorTags::class,
        'type'    => ValidatorType::class,
        'url'     => ValidatorUrl::class
    ];
	/**
	 * Инициализируем модуль
	 *
	 */
	public function Init() {

	}
	/**
	 * Запускает валидацию данных
	 *
	 * @param string $sNameValidator	Имя валидатора или метода при использовании параметра $oObject
	 * @param mixed $mValue	Валидируемое значение
	 * @param array $aParams	Параметры валидации
	 * @param null $oObject		Объект в котором необходимо вызвать метод валидации
	 *
	 * @return bool
	 */
	public function Validate($sNameValidator,$mValue,$aParams=array(),$oObject=null) {
		if (is_null($oObject)) {
			$oObject=$this;
		}
		$oValidator=$this->CreateValidator($sNameValidator,$oObject,null,$aParams);

		if (($sMsg=$oValidator->validate($mValue))!==true) {
			$sMsg=str_replace('%%field%%',is_null($oValidator->label) ? '' : $oValidator->label,$sMsg);
			$this->AddError($sMsg);
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Создает и возвращает объект валидатора
	 *
	 * @param string $sName	Имя валидатора или метода при использовании параметра $oObject
	 * @param LsObject $oObject	Объект в котором необходимо вызвать метод валидации
	 * @param null|array $aFields	Список полей сущности для которых необходимо провести валидацию
	 * @param array $aParams	Параметры
	 *
	 * @return mixed
	 */
	public function CreateValidator($sName,$oObject,$aFields=null,$aParams=array()) {
		if(is_string($aFields)) {
			$aFields=preg_split('/[\s,]+/',$aFields,-1,PREG_SPLIT_NO_EMPTY);
		}
		/**
		 * Определяем список сценариев валидации
		 */
		if(isset($aParams['on'])) {
			if(is_array($aParams['on'])) {
				$aOn=$aParams['on'];
			} else {
				$aOn=preg_split('/[\s,]+/',$aParams['on'],-1,PREG_SPLIT_NO_EMPTY);
			}
		} else {
			$aOn=array();
		}
		/**
		 * Если в качестве имени валидатора указан метод объекта, то создаем специальный валидатор
		 */
		$sMethod='validate'.func_camelize($sName);
		if(method_exists($oObject,$sMethod)) {
			$oValidator = new ValidatorInline();
			if (!is_null($aFields)) {
				$oValidator->fields=$aFields;
			}
			$oValidator->object=$oObject;
			$oValidator->method=$sMethod;
			$oValidator->params=$aParams;
			if(isset($aParams['skipOnError'])) {
				$oValidator->skipOnError=$aParams['skipOnError'];
			}
		} else {
			/**
			 * Иначе создаем валидатор по имени
			 */
			if (!is_null($aFields)) {
				$aParams['fields']=$aFields;
			}
			$sValidatorName=self::$validatorClasses[$sName];
			$oValidator = new $sValidatorName();
			foreach($aParams as $sNameParam=>$sValue) {
				$oValidator->$sNameParam=$sValue;
			}
		}
		$oValidator->on=empty($aOn) ? array() : array_combine($aOn,$aOn);
		return $oValidator;
	}
	/**
	 * Возвращает факт наличия ошибки после валидации
	 *
	 * @return bool
	 */
	public function HasErrors() {
		return count($this->aErrors) ? true : false;
	}
	/**
	 * Возвращает список ошибок после валидации
	 *
	 * @return array
	 */
	public function GetErrors() {
		return $this->aErrors;
	}
	/**
	 * Возвращает последнюю ошибку после валидации
	 *
	 * @param bool $bRemove	Удалять или нет ошибку из списка ошибок
	 * @return bool|string
	 */
	public function GetErrorLast($bRemove=false) {
		if (!$this->HasErrors()) {
			return false;
		}
		if ($bRemove) {
			return array_pop($this->aErrors);
		} else {
			return $this->aErrors[count($this->aErrors)-1];
		}
	}
	/**
	 * Добавляет ошибку в список
	 *
	 * @param string $sError	Текст ошибки
	 */
	public function AddError($sError) {
		$this->aErrors[]=$sError;
	}
	/**
	 * Очищает список ошибок
	 */
	public function ClearErrors() {
		$this->aErrors=array();
	}
}
