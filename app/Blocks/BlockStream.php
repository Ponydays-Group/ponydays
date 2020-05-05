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

use App\Modules\Comment\ModuleComment;
use Engine\Block;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Viewer\ModuleViewer;

/**
 * Обработка блока с комментариями (прямой эфир)
 *
 * @package blocks
 * @since 1.0
 */
class BlockStream extends Block {
	/**
	 * Запуск обработки
	 */
	public function Exec() {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
		/**
		 * Получаем комментарии
		 */
		if ($aComments=LS::Make(ModuleComment::class)->GetCommentsOnline('topic',Config::Get('block.stream.row'))) {
			$oViewer=$viewer->GetLocalViewer();
			$oViewer->Assign('aComments',$aComments);
			/**
			 * Формируем результат в виде шаблона и возвращаем
			 */
			$sTextResult=$oViewer->Fetch("blocks/block.stream_comment.tpl");
			$viewer->Assign('sStreamComments',$sTextResult);
		}
	}
}
