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
use Engine\Modules\ModuleLang;

/**
 * Загружает список языковых текстовок в шаблон
 *
 * @param array $params
 * @param \Smarty_Internal_Template $smarty
 * @return string
 */
function smarty_function_lang_load($params, &$smarty)
{

	if (!array_key_exists('name', $params)) {
		trigger_error("lang_load: missing 'name' parameter",E_USER_WARNING);
		return '';
	}

	$aLangName=explode(',',$params['name']);

	$aLangMsg=array();
	foreach ($aLangName as $sName) {
		$aLangMsg[$sName]=LS::Make(ModuleLang::class)->Get(trim($sName),array(),false);
	}

	if (!isset($params['json']) or $params['json']!==false) {
		$aLangMsg = json_encode($aLangMsg);
	}

	if (!empty($params['assign'])) {
		$smarty->assign($params['assign'], $aLangMsg);
	} else {
		return $aLangMsg;
	}
	return '';
}
