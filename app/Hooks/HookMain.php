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

namespace App\Hooks;

use App\Modules\ModuleUser;
use Engine\Config;
use Engine\Hook;
use Engine\LS;
use Engine\Router;

/**
 * Регистрация основных хуков
 *
 * @package hooks
 * @since   1.0
 */
class HookMain extends Hook
{
    /**
     * Регистрируем хуки
     */
    public function RegisterHook()
    {
        $this->AddHook('init_action', 'InitAction', __CLASS__, 1000);
    }

    /**
     * Обработка хука инициализации экшенов
     */
    public function InitAction()
    {
        /**
         * Проверка на закрытый режим
         */
        $oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        if (!$oUserCurrent and Config::Get('general.close') and Router::GetAction() != 'registration'
            and Router::GetAction() != 'login'
        ) {
            Router::Action('login');
        }
    }
}
