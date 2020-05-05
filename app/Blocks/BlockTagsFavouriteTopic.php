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

use App\Modules\Favourite\ModuleFavourite;
use App\Modules\Tools\ModuleTools;
use App\Modules\User\ModuleUser;
use Engine\Block;
use Engine\LS;
use Engine\Modules\Viewer\ModuleViewer;

/**
 * Обрабатывает блок облака тегов для избранного
 *
 * @package blocks
 * @since 1.0
 */
class BlockTagsFavouriteTopic extends Block {
	/**
	 * Запуск обработки
	 */
	public function Exec() {
		/**
		 * Пользователь авторизован?
		 */
		if ($oUserCurrent = LS::Make(ModuleUser::class)->getUserCurrent()) {
			if (!($oUser=$this->getParam('user'))) {
				$oUser=$oUserCurrent;
			}
            /** @var ModuleViewer $viewer */
            $viewer = LS::Make(ModuleViewer::class);
            /** @var ModuleFavourite $fav */
            $fav = LS::Make(ModuleFavourite::class);
			/**
			 * Получаем список тегов
			 */
			$aTags=$fav->GetGroupTags($oUser->getId(),'topic',null,70);
			/**
			 * Расчитываем логарифмическое облако тегов
			 */
			LS::Make(ModuleTools::class)->MakeCloud($aTags);
			/**
			 * Устанавливаем шаблон вывода
			 */
			$viewer->Assign("aFavouriteTopicTags",$aTags);
			/**
			 * Получаем список тегов пользователя
			 */
			$aTags=$fav->GetGroupTags($oUser->getId(),'topic',true,70);
			/**
			 * Расчитываем логарифмическое облако тегов
			 */
			LS::Make(ModuleTools::class)->MakeCloud($aTags);
			/**
			 * Устанавливаем шаблон вывода
			 */
			$viewer->Assign("aFavouriteTopicUserTags",$aTags);
			$viewer->Assign("oFavouriteUser",$oUser);
		}
	}
}
