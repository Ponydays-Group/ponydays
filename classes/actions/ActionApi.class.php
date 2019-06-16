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

class ActionApi extends Action

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
        $this->Viewer_SetResponseAjax('json',true,false);
        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            $this->Viewer_AssignAjax('message', 'Bad request');
            return;
        }

        if ((getRequest('login') and getRequest('password')) or getRequest('device_uid')) {

            //Если логин и пароль не заданы, то пробуем залогинить по device_uid

            if (!getRequest('login') and !getRequest('password') and getRequest('device_uid')){
                if ($oUser=$this->User_GetUserById($this->ModuleApi_getUserByKey(getRequest('device_uid')))) {
                    if ($this->User_Authorization($oUser,true)){
                        $this->Viewer_AssignAjax('notice', "Authenfication complete!");
                    }
                } else {
                    $this->Viewer_AssignAjax('message', 'Device UID incorrect');
                    return;
                }
                return;
            }

            if (!is_string(getRequest('login')) or !is_string(getRequest('password'))) {
                $this->Viewer_AssignAjax('message', "Login or password isn't a string");
                return;
            }
            /**
             * Проверяем есть ли такой юзер по логину
             */
            if ((func_check(getRequest('login'),'mail') and $oUser=$this->User_GetUserByMail(getRequest('login')))  or  $oUser=$this->User_GetUserByLogin(getRequest('login'))) {
                /**
                 * Сверяем хеши паролей и проверяем активен ли юзер
                 */

                //TODO: DRY IT.

                // ежели пароль - несолёный md5, но совпадает, обновим его, и сделаем вид, что так и было
                if ( strlen($oUser->getPassword()) == 32 && $oUser->getPassword() == func_encrypt(getRequest('password')) )  {
                    $oUser->setPassword(encryptPassword(getRequest('password')));
                    $this->User_Update($oUser);
                }
                if (checkPassword($oUser->getPassword(), getRequest('password'))) {
                    if (!$oUser->getActivate()) {
                        $this->Viewer_AssignAjax('message', "Login or password isn't a string");
                        return;
                    }
                    /**
                     * Авторизуем
                     */
                    if ($this->User_Authorization($oUser,true)){
                        if (getRequest('device_uid')){
                            if ($sKey = $this->ModuleApi_setKey($oUser->getId(), getRequest('device_uid'))){
                                $this->Viewer_AssignAjax('newKey', $sKey);
                            }
                        }
                        $this->Viewer_AssignAjax('notice', "Authenfication complete!");
                        $this->Viewer_AssignAjax('ls_key', $this->Security_GenerateSessionKey());
                    } else {
                        $this->Viewer_AssignAjax('message', 'Authenfication faild');
                        return;
                    }
                } else {
                    $this->Viewer_AssignAjax('message', 'Wrong password!');
                    return;
                }

            } else {
                $this->Viewer_AssignAjax('message', 'User not exists!');
                return;
            }
        } else {
            $this->Viewer_AssignAjax('message', 'Bad request');
            return;
        }
    }
}
