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

use App\Modules\Tools\ModuleTools;
use App\Modules\Topic\ModuleTopic;
use App\Modules\User\ModuleUser;
use Engine\Block;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Viewer\ModuleViewer;

/**
 * Обрабатывает блок облака тегов
 *
 * @package blocks
 * @since 1.0
 */
class BlockTags extends Block {
	/**
	 * Запуск обработки
	 */
	public function Exec() {
        /** @var ModuleTopic $topic */
        $topic = LS::Make(ModuleTopic::class);
		/**
		 * Получаем список тегов
		 */
		$aTags=$topic->GetOpenTopicTags(Config::Get('block.tags.tags_count'));
		/**
		 * Расчитываем логарифмическое облако тегов
		 */
		if ($aTags) {
			LS::Make(ModuleTools::class)->MakeCloud($aTags);
			/**
			 * Устанавливаем шаблон вывода
			 */
			LS::Make(ModuleViewer::class)->Assign("aTags",$aTags);
		}
		/**
		 * Теги пользователя
		 */
		if ($oUserCurrent=LS::Make(ModuleUser::class)->getUserCurrent()) {
			$aTags=$topic->GetOpenTopicTags(Config::Get('block.tags.personal_tags_count'), $oUserCurrent->getId());
			/**
			 * Расчитываем логарифмическое облако тегов
			 */
			if ($aTags) {
				LS::Make(ModuleTools::class)->MakeCloud($aTags);
				/**
				 * Устанавливаем шаблон вывода
				 */
                LS::Make(ModuleViewer::class)->Assign("aTagsUser",$aTags);
			}
		}
	}
}
