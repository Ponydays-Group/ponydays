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

use App\Entities\EntityUserReminder;
use App\Modules\ModuleCrypto;
use App\Modules\ModuleNotify;
use App\Modules\ModuleUser;
use Engine\Config;
use Engine\LS;
use Engine\Module;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleSecurity;
use Engine\Modules\ModuleSession;
use Engine\Modules\ModuleViewer;
use Engine\Result\Redirect;
use Engine\Result\Result;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Result\View\JsonView;
use Engine\Router;
use Engine\Routing\Controller;
use Engine\Routing\Exception\Http\BadRequestHttpException;
use Engine\Routing\Exception\Http\ForbiddenHttpException;

/**
 * Обрабатывые авторизацию
 *
 * @package actions
 * @since   1.0
 */
class ActionLogin extends Controller
{

    /**
     * Ajax авторизация
     *
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     * @param \App\Modules\ModuleUser       $user
     * @param \Engine\Modules\ModuleSession $session
     * @param \App\Modules\ModuleCrypto     $crypto
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function ajaxLogin(ModuleMessage $message, ModuleLang $lang, ModuleUser $user, ModuleSession $session, ModuleCrypto $crypto): JsonView
    {
        /**
         * Логин и пароль являются строками?
         */
        if (!is_string(getRequest('login')) or !is_string(getRequest('password'))) {
            $message->AddErrorSingle($lang->Get('system_error'));

            return AjaxView::empty();
        }
        /**
         * Проверяем есть ли такой юзер по логину
         */
        if ((func_check(getRequest('login'), 'mail')
            and $oUser = $user->GetUserByMail(getRequest('login')))
            or $oUser = $user->GetUserByLogin(getRequest('login'))
        ) {
            // проверка на бан

//			if (LS::Make(ModuleUser::class)->isBanned($oUser->getId())) {
            if ($oUser->isBanned()) {
                $message->AddNoticeSingle($oUser->getBanComment());
                $user->Logout();
                $session->DropSession();

                throw new ForbiddenHttpException();
            }

            /**
             * Проверяем пароль и обновляем хеш, если нужно
             */
            $user_password = $oUser->getPassword();
            if ($crypto->PasswordVerify(getRequest('password'), $user_password)) {
                if ($crypto->PasswordNeedsRehash($user_password)) {
                    $oUser->setPassword($crypto->PasswordHash(getRequest('password')));
                    $user->Update($oUser);
                }

                /**
                 * Проверяем активен ли юзер
                 */
                if (!$oUser->getActivate()) {
                    $message->AddErrorSingle(
                        $lang->Get(
                            'user_not_activated',
                            ['reactivation_path' => Router::GetPath('login').'reactivation']
                        )
                    );

                    return AjaxView::empty();
                }
                $bRemember = getRequest('remember', false) ? true : false;
                /**
                 * Авторизуем
                 */
                $user->Authorization($oUser, $bRemember);
                /**
                 * Определяем редирект
                 */
                $sUrl = Config::Get('module.user.redirect_after_login');
                if (getRequestStr('return-path')) {
                    $sUrl = getRequestStr('return-path');
                }

                return AjaxView::from([
                    'sUrlRedirect' => $sUrl ? $sUrl : Config::Get('path.root.web'),
                    'sKey' => $user->GenerateUserKey($oUser)
                ]);
            }
        }
        $message->AddErrorSingle($lang->Get('user_login_bad'));

        return AjaxView::empty();
    }

    /**
     * Повторный запрос активации
     *
     * @param \App\Modules\ModuleUser    $user
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\Redirect|\Engine\Result\View\HtmlView
     */
    protected function reactivation(ModuleUser $user, ModuleLang $lang)
    {
        if ($user->GetUserCurrent()) {
            return Redirect::to(Config::Get('path.root.web').'/');
        }

        return HtmlView::by('login/reactivation')->withHtmlTitle($lang->Get('reactivation'));
    }

    /**
     *  Ajax повторной активации
     *
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     * @param \App\Modules\ModuleUser       $user
     * @param \App\Modules\ModuleNotify     $notify
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function ajaxReactivation(ModuleMessage $message, ModuleLang $lang, ModuleUser $user, ModuleNotify $notify): JsonView
    {
        if ((func_check(getRequestStr('mail'), 'mail') and $oUser = $user->GetUserByMail(getRequestStr('mail')))) {
            if ($oUser->getActivate()) {
                $message->AddErrorSingle($lang->Get('registration_activate_error_reactivate'));

                return AjaxView::empty();
            } else {
                $oUser->setActivateKey(md5(func_generator().time()));
                if ($user->Update($oUser)) {
                    $message->AddNotice($lang->Get('reactivation_send_link'));
                    $notify->SendReactivationCode($oUser);

                    return AjaxView::empty();
                }
            }
        }

        $message->AddErrorSingle($lang->Get('password_reminder_bad_email'));

        return AjaxView::empty();
    }

    /**
     * Обрабатываем процесс залогинивания
     * По факту только отображение шаблона, дальше вступает в дело Ajax
     *
     * @param \App\Modules\ModuleUser    $user
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\Result
     */
    protected function login(ModuleUser $user, ModuleLang $lang): Result
    {
        /**
         * Если уже авторизирован
         */
        if ($user->GetUserCurrent()) {
            return Redirect::to(Config::Get('path.root.web').'/');
        }

        return HtmlView::by('login/index')->withHtmlTitle($lang->Get('login'));
    }

    /**
     * Обрабатываем процесс разлогинивания
     *
     * @param \Engine\Modules\ModuleSecurity $security
     * @param \App\Modules\ModuleUser        $user
     *
     * @return \Engine\Result\View\HtmlView
     */
    protected function exit(ModuleSecurity $security, ModuleUser $user): HtmlView
    {
        $security->ValidateSendForm();
        $user->Logout();

        return HtmlView::by('login/exit')->with(['bRefreshToHome' => true]);
    }

    /**
     * Ajax запрос на восстановление пароля
     */
    protected function ajaxReminder(ModuleUser $user, ModuleMessage $message, ModuleLang $lang, ModuleNotify $notify): JsonView
    {
        /**
         * Пользователь с таким емайлом существует?
         */
        if ((func_check(getRequestStr('mail'), 'mail') and $oUser = $user->GetUserByMail(getRequestStr('mail')))) {
            /**
             * Формируем и отправляем ссылку на смену пароля
             */
            $oReminder = new EntityUserReminder();
            $oReminder->setCode(func_generator(32));
            $oReminder->setDateAdd(date("Y-m-d H:i:s"));
            $oReminder->setDateExpire(date("Y-m-d H:i:s", time() + 60 * 60 * 24 * 7));
            $oReminder->setDateUsed(null);
            $oReminder->setIsUsed(0);
            $oReminder->setUserId($oUser->getId());
            if ($user->AddReminder($oReminder)) {
                $message->AddNotice($lang->Get('password_reminder_send_link'));
                $notify->SendReminderCode($oUser, $oReminder);

                return AjaxView::empty();
            }
        }
        $message->AddError($lang->Get('password_reminder_bad_email'), $lang->Get('error'));

        return AjaxView::empty();
    }

    /**
     * Обработка напоминания пароля, подтверждение смены пароля
     *
     * @param \App\Modules\ModuleUser       $user
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     * @param \App\Modules\ModuleNotify     $notify
     * @param \App\Modules\ModuleCrypto     $crypto
     * @param string                        $key
     */
    protected function reminder(ModuleUser $user, ModuleMessage $message, ModuleLang $lang, ModuleNotify $notify, ModuleCrypto $crypto, string $key)
    {
        /**
         * Проверка кода на восстановление пароля и генерация нового пароля
         */
        if (func_check($key, 'md5')) {
            /**
             * Проверка кода подтверждения
             */
            if ($oReminder = $user->GetReminderByCode($key)) {
                if (!$oReminder->getIsUsed() and strtotime($oReminder->getDateExpire()) > time() and $oUser = $user->GetUserById($oReminder->getUserId())) {
                    $sNewPassword = func_generator(16);
                    $oUser->setPassword($crypto->PasswordHash($sNewPassword));
                    if ($user->Update($oUser)) {
                        $oReminder->setDateUsed(date("Y-m-d H:i:s"));
                        $oReminder->setIsUsed(1);
                        $user->UpdateReminder($oReminder);
                        $notify->SendReminderPassword($oUser, $sNewPassword);

                        return HtmlView::by('login/reminder_confirm')->withHtmlTitle($lang->Get('password_reminder'));
                    }
                }
            }

            $message->AddErrorSingle(
                $lang->Get('password_reminder_bad_code'),
                $lang->Get('error')
            );
        }

        throw new BadRequestHttpException();
    }
}
