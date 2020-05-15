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

namespace Engine\Modules\Validate;

/**
 * CRequiredValidator class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use Engine\LS;
use Engine\Modules\ModuleLang;

/**
 * Валидатор на пустое значение или точное совпадение
 *
 * @package engine.modules.validate
 * @since 1.0
 */
class ValidatorRequired extends Validator {
	/**
	 * Требуемое значение для точного совпадения
	 *
	 * @var mixed
	 */
	public $requiredValue;
	/**
	 * Строгое сравнение с учетом типов, актуально при использовании requiredValue
	 *
	 * @var bool
	 */
	public $strict=false;

	/**
	 * Запуск валидации
	 *
	 * @param mixed $sValue	Данные для валидации
	 *
	 * @return bool|string
	 */
	public function validate($sValue) {
	    /** @var \Engine\Modules\ModuleLang $lang */
	    $lang = LS::Make(ModuleLang::class);
		if($this->requiredValue!==null) {
			if(!$this->strict && $sValue!=$this->requiredValue || $this->strict && $sValue!==$this->requiredValue) {
				return $this->getMessage($lang->Get('validate_required_must_be',null,false),'msg',array('value'=>$this->requiredValue));
			}
		} else if($this->isEmpty($sValue,true)) {
			return $this->getMessage($lang->Get('validate_required_cannot_blank',null,false),'msg');
		}
		return true;
	}
}
