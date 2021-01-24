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
use Engine\Result\View\HtmlView;
use Engine\Result\View\View;
use Engine\Routing\Controller;
use Engine\Routing\Exception\Http\HttpException;

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
     * TODO: More http states
     * @var array
     */
    protected $aHttpErrors = [
        // '400' => ['header' => '404 Bad Request'],
        // '401' => ['header' => '401 Unauthorized'],
        // '402' => ['header' => '402 Payment Required'],
        // '403' => ['header' => '403 Forbidden'],
        '404' => ['header' => '404 Not Found'],
        // '405' => ['header' => '405 Method Not Allowed'],
    ];

    /**
     * Вывод ошибки
     *
     * @param \Engine\Modules\ModuleLang                        $lang
     * @param string                                            $event
     * @param \Engine\Routing\Exception\Http\HttpException|null $httpException
     *
     * @return \Engine\Result\View\View
     */
    public function eventError(ModuleLang $lang, string $event = '400', HttpException $httpException = null): View
    {
        $view = HtmlView::by('error/index')->withHtmlTitle($lang->Get('error'));
        /**
         * Если евент равен одной из ошибок из $aHttpErrors, то шлем браузеру специфичный header
         * Например, для 404 в хидере будет послан браузеру заголовок HTTP/1.1 404 Not Found
         */
        if (array_key_exists($event, $this->aHttpErrors)) {
            $view->msgError($lang->Get('system_error_'.$event), $event);
            $aHttpError = $this->aHttpErrors[$event];
            if (isset($aHttpError['header'])) {
                $sProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
                header("{$sProtocol} {$aHttpError['header']}");
            }
        }

        return $view;
    }
}
