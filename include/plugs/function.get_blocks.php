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

use Engine\LS;
use Engine\Modules\ModuleViewer;

/**
 * Загружает в переменную список блоков
 *
 * @param array $params
 * @param \Smarty_Internal_Template $smarty
 * @return string
 */
function smarty_function_get_blocks($params, &$smarty)
{
	if (!array_key_exists('assign', $params)) {
		trigger_error("get_blocks: missing 'assign' parameter",E_USER_WARNING);
        return '';
    }

	$smarty->assign($params['assign'], LS::Make(ModuleViewer::class)->GetBlocks(true));
	return '';
}
