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
 * Экшен обработки ajax запросов
 * Ответ отдает в JSON фомате
 *
 * @package actions
 * @since 1.0
 */
class ActionServer extends Action

{
    /**
     * Текущий пользователь
     *
     * @var ModuleUser_EntityUser|null
     */
    protected $oUserCurrent = null;

    /**
     * Инициализация
     */
    public

    function Init()
    {
    }

    /**
     * Регистрация евентов
     */
    protected
    function RegisterEvent()
    {
        $this->AddEvent('deploy', 'EventDeploy');
    }

    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    protected
    function EventDeploy()
    {
        if (getRequest('token')!=Config::Get('deploy_token')) {
            return false;
        }
        $this->Viewer_SetResponseAjax('json', true, false);
        shell_exec('cd '.dirname(__FILE__)."/../../frontend/".' && git pull && webpack .');
    }
}