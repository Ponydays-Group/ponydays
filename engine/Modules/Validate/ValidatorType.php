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
 * CTypeValidator class file.
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
 * Валидатор типа данных
 * Для типа дата/время используется внешний валидатор DateTimeParser
 *
 * @package engine.modules.validate
 * @since   1.0
 */
class ValidatorType extends Validator
{
    /**
     * Допустимый тип данных.
     * Допустимые значения: 'string', 'integer', 'float', 'array', 'date', 'time' и 'datetime'.
     *
     * @var string
     */
    public $type = 'string';
    /**
     * Допустимый формат даты, актуально при type = date
     *
     * @var string
     */
    public $dateFormat = 'dd-MM-yyyy';
    /**
     * Допустимый формат времени, актуально при type = time
     *
     * @var string
     */
    public $timeFormat = 'hh:mm';
    /**
     * Допустимый формат даты со временем, актуально при type = datetime
     *
     * @var string
     */
    public $datetimeFormat = 'dd-MM-yyyy hh:mm';
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
        if ($this->allowEmpty && $this->isEmpty($sValue)) {
            return true;
        }

        require_once('./lib/DateTime/DateTimeParser.php');

        if ($this->type === 'integer') {
            $bValid = preg_match('/^[-+]?[0-9]+$/', trim($sValue));
        } elseif ($this->type === 'float') {
            $bValid = preg_match('/^[-+]?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?$/', trim($sValue));
        } elseif ($this->type === 'date') {
            $bValid = DateTimeParser::parse(
                    $sValue,
                    $this->dateFormat,
                    ['month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0]
                ) !== false;
        } elseif ($this->type === 'time') {
            $bValid = DateTimeParser::parse($sValue, $this->timeFormat) !== false;
        } elseif ($this->type === 'datetime') {
            $bValid = DateTimeParser::parse(
                    $sValue,
                    $this->datetimeFormat,
                    ['month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0, 'second' => 0]
                ) !== false;
        } elseif ($this->type === 'array') {
            $bValid = is_array($sValue);
        } else {
            return true;
        }

        if (!$bValid) {
            /** @var ModuleLang $lang */
            $lang = LS::Make(ModuleLang::class);

            return $this->getMessage($lang->Get('validate_type_error', null, false), 'msg', ['type' => $this->type]);
        }

        return true;
    }
}
