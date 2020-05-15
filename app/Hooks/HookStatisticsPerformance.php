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

use Engine\Engine;
use Engine\Hook;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Регистрация хука для вывода статистики производительности
 *
 * @package hooks
 * @since 1.0
 */
class HookStatisticsPerformance extends Hook {
	/**
	 * Регистрируем хуки
	 */
	public function RegisterHook() {
		$this->AddHook('template_body_end','Statistics',__CLASS__,-1000);
	}
	/**
	 * Обработка хука перед закрывающим тегом body
	 *
	 * @return string
	 */
	public function Statistics() {
		$oEngine=Engine::getInstance();
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = $oEngine->make(ModuleViewer::class);
        /**
		 * Подсчитываем время выполнения
		 */
		$iTimeInit=$oEngine->GetTimeInit();
		$iTimeFull=round(microtime(true)-$iTimeInit,3);
		$viewer->Assign('iTimeFullPerformance',$iTimeFull);
		/**
		 * Получаем статистику по кешу и БД
		 */
		$aStats=$oEngine->getStats();
		$aStats['cache']['time']=round($aStats['cache']['time'],5);
		$viewer->Assign('aStatsPerformance',$aStats);
		$viewer->Assign('bIsShowStatsPerformance',Router::GetIsShowStats());
		/**
		 * В ответ рендерим шаблон статистики
		 */
		return $viewer->Fetch('statistics_performance.tpl');
	}
}
