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

use App\Modules\Geo\ModuleGeo;
use App\Modules\Tools\ModuleTools;
use Engine\Block;
use Engine\LS;
use Engine\Modules\Viewer\ModuleViewer;

/**
 * Обрабатывает блок облака тегов стран юзеров
 *
 * @package blocks
 * @since 1.0
 */
class BlockTagsCountry extends Block {
	/**
	 * Запуск обработки
	 */
	public function Exec() {
		/**
		 * Получаем страны
		 */
		$aCountries=LS::Make(ModuleGeo::class)->GetGroupCountriesByTargetType('user',20);
		/**
		 * Формируем облако тегов
		 */
		LS::Make(ModuleTools::class)->MakeCloud($aCountries);
		/**
		 * Выводим в шаблон
		 */
        LS::Make(ModuleViewer::class)->Assign("aCountryList",$aCountries);
	}
}
