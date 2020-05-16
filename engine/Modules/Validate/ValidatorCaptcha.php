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

use Engine\LS;
use Engine\Modules\ModuleLang;

/**
 * Валидатор каптчи (число с картинки)
 *
 * @package engine.modules.validate
 * @since   1.0
 */
class ValidatorCaptcha extends Validator
{
    /**
     * Допускать или нет пустое значение
     *
     * @var bool
     */
    public $allowEmpty = false;

    /**
     * Запуск валидации
     *
     * @param mixed $sValue Данные для валидации
     *
     * @return bool|string
     */
    public function validate($sValue)
    {
        /** @var \Engine\Modules\ModuleLang $lang */
        $lang = LS::Make(ModuleLang::class);
        if (is_array($sValue)) {
            return $this->getMessage($lang->Get('validate_captcha_not_valid', null, false), 'msg');
        }
        if ($this->allowEmpty && $this->isEmpty($sValue)) {
            return true;
        }

        if (!isset($_SESSION['captcha_keystring']) or $_SESSION['captcha_keystring'] != strtolower($sValue)) {
            return $this->getMessage($lang->Get('validate_captcha_not_valid', null, false), 'msg');
        }

        return true;
    }
}
