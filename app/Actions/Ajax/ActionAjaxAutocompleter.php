<?php

namespace App\Actions\Ajax;

use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Result\View\AjaxView;
use Engine\Routing\Controller;

class ActionAjaxAutocompleter extends Controller
{
    /**
     * Автоподставновка тегов
     *
     * @param \App\Modules\ModuleTopic $topic
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function EventAutocompleterTag(ModuleTopic $topic): AjaxView
    {
        /**
         * Первые буквы тега переданы?
         */
        if (!($sValue = getRequest('value', null, 'post')) or !is_string($sValue)) {
            return AjaxView::empty();
        }

        $aItems = [];
        /**
         * Формируем список тегов
         */
        $aTags = $topic->GetTopicTagsByLike($sValue, 10);
        foreach ($aTags as $oTag) {
            $aItems[] = $oTag->getText();
        }

        return AjaxView::from(['aItems' => $aItems]);
    }

    /**
     * Автоподставновка пользователей
     *
     * @param \App\Modules\ModuleUser $user
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function EventAutocompleterUser(ModuleUser $user)
    {
        /**
         * Первые буквы логина переданы?
         */
        if (!($sValue = getRequest('value', null, 'post')) or !is_string($sValue)) {
            return AjaxView::empty();
        }

        $aItems = [];
        /**
         * Формируем список пользователей
         */
        $aUsers = $user->GetUsersByLoginLike($sValue, 10);
        foreach ($aUsers as $oUser) {
            $aItems[] = $oUser->getLogin();
        }

        return AjaxView::from(['aItems' => $aItems]);
    }
}
