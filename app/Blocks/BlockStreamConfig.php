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

use App\Modules\ModuleStream;
use App\Modules\ModuleUser;
use Engine\Block;
use Engine\LS;
use Engine\Modules\ModuleViewer;

/**
 * Блок настройки ленты активности
 *
 * @package blocks
 * @since   1.0
 */
class BlockStreamConfig extends Block
{
    /**
     * Запуск обработки
     */
    public function Exec()
    {
        /**
         * пользователь авторизован?
         */
        if ($oUserCurrent = LS::Make(ModuleUser::class)->getUserCurrent()) {
            /** @var ModuleViewer $viewer */
            $viewer = LS::Make(ModuleViewer::class);
            /**
             * Получаем и прогружаем необходимые переменные в шаблон
             */
            $aTypesList = LS::Make(ModuleStream::class)->getTypesList($oUserCurrent->getId());
            $viewer->Assign('aStreamTypesList', $aTypesList);
            $aUserSubscribes = LS::Make(ModuleStream::class)->getUserSubscribes($oUserCurrent->getId());
            $aFriends = LS::Make(ModuleUser::class)->getUsersFriend($oUserCurrent->getId());
            $viewer->Assign('aStreamSubscribedUsers', $aUserSubscribes);
            $viewer->Assign('aStreamFriends', $aFriends['collection']);
        }
    }
}