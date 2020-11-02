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

namespace App\Actions;

use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Routing\Controller;
use Engine\View\View;

/**
 * Экшен обработки УРЛа вида /error/ т.е. ошибок
 *
 * @package actions
 * @since   1.0
 */
class ActionError extends Controller
{
    /**
     * Список специфических HTTP ошибок для которых необходимо отдавать header
     *
     * @var array
     */
    protected $aHttpErrors = [
        '404' => [
            'header' => '404 Not Found',
        ],
    ];

    /**
     * Вывод ошибки
     *
     * @param string                        $event
     *
     * @param \Engine\Modules\ModuleMessage $msg
     * @param \Engine\Modules\ModuleLang    $lang
     *
     * @return \Engine\View\View
     */
    public function error(ModuleMessage $msg, ModuleLang $lang, string $event = '400'): View
    {
        /**
         * Если евент равен одной из ошибок из $aHttpErrors, то шлем браузеру специфичный header
         * Например, для 404 в хидере будет послан браузеру заголовок HTTP/1.1 404 Not Found
         */
        if (array_key_exists($event, $this->aHttpErrors)) {
            $msg->AddErrorSingle($lang->Get('system_error_'.$event), $event);
            $aHttpError = $this->aHttpErrors[$event];
            if (isset($aHttpError['header'])) {
                $sProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
                header("{$sProtocol} {$aHttpError['header']}");
            }
        }

        return View::by('error/index')->withHtmlTitle($lang->Get('error'));
    }
}
