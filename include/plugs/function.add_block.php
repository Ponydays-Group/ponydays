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
 * Добавляет блок(в сайдбар, тулбар и т.п.)
 *
 * @param array $params
 * @param \Smarty_Internal_Template $smarty
 * @return string
 */
function smarty_function_add_block($params, &$smarty)
{
	if (!array_key_exists('group', $params)) {
		trigger_error("add_block: missing 'group' parameter",E_USER_WARNING);
        return '';
    }

	if (!array_key_exists('name', $params)) {
		trigger_error("add_block: missing 'name' parameter",E_USER_WARNING);
		return '';
	}

	$aBlockParams=(isset($params['params']) && is_array($params['params'])) ? $params['params'] : array();
	$iPriority=isset($params['priority']) ? $params['priority'] : 5;

	foreach($params as $k=>$v) {
		if (!in_array($k,array('group','name','params','priority'))) {
			$aBlockParams[$k]=$v;
		}
	}

	LS::Make(ModuleViewer::class)->AddBlock($params['group'],$params['name'],$aBlockParams,$iPriority);
	return '';
}
