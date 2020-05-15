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

use App\Modules\ModuleAPI;
use App\Modules\ModuleCrypto;
use App\Entities\EntityUser;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\LS;
use Engine\Modules\ModuleSecurity;
use Engine\Modules\ModuleViewer;

class ActionApi extends Action

{
    /**
     * Текущий пользователь
     *
     * @var EntityUser|null
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
        $this->AddEvent('login', 'EventAjaxLogin');
    }

    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    protected
    function EventTest()
    {
        echo "TEST";
    }
    protected function EventAjaxLogin() {

        //Проверяем тип запроса. Если не POST - возвращаем ошибку 400
        LS::Make(ModuleViewer::class)->SetResponseAjax('json',true,false);
        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            LS::Make(ModuleViewer::class)->AssignAjax('message', 'Bad request');
            return;
        }

        if ((getRequest('login') and getRequest('password')) or getRequest('device_uid')) {

            //Если логин и пароль не заданы, то пробуем залогинить по device_uid

            if (!getRequest('login') and !getRequest('password') and getRequest('device_uid')){
                if ($oUser=LS::Make(ModuleUser::class)->GetUserById(LS::Make(ModuleAPI::class)->getUserByKey(getRequest('device_uid')))) {
                    if (LS::Make(ModuleUser::class)->Authorization($oUser,true)){
                        LS::Make(ModuleViewer::class)->AssignAjax('notice', "Authenfication complete!");
                    }
                } else {
                    LS::Make(ModuleViewer::class)->AssignAjax('message', 'Device UID incorrect');
                    return;
                }
                return;
            }

            if (!is_string(getRequest('login')) or !is_string(getRequest('password'))) {
                LS::Make(ModuleViewer::class)->AssignAjax('message', "Login or password isn't a string");
                return;
            }
            /**
             * Проверяем есть ли такой юзер по логину
             */
            if ((func_check(getRequest('login'),'mail') and $oUser=LS::Make(ModuleUser::class)->GetUserByMail(getRequest('login')))  or  $oUser=LS::Make(ModuleUser::class)->GetUserByLogin(getRequest('login'))) {
                /**
                 * Сверяем хеши паролей и проверяем активен ли юзер
                 */

                //TODO: DRY IT.

				/**
				 * Проверяем пароль и обновляем хеш, если нужно
				 */
				$user_password = $oUser->getPassword();
				if(LS::Make(ModuleCrypto::class)->PasswordVerify(getRequest('password'), $user_password)) {
					if(LS::Make(ModuleCrypto::class)->PasswordNeedsRehash($user_password)) {
						$oUser->setPassword(LS::Make(ModuleCrypto::class)->PasswordHash(getRequest('password')));
						LS::Make(ModuleUser::class)->Update($oUser);
					}

					/**
					 * Проверяем активен ли юзер
					 */
                    if (!$oUser->getActivate()) {
                        LS::Make(ModuleViewer::class)->AssignAjax('message', "Login or password isn't a string");
                        return;
                    }
                    /**
                     * Авторизуем
                     */
                    if (LS::Make(ModuleUser::class)->Authorization($oUser,true)){
                        if (getRequest('device_uid')){
                            if ($sKey = LS::Make(ModuleAPI::class)->setKey($oUser->getId(), getRequest('device_uid'))){
                                LS::Make(ModuleViewer::class)->AssignAjax('newKey', $sKey);
                            }
                        }
                        LS::Make(ModuleViewer::class)->AssignAjax('notice', "Authenfication complete!");
                        LS::Make(ModuleViewer::class)->AssignAjax('ls_key', LS::Make(ModuleSecurity::class)->GenerateSessionKey());
                    } else {
                        LS::Make(ModuleViewer::class)->AssignAjax('message', 'Authenfication faild');
                        return;
                    }
                } else {
                    LS::Make(ModuleViewer::class)->AssignAjax('message', 'Wrong password!');
                    return;
                }

            } else {
                LS::Make(ModuleViewer::class)->AssignAjax('message', 'User not exists!');
                return;
            }
        } else {
            LS::Make(ModuleViewer::class)->AssignAjax('message', 'Bad request');
            return;
        }
    }
}
