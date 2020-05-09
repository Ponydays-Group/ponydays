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

/**
 * Плагин для смарти
 * Подключает обработчик блоков шаблона
 *
 * @param array $aParams
 * @param Smarty $oSmarty
 * @return string
 */
function smarty_insert_block($aParams,&$oSmarty) {
	/**
	 * Устанавливаем шаблон
	 */
	$sBlock=ucfirst(basename($aParams['block']));

	$sBlockTemplate = 'blocks/block.'.$aParams['block'].'.tpl';
	$sBlock ='App\\Blocks\\Block'.$sBlock;

	if (!isset($aParams['block']) or !$oSmarty->templateExists($sBlockTemplate)) {
		trigger_error("Not found template for block: ".$sBlockTemplate,E_USER_WARNING);
		return ;
	}
	/**
	 * параметры
	 */
	$aParamsBlock=array();
	if (isset($aParams['params'])) {
		$aParamsBlock=$aParams['params'];
	}
	/**
	 * Подключаем необходимый обработчик
	 */
	$oBlock = new $sBlock($aParamsBlock);
	/**
	 * Запускаем обработчик
	 */
	$oBlock->Exec();
	/**
	 * Возвращаем результат в виде обработанного шаблона блока
	 */
	return $oSmarty->fetch($sBlockTemplate);
}
