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
 * CDateValidator class file.
 *
 * @author    Qiang Xue <qiang.xue@gmail.com>
 * @link      http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

use DateTimeParser;
use Engine\LS;
use Engine\Modules\ModuleLang;

/**
 * Валидатор даты
 * Валидатор использует внешний класс DateTimeParser
 *
 * @package engine.modules.validate
 * @since   1.0
 */
class ValidatorDate extends Validator
{
    /**
     * Формат допустимой даты, может содержать список форматов в массиве
     *
     * @var string|array
     */
    public $format = 'dd-MM-yyyy';
    /**
     * Допускать или нет пустое значение
     *
     * @var bool
     */
    public $allowEmpty = true;

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
            return $this->getMessage($lang->Get('validate_date_format_invalid', null, false), 'msg');
        }
        if ($this->allowEmpty && $this->isEmpty($sValue)) {
            return true;
        }

        require_once('./lib/DateTime/DateTimeParser.php');

        $aFormats = is_string($this->format) ? [$this->format] : $this->format;
        $bValid = false;
        foreach ($aFormats as $sFormat) {
            $iTimestamp = DateTimeParser::parse(
                $sValue,
                $sFormat,
                ['month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0]
            );
            if ($iTimestamp !== false) {
                $bValid = true;
                break;
            }
        }

        if (!$bValid) {
            return $this->getMessage($lang->Get('validate_date_format_invalid', null, false), 'msg');
        }

        return true;
    }
}
