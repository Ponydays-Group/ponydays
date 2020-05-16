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

namespace App\Modules;

use App\Entities\EntityGeo;
use App\Entities\EntityGeoTarget;
use App\Mappers\MapperGeo;
use Engine\Engine;
use Engine\LS;
use Engine\Module;

/**
 * Модуль Geo - привязка объектов к географии (страна/регион/город)
 * Терминология:
 *        объект - который привязываем к гео-объекту
 *        гео-объект - географический объект(страна/регион/город)
 *
 * @package modules.geo
 * @since   1.0
 */
class ModuleGeo extends Module
{
    /**
     * Объект маппера
     *
     * @var MapperGeo
     */
    protected $oMapper;
    /**
     * Объект текущего пользователя
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent;
    /**
     * Список доступных типов объектов
     * На данный момент доступен параметр allow_multi=>1 - указывает на возможность создавать несколько связей для
     * одного объекта
     *
     * @var array
     */
    protected $aTargetTypes = [
        'user' => [],
    ];
    /**
     * Список доступных типов гео-объектов
     *
     * @var array
     */
    protected $aGeoTypes = [
        'country',
        'region',
        'city',
    ];

    /**
     * Инициализация
     *
     */
    public function Init()
    {
        $this->oMapper = Engine::MakeMapper(MapperGeo::class);
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
    }

    /**
     * Возвращает список типов объектов
     *
     * @return array
     */
    public function GetTargetTypes()
    {
        return $this->aTargetTypes;
    }

    /**
     * Добавляет в разрешенные новый тип
     *
     * @param string $sTargetType Тип владельца
     * @param array  $aParams     Параметры
     *
     * @return bool
     */
    public function AddTargetType($sTargetType, $aParams = [])
    {
        if (!array_key_exists($sTargetType, $this->aTargetTypes)) {
            $this->aTargetTypes[$sTargetType] = $aParams;

            return true;
        }

        return false;
    }

    /**
     * Проверяет разрешен ли данный тип
     *
     * @param string $sTargetType Тип владельца
     *
     * @return bool
     */
    public function IsAllowTargetType($sTargetType)
    {
        return in_array($sTargetType, array_keys($this->aTargetTypes));
    }

    /**
     * Проверяет разрешен ли данный гео-тип
     *
     * @param string $sGeoType Тип владельца
     *
     * @return bool
     */
    public function IsAllowGeoType($sGeoType)
    {
        return in_array($sGeoType, $this->aGeoTypes);
    }

    /**
     * Проверка объекта
     *
     * @param string $sTargetType Тип владельца
     * @param int    $iTargetId   ID владельца
     *
     * @return bool
     */
    public function CheckTarget($sTargetType, $iTargetId)
    {
        if (!$this->IsAllowTargetType($sTargetType)) {
            return false;
        }
        $sMethod = 'CheckTarget'.func_camelize($sTargetType);
        if (method_exists($this, $sMethod)) {
            return $this->$sMethod($iTargetId);
        }

        return false;
    }

    /**
     * Проверка на возможность нескольких связей
     *
     * @param string $sTargetType Тип владельца
     *
     * @return bool
     */
    public function IsAllowTargetMulti($sTargetType)
    {
        if ($this->IsAllowTargetType($sTargetType)) {
            if (isset($this->aTargetTypes[$sTargetType]['allow_multi'])
                and $this->aTargetTypes[$sTargetType]['allow_multi']
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Добавляет связь объекта с гео-объектом в БД
     *
     * @param \App\Entities\EntityGeoTarget $oTarget Объект связи с владельцем
     *
     * @return EntityGeoTarget|bool
     */
    public function AddTarget($oTarget)
    {
        if ($this->oMapper->AddTarget($oTarget)) {
            return $oTarget;
        }

        return false;
    }

    /**
     * Создание связи
     *
     * @param \App\Entities\EntityGeo $oGeoObject
     * @param string                  $sTargetType Тип владельца
     * @param int                     $iTargetId   ID владельца
     *
     * @return bool|\App\Entities\EntityGeoTarget
     */
    public function CreateTarget($oGeoObject, $sTargetType, $iTargetId)
    {
        /**
         * Проверяем объект на валидность
         */
        if (!$this->CheckTarget($sTargetType, $iTargetId)) {
            return false;
        }
        /**
         * Проверяем есть ли уже у этого объекта другие связи
         */
        $aTargets = $this->GetTargets(['target_type' => $sTargetType, 'target_id' => $iTargetId], 1, 1);
        if ($aTargets['count']) {
            if ($this->IsAllowTargetMulti($sTargetType)) {
                /**
                 * Разрешено несколько связей
                 * Проверяем есть ли уже связь с данным гео-объектом, если есть то возвращаем его
                 */
                $aTargetSelf = $this->GetTargets(
                    [
                        'target_type' => $sTargetType,
                        'target_id'   => $iTargetId,
                        'geo_type'    => $oGeoObject->getType(),
                        'geo_id'      => $oGeoObject->getId()
                    ],
                    1,
                    1
                );
                if (isset($aTargetSelf['collection'][0])) {
                    return $aTargetSelf['collection'][0];
                }
            } else {
                /**
                 * Есть другие связи и несколько связей запрещено - удаляем имеющиеся связи
                 */
                $this->DeleteTargets(['target_type' => $sTargetType, 'target_id' => $iTargetId]);
            }
        }
        /**
         * Создаем связь
         */
        $oTarget = new EntityGeoTarget();
        $oTarget->setGeoType($oGeoObject->getType());
        $oTarget->setGeoId($oGeoObject->getId());
        $oTarget->setTargetType($sTargetType);
        $oTarget->setTargetId($iTargetId);
        if ($oGeoObject->getType() == 'city') {
            $oTarget->setCountryId($oGeoObject->getCountryId());
            $oTarget->setRegionId($oGeoObject->getRegionId());
            $oTarget->setCityId($oGeoObject->getId());
        } elseif ($oGeoObject->getType() == 'region') {
            $oTarget->setCountryId($oGeoObject->getCountryId());
            $oTarget->setRegionId($oGeoObject->getId());
        } elseif ($oGeoObject->getType() == 'country') {
            $oTarget->setCountryId($oGeoObject->getId());
        }

        return $this->AddTarget($oTarget);
    }

    /**
     * Возвращает список связей по фильтру
     *
     * @param array $aFilter   Фильтр
     * @param int   $iCurrPage Номер страницы
     * @param int   $iPerPage  Количество элементов на страницу
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public function GetTargets($aFilter, $iCurrPage, $iPerPage)
    {
        return [
            'collection' => $this->oMapper->GetTargets($aFilter, $iCount, $iCurrPage, $iPerPage),
            'count'      => $iCount
        ];
    }

    /**
     * Возвращает первый объект связи по объекту
     *
     * @param string $sTargetType Тип владельца
     * @param int    $iTargetId   ID владельца
     *
     * @return null|EntityGeoTarget
     */
    public function GetTargetByTarget($sTargetType, $iTargetId)
    {
        $aTargets = $this->GetTargets(['target_type' => $sTargetType, 'target_id' => $iTargetId], 1, 1);
        if (isset($aTargets['collection'][0])) {
            return $aTargets['collection'][0];
        }

        return null;
    }

    /**
     * Возвращает список связей для списка объектов одного типа.
     *
     * @param string $sTargetType Тип владельца
     * @param array  $aTargetId   Список ID владельцев
     *
     * @return array В качестве ключей используется ID объекта, в качестве значений массив связей этого объекта
     */
    public function GetTargetsByTargetArray($sTargetType, $aTargetId)
    {
        if (!is_array($aTargetId)) {
            $aTargetId = [$aTargetId];
        }
        if (!count($aTargetId)) {
            return [];
        }
        $aResult = [];
        $aTargets = $this->GetTargets(['target_type' => $sTargetType, 'target_id' => $aTargetId], 1, count($aTargetId));
        if ($aTargets['count']) {
            foreach ($aTargets['collection'] as $oTarget) {
                $aResult[$oTarget->getTargetId()][] = $oTarget;
            }
        }

        return $aResult;
    }

    /**
     * Удаляет связи по фильтру
     *
     * @param array $aFilter Фильтр
     *
     * @return bool|int
     */
    public function DeleteTargets($aFilter)
    {
        return $this->oMapper->DeleteTargets($aFilter);
    }

    /**
     * Удаление всех связей объекта
     *
     * @param string $sTargetType Тип владельца
     * @param int    $iTargetId   ID владельца
     *
     * @return bool|int
     */
    public function DeleteTargetsByTarget($sTargetType, $iTargetId)
    {
        return $this->DeleteTargets(['target_type' => $sTargetType, 'target_id' => $iTargetId]);
    }

    /**
     * Возвращает список стран по фильтру
     *
     * @param array $aFilter   Фильтр
     * @param array $aOrder    Сортировка
     * @param int   $iCurrPage Номер страницы
     * @param int   $iPerPage  Количество элементов на страницу
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public function GetCountries($aFilter, $aOrder, $iCurrPage, $iPerPage)
    {
        return [
            'collection' => $this->oMapper->GetCountries($aFilter, $aOrder, $iCount, $iCurrPage, $iPerPage),
            'count'      => $iCount
        ];
    }

    /**
     * Возвращает список регионов по фильтру
     *
     * @param array $aFilter   Фильтр
     * @param array $aOrder    Сортировка
     * @param int   $iCurrPage Номер страницы
     * @param int   $iPerPage  Количество элементов на страницу
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public function GetRegions($aFilter, $aOrder, $iCurrPage, $iPerPage)
    {
        return [
            'collection' => $this->oMapper->GetRegions($aFilter, $aOrder, $iCount, $iCurrPage, $iPerPage),
            'count'      => $iCount
        ];
    }

    /**
     * Возвращает список городов по фильтру
     *
     * @param array $aFilter   Фильтр
     * @param array $aOrder    Сортировка
     * @param int   $iCurrPage Номер страницы
     * @param int   $iPerPage  Количество элементов на страницу
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public function GetCities($aFilter, $aOrder, $iCurrPage, $iPerPage)
    {
        return [
            'collection' => $this->oMapper->GetCities($aFilter, $aOrder, $iCount, $iCurrPage, $iPerPage),
            'count'      => $iCount
        ];
    }

    /**
     * Возвращает страну по ID
     *
     * @param int $iId ID страны
     *
     * @return \App\Entities\EntityGeoCountry|null
     */
    public function GetCountryById($iId)
    {
        $aRes = $this->GetCountries(['id' => $iId], [], 1, 1);
        if (isset($aRes['collection'][0])) {
            return $aRes['collection'][0];
        }

        return null;
    }

    /**
     * Возвращает регион по ID
     *
     * @param int $iId ID региона
     *
     * @return \App\Entities\EntityGeoRegion|null
     */
    public function GetRegionById($iId)
    {
        $aRes = $this->GetRegions(['id' => $iId], [], 1, 1);
        if (isset($aRes['collection'][0])) {
            return $aRes['collection'][0];
        }

        return null;
    }

    /**
     * Возвращает регион по ID
     *
     * @param int $iId ID города
     *
     * @return \App\Entities\EntityGeoCity|null
     */
    public function GetCityById($iId)
    {
        $aRes = $this->GetCities(['id' => $iId], [], 1, 1);
        if (isset($aRes['collection'][0])) {
            return $aRes['collection'][0];
        }

        return null;
    }

    /**
     * Возвращает гео-объект
     *
     * @param string $sType Тип гео-объекта
     * @param int    $iId   ID гео-объекта
     *
     * @return EntityGeo|null
     */
    public function GetGeoObject($sType, $iId)
    {
        $sType = strtolower($sType);
        if (!$this->IsAllowGeoType($sType)) {
            return null;
        }
        switch ($sType) {
            case 'country':
                return $this->GetCountryById($iId);
                break;
            case 'region':
                return $this->GetRegionById($iId);
                break;
            case 'city':
                return $this->GetCityById($iId);
                break;
            default:
                return null;
        }
    }

    /**
     * Возвращает первый гео-объект для объекта
     *
     * @param string $sTargetType Тип владельца
     * @param int    $iTargetId   ID владельца
     *
     * @return \App\Entities\EntityGeo|\App\Entities\EntityGeoCity|\App\Entities\EntityGeoCountry|\App\Entities\EntityGeoRegion|null
     */
    public function GetGeoObjectByTarget($sTargetType, $iTargetId)
    {
        $aTargets = $this->GetTargets(['target_type' => $sTargetType, 'target_id' => $iTargetId], 1, 1);
        if (isset($aTargets['collection'][0])) {
            $oTarget = $aTargets['collection'][0];

            return $this->GetGeoObject($oTarget->getGeoType(), $oTarget->getGeoId());
        }

        return null;
    }

    /**
     * Возвращает список стран сгруппированных по количеству использований в данном типе объектов
     *
     * @param string $sTargetType Тип владельца
     * @param int    $iLimit      Количество элементов
     *
     * @return array
     */
    public function GetGroupCountriesByTargetType($sTargetType, $iLimit)
    {
        return $this->oMapper->GetGroupCountriesByTargetType($sTargetType, $iLimit);
    }

    /**
     * Возвращает список городов сгруппированных по количеству использований в данном типе объектов
     *
     * @param string $sTargetType Тип владельца
     * @param int    $iLimit      Количество элементов
     *
     * @return array
     */
    public function GetGroupCitiesByTargetType($sTargetType, $iLimit)
    {
        return $this->oMapper->GetGroupCitiesByTargetType($sTargetType, $iLimit);
    }

    /**
     * Проверка объекта с типом "user"
     * Название метода формируется автоматически
     *
     * @param int $iTargetId ID пользователя
     *
     * @return bool
     */
    public function CheckTargetUser($iTargetId)
    {
        if ($oUser = LS::Make(ModuleUser::class)->GetUserById($iTargetId)) {
            return true;
        }

        return false;
    }
}
