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

namespace App\Blocks;

use App\Modules\ModuleUser;
use App\Modules\ModuleUserfeed;
use Engine\Block;
use Engine\LS;
use Engine\Modules\ModuleViewer;

/**
 * Блок настройки списка пользователей в ленте
 *
 * @package blocks
 * @since   1.0
 */
class BlockUserfeedUsers extends Block
{
    /**
     * Запуск обработки
     */
    public function Exec()
    {
        /**
         * Получаем необходимые переменные и прогружаем в шаблон
         */
        if ($oUserCurrent = LS::Make(ModuleUser::class)->getUserCurrent()) {
            /**
             * Получаем необходимые переменные и прогружаем в шаблон
             */
            $aUserSubscribes = LS::Make(ModuleUserfeed::class)->getUserSubscribes($oUserCurrent->getId());
            $aFriends = LS::Make(ModuleUser::class)->getUsersFriend($oUserCurrent->getId());

            /** @var \Engine\Modules\ModuleViewer $viewer */
            $viewer = LS::Make(ModuleViewer::class);
            $viewer->Assign('aUserfeedSubscribedUsers', $aUserSubscribes['users']);
            $viewer->Assign('aUserfeedFriends', $aFriends['collection']);
        }
    }
}
