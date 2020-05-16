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

namespace App\Entities;

use App\Modules\ModuleUser;
use Engine\Config;
use Engine\Entity;
use Engine\LS;
use Engine\Modules\ModuleLang;

/**
 * Сущность заметки о пользователе
 *
 * @package modules.user
 * @since   1.0
 */
class EntityUserNote extends Entity
{
    /**
     * Определяем правила валидации
     *
     * @var array
     */
    protected $aValidateRules = [
        ['target_user_id', 'target'],
    ];

    /**
     * Инициализация
     */
    public function Init()
    {
        parent::Init();
        $this->aValidateRules[] = [
            'text',
            'string',
            'max'        => Config::Get('module.user.usernote_text_max'),
            'min'        => 1,
            'allowEmpty' => false
        ];
    }

    /**
     * Валидация пользователя
     *
     * @param string $sValue  Значение
     * @param array  $aParams Параметры
     *
     * @return bool
     */
    public function ValidateTarget($sValue, $aParams)
    {
        if ($oUserTarget = LS::Make(ModuleUser::class)->GetUserById($sValue) and $this->getUserId()
            != $oUserTarget->getId()
        ) {
            return true;
        }

        return LS::Make(ModuleLang::class)->Get('user_note_target_error');
    }
}
