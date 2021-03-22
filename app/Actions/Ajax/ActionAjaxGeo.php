<?php

namespace App\Actions\Ajax;

use App\Modules\ModuleGeo;
use App\Modules\ModuleUser;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Result\View\AjaxView;
use Engine\Routing\Controller;

class ActionAjaxGeo extends Controller
{
    /**
     * @var \App\Entities\EntityUser
     */
    protected $currentUser = null;

    public function boot()
    {
        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);
        $this->currentUser = $user->GetUserCurrent();
    }

    /**
     * Получение списка регионов по стране
     *
     * @param \App\Modules\ModuleGeo     $geo
     *
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventGeoGetRegions(ModuleGeo $geo, ModuleLang $lang): AjaxView
    {
        $iCountryId = getRequestStr('country');
        $iLimit = 200;
        if (is_numeric(getRequest('limit')) and getRequest('limit') > 0) {
            $iLimit = getRequest('limit');
        }

        /**
         * Находим страну
         */
        if (!($oCountry = $geo->GetGeoObject('country', $iCountryId))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Получаем список регионов
         */
        $aResult = $geo->GetRegions([ 'country_id' => $oCountry->getId() ], [ 'sort' => 'asc' ], 1, $iLimit);
        $aRegions = [];
        foreach ($aResult['collection'] as $oObject) {
            $aRegions[] = [
                'id'   => $oObject->getId(),
                'name' => $oObject->getName(),
            ];
        }

        return AjaxView::from(['aRegions' => $aRegions]);
    }

    /**
     * Получение списка городов по региону
     *
     * @param \App\Modules\ModuleGeo     $geo
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventGeoGetCities(ModuleGeo $geo, ModuleLang $lang): AjaxView
    {
        $iRegionId = getRequestStr('region');
        $iLimit = 500;
        if (is_numeric(getRequest('limit')) and getRequest('limit') > 0) {
            $iLimit = getRequest('limit');
        }

        /**
         * Находим регион
         */
        if (!($oRegion = $geo->GetGeoObject('region', $iRegionId))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Получаем города
         */
        $aResult = $geo->GetCities(['region_id' => $oRegion->getId()], ['sort' => 'asc'], 1, $iLimit);
        $aCities = [];
        foreach ($aResult['collection'] as $oObject) {
            $aCities[] = [
                'id'   => $oObject->getId(),
                'name' => $oObject->getName(),
            ];
        }

        return AjaxView::from(['aCities' => $aCities]);
    }
}
