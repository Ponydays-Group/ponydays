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

use App\Entities\EntityUser;
use App\Modules\ModuleCrypto;
use App\Modules\ModuleNotify;
use App\Modules\ModuleStream;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleSession;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки регистрации
 *
 * @package actions
 * @since   1.0
 */
class ActionRegistration extends Action
{
    /**
     * Инициализация
     *
     */
    public function Init()
    {
        /**
         * Проверяем аторизован ли юзер
         */
        if (LS::Make(ModuleUser::class)->isRegistrationClosed()) {
            LS::Make(ModuleMessage::class)->AddNoticeSingle("Регистрация временно закрыта.");
            Router::Action('error');

            return;
        }


        if (LS::Make(ModuleUser::class)->IsAuthorization()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('registration_is_authorization'),
                LS::Make(ModuleLang::class)->Get('attention')
            );
            Router::Action('error');

            return;
        }
        /**
         * Если включены инвайты то перенаправляем на страницу регистрации по инвайтам
         */
        if (!LS::Make(ModuleUser::class)->IsAuthorization() and Config::Get('general.reg.invite') and !in_array(
                Router::GetActionEvent(),
                ['invite', 'activate', 'confirm']
            ) and !$this->CheckInviteRegister()
        ) {
            Router::Action('registration', 'invite');

            return;
        }
        $this->SetDefaultEvent('index');
        /**
         * Устанавливаем title страницы
         */
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('registration'));
    }

    /**
     * Регистрируем евенты
     *
     */
    protected function RegisterEvent()
    {
        $this->AddEvent('index', 'EventIndex');
        $this->AddEvent('confirm', 'EventConfirm');
        $this->AddEvent('activate', 'EventActivate');
        $this->AddEvent('invite', 'EventInvite');

        $this->AddEvent('ajax-validate-fields', 'EventAjaxValidateFields');
        $this->AddEvent('ajax-registration', 'EventAjaxRegistration');
    }



    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    /**
     * Ajax валидация форму регистрации
     */
    protected function EventAjaxValidateFields()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        /**
         * Создаем объект пользователя и устанавливаем сценарий валидации
         */
        $oUser = new EntityUser();
        $oUser->_setValidateScenario('registration');
        /**
         * Пробегаем по переданным полям/значениям и валидируем их каждое в отдельности
         */
        $aFields = getRequest('fields');
        if (is_array($aFields)) {
            foreach ($aFields as $aField) {
                if (isset($aField['field']) and isset($aField['value'])) {
                    LS::Make(ModuleHook::class)->Run('registration_validate_field', ['aField' => &$aField]);

                    $sField = $aField['field'];
                    $sValue = $aField['value'];
                    /**
                     * Список полей для валидации
                     */
                    switch ($sField) {
                        case 'login':
                            $oUser->setLogin($sValue);
                            break;
                        case 'mail':
                            $oUser->setMail($sValue);
                            break;
                        case 'captcha':
                            $oUser->setCaptcha($sValue);
                            break;
                        case 'password':
                            $oUser->setPassword($sValue);
                            break;
                        case 'password_confirm':
                            $oUser->setPasswordConfirm($sValue);
                            $oUser->setPassword(
                                isset($aField['params']['password']) ? $aField['params']['password'] : null
                            );
                            break;
                        default:
                            continue;
                            break;
                    }
                    /**
                     * Валидируем поле
                     */
                    $oUser->_Validate([$sField], false);
                }
            }
        }
        /**
         * Возникли ошибки?
         */
        if ($oUser->_hasValidateErrors()) {
            /**
             * Получаем ошибки
             */
            LS::Make(ModuleViewer::class)->AssignAjax('aErrors', $oUser->_getValidateErrors());
        }
    }

    /**
     * Обработка Ajax регистрации
     */
    protected function EventAjaxRegistration()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
        if (Config::Get('reCaptcha.enabled')) {
            $recaptcha = new \ReCaptcha\ReCaptcha(Config::Get('reCaptcha.secret'));
            $recaptcha->setExpectedHostname(Config::Get('reCaptcha.expected_hostname'));
            $sCaptchaResponse = getRequest('g-recaptcha-response');

            $resp = $recaptcha->verify($sCaptchaResponse, $_SERVER['REMOTE_ADDR']);
            if (!$resp->isSuccess()) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('registration_captcha_error'));

                return;
            }
        }
        /**
         * Создаем объект пользователя и устанавливаем сценарий валидации
         */
        $oUser = new EntityUser();
        $oUser->_setValidateScenario('registration');
        /**
         * Заполняем поля (данные)
         */
        $oUser->setLogin(getRequestStr('login'));
        $oUser->setMail(getRequestStr('mail'));
        $oUser->setPassword(getRequestStr('password'));
        $oUser->setPasswordConfirm(getRequestStr('password_confirm'));
        $oUser->setDateRegister(date("Y-m-d H:i:s"));
        $oUser->setIpRegister(func_getIp());
        /**
         * Если используется активация, то генерим код активации
         */
        if (Config::Get('general.reg.activation')) {
            $oUser->setActivate(0);
            $oUser->setActivateKey(md5(func_generator().time()));
        } else {
            $oUser->setActivate(1);
            $oUser->setActivateKey(null);
        }
        LS::Make(ModuleHook::class)->Run('registration_validate_before', ['oUser' => $oUser]);
        /**
         * Запускаем валидацию
         */
        if ($oUser->_Validate()) {
            LS::Make(ModuleHook::class)->Run('registration_validate_after', ['oUser' => $oUser]);
            $oUser->setPassword(LS::Make(ModuleCrypto::class)->PasswordHash($oUser->getPassword()));
            if (LS::Make(ModuleUser::class)->Add($oUser)) {
                LS::Make(ModuleHook::class)->Run('registration_after', ['oUser' => $oUser]);
                /**
                 * Убиваем каптчу
                 */
//				unset($_SESSION['captcha_keystring']);
                /**
                 * Подписываем пользователя на дефолтные события в ленте активности
                 */
                LS::Make(ModuleStream::class)->switchUserEventDefaultTypes($oUser->getId());
                /**
                 * Если юзер зарегистрировался по приглашению то обновляем инвайт
                 */
                if (Config::Get('general.reg.invite') and
                    $oInvite = LS::Make(ModuleUser::class)->GetInviteByCode($this->GetInviteRegister())
                ) {
                    $oInvite->setUserToId($oUser->getId());
                    $oInvite->setDateUsed(date("Y-m-d H:i:s"));
                    $oInvite->setUsed(1);
                    LS::Make(ModuleUser::class)->UpdateInvite($oInvite);
                }
                /**
                 * Если стоит регистрация с активацией то проводим её
                 */
                if (Config::Get('general.reg.activation')) {
                    /**
                     * Отправляем на мыло письмо о подтверждении регистрации
                     */
                    LS::Make(ModuleNotify::class)->SendRegistrationActivate($oUser, getRequestStr('password'));
                    LS::Make(ModuleViewer::class)->AssignAjax(
                        'sUrlRedirect',
                        Router::GetPath('registration').'confirm/'
                    );
                } else {
                    LS::Make(ModuleNotify::class)->SendRegistration($oUser, getRequestStr('password'));
                    $oUser = LS::Make(ModuleUser::class)->GetUserById($oUser->getId());
                    /**
                     * Сразу авторизуем
                     */
                    LS::Make(ModuleUser::class)->Authorization($oUser, false);
                    $this->DropInviteRegister();
                    /**
                     * Определяем URL для редиректа после авторизации
                     */
                    $sUrl = Config::Get('module.user.redirect_after_registration');
                    /*if (getRequestStr('return-path')) {
                        $sUrl=getRequestStr('return-path');
                    }*/
                    LS::Make(ModuleViewer::class)->AssignAjax(
                        'sUrlRedirect',
                        $sUrl ? $sUrl : Config::Get('path.root.web')
                    );
                    LS::Make(ModuleMessage::class)->AddNoticeSingle(
                        LS::Make(ModuleLang::class)->Get('registration_ok')
                    );
                }
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));

                return;
            }
        } else {
            /**
             * Получаем ошибки
             */
            LS::Make(ModuleViewer::class)->AssignAjax('aErrors', $oUser->_getValidateErrors());
        }
    }

    /**
     * Показывает страничку регистрации
     * Просто вывод шаблона
     */
    protected function EventIndex()
    {

    }

    /**
     * Обрабатывает активацию аккаунта
     */
    protected function EventActivate()
    {
        $bError = false;
        /**
         * Проверяет передан ли код активации
         */
        $sActivateKey = $this->GetParam(0);
        if (!func_check($sActivateKey, 'md5')) {
            $bError = true;
        }
        /**
         * Проверяет верный ли код активации
         */
        if (!($oUser = LS::Make(ModuleUser::class)->GetUserByActivateKey($sActivateKey))) {
            $bError = true;
        }
        /**
         *
         */
        if ($oUser and $oUser->getActivate()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('registration_activate_error_reactivate'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Если что то не то
         */
        if ($bError) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('registration_activate_error_code'),
                LS::Make(ModuleLang::class)->Get('error')
            );
            Router::Action('error');

            return;
        }
        /**
         * Активируем
         */
        $oUser->setActivate(1);
        $oUser->setDateActivate(date("Y-m-d H:i:s"));
        /**
         * Сохраняем юзера
         */
        if (LS::Make(ModuleUser::class)->Update($oUser)) {
            $this->DropInviteRegister();
            LS::Make(ModuleViewer::class)->Assign('bRefreshToHome', true);
            LS::Make(ModuleUser::class)->Authorization($oUser, false);

            return;
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
            Router::Action('error');

            return;
        }
    }

    /**
     * Обработка кода приглашения при включеном режиме инвайтов
     *
     */
    protected function EventInvite()
    {
        if (!Config::Get('general.reg.invite')) {
            parent::EventNotFound();

            return;
        }
        /**
         * Обработка отправки формы с кодом приглашения
         */
        if (isPost('submit_invite')) {
            /**
             * проверяем код приглашения на валидность
             */
            if ($this->CheckInviteRegister()) {
                $sInviteId = $this->GetInviteRegister();
            } else {
                $sInviteId = getRequestStr('invite_code');
            }
            $oInvate = LS::Make(ModuleUser::class)->GetInviteByCode($sInviteId);
            if ($oInvate) {
                if (!$this->CheckInviteRegister()) {
                    LS::Make(ModuleSession::class)->Set('invite_code', $oInvate->getCode());
                }
                Router::Action('registration');

                return;
            } else {
                LS::Make(ModuleMessage::class)->AddError(
                    LS::Make(ModuleLang::class)->Get('registration_invite_code_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
            }
        }
    }

    /**
     * Пытается ли юзер зарегистрироваться с помощью кода приглашения
     *
     * @return bool
     */
    protected function CheckInviteRegister()
    {
        if (LS::Make(ModuleSession::class)->Get('invite_code')) {
            return true;
        }

        return false;
    }

    /**
     * Вожвращает код приглашения из сессии
     *
     * @return string
     */
    protected function GetInviteRegister()
    {
        return LS::Make(ModuleSession::class)->Get('invite_code');
    }

    /**
     * Удаляет код приглашения из сессии
     */
    protected function DropInviteRegister()
    {
        if (Config::Get('general.reg.invite')) {
            LS::Make(ModuleSession::class)->Drop('invite_code');
        }
    }

    /**
     * Просто выводит шаблон для подтверждения регистрации
     *
     */
    protected function EventConfirm()
    {
    }
}
