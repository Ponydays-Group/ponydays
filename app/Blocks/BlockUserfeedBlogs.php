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

use App\Modules\ModuleBlog;
use App\Modules\ModuleUser;
use App\Modules\ModuleUserfeed;
use Engine\Block;
use Engine\LS;
use Engine\Modules\ModuleViewer;

/**
 * Блок настройки списка блогов в ленте
 *
 * @package blocks
 * @since 1.0
 */
class BlockUserfeedBlogs extends Block {
	/**
	 * Запуск обработки
	 */
public function Exec() {
		/**
		 * Пользователь авторизован?
		 */
		if ($oUserCurrent = LS::Make(ModuleUser::class)->getUserCurrent()) {
			$aUserSubscribes = LS::Make(ModuleUserfeed::class)->getUserSubscribes($oUserCurrent->getId());
			/**
			 * Получаем список ID блогов, в которых состоит пользователь
			 */
			$aBlogsId = LS::Make(ModuleBlog::class)->GetBlogUsersByUserId($oUserCurrent->getId(), array(ModuleBlog::BLOG_USER_ROLE_USER,ModuleBlog::BLOG_USER_ROLE_MODERATOR,ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR),true);
			/**
			 * Получаем список ID блогов, которые создал пользователь
			 */
			$aBlogsAll=LS::Make(ModuleBlog::class)->GetBlogs($oUserCurrent->getId(),true);
			$aBlogsId=array_merge($aBlogsId,$aBlogsAll);
			$aBlogs=LS::Make(ModuleBlog::class)->GetBlogsAdditionalData($aBlogsId,array('owner'=>array()),array('blog_title'=>'asc'));
			/**
			 * Выводим в шаблон
			 */
            /** @var \Engine\Modules\ModuleViewer $viewer */
            $viewer = LS::Make(ModuleViewer::class);
			$viewer->Assign('aUserfeedSubscribedBlogs', $aUserSubscribes['blogs']);
			$viewer->Assign('aUserfeedBlogs', $aBlogs);
		}
	}
}
