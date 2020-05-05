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

use App\Modules\Page\ModulePage;
use Engine\Hook;
use Engine\LS;
use Engine\Modules\Viewer\ModuleViewer;

/**
 * Регистрация хука для вывода меню страниц
 *
 */
class HookPage extends Hook {
	public function RegisterHook() {
		$this->AddHook('template_main_menu_item','Menu');
	}

	public function Menu() {
		$aPages=LS::Make(ModulePage::class)->GetPages(array('pid'=>null,'main'=>1,'active'=>1));

        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->Assign('aPagesMain',$aPages);
		return $viewer->Fetch('main_menu.tpl');
	}
}
