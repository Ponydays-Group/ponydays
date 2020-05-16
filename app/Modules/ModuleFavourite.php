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

use App\Entities\EntityFavourite;
use App\Entities\EntityFavouriteTag;
use App\Mappers\MapperFavourite;
use Engine\Config;
use Engine\Engine;
use Engine\LS;
use Engine\Module;
use Engine\Modules\ModuleCache;
use Zend_Cache;

/**
 * Модуль для работы с избранным
 *
 * @package modules.favourite
 * @since 1.0
 */
class ModuleFavourite extends Module {
	/**
	 * Объект маппера
	 *
	 * @var \App\Mappers\MapperFavourite
	 */
	protected $oMapper;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		$this->oMapper=Engine::MakeMapper(MapperFavourite::class);
	}
	/**
	 * Получает информацию о том, найден ли таргет в избранном или нет
	 *
	 * @param  int $sTargetId	ID владельца
	 * @param  string $sTargetType	Тип владельца
	 * @param  int $sUserId	ID пользователя
	 *
	 * @return \App\Entities\EntityFavourite|null
	 */
	public function GetFavourite($sTargetId,$sTargetType,$sUserId) {
		if (!is_numeric($sTargetId) or !is_string($sTargetType)) {
			return null;
		}
		$data=$this->GetFavouritesByArray($sTargetId,$sTargetType,$sUserId);
		return (isset($data[$sTargetId]))
			? $data[$sTargetId]
			: null;
	}
	/**
	 * Получить список избранного по списку айдишников
	 *
	 * @param  array  $aTargetId	Список ID владельцев
	 * @param  string $sTargetType	Тип владельца
	 * @param  int $sUserId	ID пользователя
	 * @return array
	 */
	public function GetFavouritesByArray($aTargetId,$sTargetType,$sUserId) {
		if (!$aTargetId) {
			return array();
		}
		if (Config::Get('sys.cache.solid')) {
			return $this->GetFavouritesByArraySolid($aTargetId,$sTargetType,$sUserId);
		}
		if (!is_array($aTargetId)) {
			$aTargetId=array($aTargetId);
		}
		$aTargetId=array_unique($aTargetId);
		$aFavourite=array();
		$aIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys=func_build_cache_keys($aTargetId,"favourite_{$sTargetType}_",'_'.$sUserId);
		if (false !== ($data = LS::Make(ModuleCache::class)->Get($aCacheKeys))) {
			/**
			 * проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {
					if ($data[$sKey]) {
						$aFavourite[$data[$sKey]->getTargetId()]=$data[$sKey];
					} else {
						$aIdNotNeedQuery[]=$sValue;
					}
				}
			}
		}
		/**
		 * Смотрим чего не было в кеше и делаем запрос в БД
		 */
		$aIdNeedQuery=array_diff($aTargetId,array_keys($aFavourite));
		$aIdNeedQuery=array_diff($aIdNeedQuery,$aIdNotNeedQuery);
		$aIdNeedStore=$aIdNeedQuery;
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if ($data = $this->oMapper->GetFavouritesByArray($aIdNeedQuery,$sTargetType,$sUserId)) {
			foreach ($data as $oFavourite) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aFavourite[$oFavourite->getTargetId()]=$oFavourite;
				$cache->Set($oFavourite, "favourite_{$oFavourite->getTargetType()}_{$oFavourite->getTargetId()}_{$sUserId}", array(), 60*60*24*7);
				$aIdNeedStore=array_diff($aIdNeedStore,array($oFavourite->getTargetId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aIdNeedStore as $sId) {
			$cache->Set(null, "favourite_{$sTargetType}_{$sId}_{$sUserId}", array(), 60*60*24*7);
		}
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aFavourite=func_array_sort_by_keys($aFavourite,$aTargetId);
		return $aFavourite;
	}
	/**
	 * Получить список избранного по списку айдишников, но используя единый кеш
	 *
	 * @param  array  $aTargetId	Список ID владельцев
	 * @param  string $sTargetType	Тип владельца
	 * @param  int $sUserId	ID пользователя
	 * @return array
	 */
	public function GetFavouritesByArraySolid($aTargetId,$sTargetType,$sUserId) {
		if (!is_array($aTargetId)) {
			$aTargetId=array($aTargetId);
		}
		$aTargetId=array_unique($aTargetId);
		$aFavourites=array();
		$s=join(',',$aTargetId);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("favourite_{$sTargetType}_{$sUserId}_id_{$s}"))) {
			$data = $this->oMapper->GetFavouritesByArray($aTargetId,$sTargetType,$sUserId);
			foreach ($data as $oFavourite) {
				$aFavourites[$oFavourite->getTargetId()]=$oFavourite;
			}
			$cache->Set($aFavourites, "favourite_{$sTargetType}_{$sUserId}_id_{$s}", array("favourite_{$sTargetType}_change_user_{$sUserId}"), 60*60*24*1);
			return $aFavourites;
		}
		return $data;
	}
	/**
	 * Получает список таргетов из избранного
	 *
	 * @param  int $sUserId	ID пользователя
	 * @param  string $sTargetType	Тип владельца
	 * @param  int $iCurrPage	Номер страницы
	 * @param  int $iPerPage	Количество элементов на страницу
	 * @param  array $aExcludeTarget	Список ID владельцев для исклчения
	 * @return array
	 */
	public function GetFavouritesByUserId($sUserId,$sTargetType,$iCurrPage,$iPerPage,$aExcludeTarget=array()) {
		$s=serialize($aExcludeTarget);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("{$sTargetType}_favourite_user_{$sUserId}_{$iCurrPage}_{$iPerPage}_{$s}"))) {
			$data = array(
				'collection' => $this->oMapper->GetFavouritesByUserId($sUserId,$sTargetType,$iCount,$iCurrPage,$iPerPage,$aExcludeTarget),
				'count'      => $iCount
			);
			$cache->Set(
				$data,
				"{$sTargetType}_favourite_user_{$sUserId}_{$iCurrPage}_{$iPerPage}_{$s}",
				array(
					"favourite_{$sTargetType}_change",
					"favourite_{$sTargetType}_change_user_{$sUserId}"
				),
				60*60*24*1
			);
		}
		return $data;
	}
	/**
	 * Возвращает число таргетов определенного типа в избранном по ID пользователя
	 *
	 * @param  int $sUserId	ID пользователя
	 * @param  string $sTargetType	Тип владельца
	 * @param  array $aExcludeTarget	Список ID владельцев для исклчения
	 * @return int
	 */
	public function GetCountFavouritesByUserId($sUserId,$sTargetType,$aExcludeTarget=array()) {
		$s=serialize($aExcludeTarget);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("{$sTargetType}_count_favourite_user_{$sUserId}_{$s}"))) {
			$data = $this->oMapper->GetCountFavouritesByUserId($sUserId,$sTargetType,$aExcludeTarget);
			$cache->Set(
				$data,
				"{$sTargetType}_count_favourite_user_{$sUserId}_{$s}",
				array(
					"favourite_{$sTargetType}_change",
					"favourite_{$sTargetType}_change_user_{$sUserId}"
				),
				60*60*24*1
			);
		}
		return $data;
	}
	/**
	 * Получает список комментариев к записям открытых блогов
	 * из избранного указанного пользователя
	 *
	 * @param  int $sUserId	ID пользователя
	 * @param  int $iCurrPage	Номер страницы
	 * @param  int $iPerPage	Количество элементов на страницу
	 * @return array
	 */
	public function GetFavouriteOpenCommentsByUserId($sUserId,$iCurrPage,$iPerPage) {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("comment_favourite_user_{$sUserId}_{$iCurrPage}_{$iPerPage}_open"))) {
			$data = array(
				'collection' => $this->oMapper->GetFavouriteOpenCommentsByUserId($sUserId,$iCount,$iCurrPage,$iPerPage),
				'count'      => $iCount
			);
			$cache->Set(
				$data,
				"comment_favourite_user_{$sUserId}_{$iCurrPage}_{$iPerPage}_open",
				array(
					"favourite_comment_change",
					"favourite_comment_change_user_{$sUserId}"
				),
				60*60*24*1
			);
		}
		return $data;
	}
	/**
	 * Возвращает число комментариев к открытым блогам в избранном по ID пользователя
	 *
	 * @param  int $sUserId	ID пользователя
	 * @return int
	 */
	public function GetCountFavouriteOpenCommentsByUserId($sUserId) {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("comment_count_favourite_user_{$sUserId}_open"))) {
			$data = $this->oMapper->GetCountFavouriteOpenCommentsByUserId($sUserId);
			$cache->Set(
				$data,
				"comment_count_favourite_user_{$sUserId}_open",
				array(
					"favourite_comment_change",
					"favourite_comment_change_user_{$sUserId}"
				),
				60*60*24*1
			);
		}
		return $data;
	}
	/**
	 * Получает список топиков из открытых блогов
	 * из избранного указанного пользователя
	 *
	 * @param  int $sUserId	ID пользователя
	 * @param  int $iCurrPage	Номер страницы
	 * @param  int $iPerPage	Количество элементов на страницу
	 * @return array
	 */
	public function GetFavouriteOpenTopicsByUserId($sUserId,$iCurrPage,$iPerPage) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("topic_favourite_user_{$sUserId}_{$iCurrPage}_{$iPerPage}_open"))) {
			$data = array(
				'collection' => $this->oMapper->GetFavouriteOpenTopicsByUserId($sUserId,$iCount,$iCurrPage,$iPerPage),
				'count'      => $iCount
			);
			$cache->Set(
				$data,
				"topic_favourite_user_{$sUserId}_{$iCurrPage}_{$iPerPage}_open",
				array(
					"favourite_topic_change",
					"favourite_topic_change_user_{$sUserId}"
				),
				60*60*24*1
			);
		}
		return $data;
	}
	/**
	 * Возвращает число топиков в открытых блогах из избранного по ID пользователя
	 *
	 * @param  string $sUserId	ID пользователя
	 * @return int
	 */
	public function GetCountFavouriteOpenTopicsByUserId($sUserId) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("topic_count_favourite_user_{$sUserId}_open"))) {
			$data = $this->oMapper->GetCountFavouriteOpenTopicsByUserId($sUserId);
			$cache->Set(
				$data,
				"topic_count_favourite_user_{$sUserId}_open",
				array(
					"favourite_topic_change",
					"favourite_topic_change_user_{$sUserId}"
				),
				60*60*24*1
			);
		}
		return $data;
	}
	/**
	 * Добавляет таргет в избранное
	 *
	 * @param  \App\Entities\EntityFavourite $oFavourite Объект избранного
	 *
	 * @return bool
	 */
	public function AddFavourite(EntityFavourite $oFavourite) {
		if (!$oFavourite->getTags()) {
			$oFavourite->setTags('');
		}
		$oUser = LS::Make(ModuleUser::class)->GetUserById($oFavourite->getUserId());
		$this->SetFavouriteTags($oFavourite);
		//чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("favourite_{$oFavourite->getTargetType()}_change_user_{$oFavourite->getUserId()}"));
		$cache->Delete("favourite_{$oFavourite->getTargetType()}_{$oFavourite->getTargetId()}_{$oFavourite->getUserId()}");
		if (LS::Make(ModuleACL::class)->CanAddFavourite($oFavourite, $oUser)){
		    return $this->oMapper->AddFavourite($oFavourite);
	    } else {
	        return false;
	    }
	}
	/**
	 * Обновляет запись об избранном
	 *
	 * @param \App\Entities\EntityFavourite $oFavourite Объект избранного
	 *
	 * @return bool
	 */
	public function UpdateFavourite(EntityFavourite $oFavourite) {
		if (!$oFavourite->getTags()) {
			$oFavourite->setTags('');
		}
		$this->SetFavouriteTags($oFavourite);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("favourite_{$oFavourite->getTargetType()}_change_user_{$oFavourite->getUserId()}"));
		$cache->Delete("favourite_{$oFavourite->getTargetType()}_{$oFavourite->getTargetId()}_{$oFavourite->getUserId()}");
		return $this->oMapper->UpdateFavourite($oFavourite);
	}
	/**
	 * Устанавливает список тегов для избранного
	 *
     * @param \App\Entities\EntityFavourite $oFavourite Объект избранного
	 * @param bool                          $bAddNew    Добавлять новые теги или нет
	 */
	public function SetFavouriteTags($oFavourite,$bAddNew=true) {
		/**
		 * Удаляем все теги
		 */
		$this->oMapper->DeleteTags($oFavourite);
		/**
		 * Добавляем новые
		 */
		if ($bAddNew and $oFavourite->getTags()) {
			/**
			 * Добавляем теги объекта избранного, если есть
			 */
			if ($aTags=$this->GetTagsTarget($oFavourite->getTargetType(),$oFavourite->getTargetId())) {
				foreach($aTags as $sTag) {
					$oTag = new EntityFavouriteTag($oFavourite->_getData());
					$oTag->setText(htmlspecialchars($sTag));
					$oTag->setIsUser(0);
					$this->oMapper->AddTag($oTag);
				}
			}
			/**
			 * Добавляем пользовательские теги
			 */
			foreach($oFavourite->getTagsArray() as $sTag) {
				$oTag = new EntityFavouriteTag($oFavourite->_getData());
				$oTag->setText($sTag); // htmlspecialchars уже используется при установке тегов
				$oTag->setIsUser(1);
				$this->oMapper->AddTag($oTag);
			}
		}
	}
	/**
	 * Удаляет таргет из избранного
	 *
	 * @param  \App\Entities\EntityFavourite $oFavourite Объект избранного
	 *
	 * @return bool
	 */
	public function DeleteFavourite(EntityFavourite $oFavourite) {
		$this->SetFavouriteTags($oFavourite,false);
		//чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("favourite_{$oFavourite->getTargetType()}_change_user_{$oFavourite->getUserId()}"));
		$cache->Delete("favourite_{$oFavourite->getTargetType()}_{$oFavourite->getTargetId()}_{$oFavourite->getUserId()}");
		return $this->oMapper->DeleteFavourite($oFavourite);
	}
	/**
	 * Меняет параметры публикации у таргета
	 *
	 * @param  array|int $aTargetId	Список ID владельцев
	 * @param  string $sTargetType 	Тип владельца
	 * @param  int $iPublish	Флаг публикации
	 * @return bool
	 */
	public function SetFavouriteTargetPublish($aTargetId,$sTargetType,$iPublish) {
		if(!is_array($aTargetId)) $aTargetId = array($aTargetId);

        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("favourite_{$sTargetType}_change"));
		return $this->oMapper->SetFavouriteTargetPublish($aTargetId,$sTargetType,$iPublish);
	}
	/**
	 * Удаляет избранное по списку идентификаторов таргетов
	 *
	 * @param  array|int $aTargetId	Список ID владельцев
	 * @param  string    $sTargetType	Тип владельца
	 * @return bool
	 */
	public function DeleteFavouriteByTargetId($aTargetId, $sTargetType) {
		if(!is_array($aTargetId)) $aTargetId = array($aTargetId);
		/**
		 * Чистим зависимые кеши
		 */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("favourite_{$sTargetType}_change"));
		$this->DeleteTagByTarget($aTargetId,$sTargetType);
		return $this->oMapper->DeleteFavouriteByTargetId($aTargetId,$sTargetType);
	}
	/**
	 * Удаление тегов по таргету
	 *
	 * @param array $aTargetId	Список ID владельцев
	 * @param string $sTargetType	Тип владельца
	 * @return bool
	 */
	public function DeleteTagByTarget($aTargetId,$sTargetType) {
		return $this->oMapper->DeleteTagByTarget($aTargetId,$sTargetType);
	}
	/**
	 * Возвращает список тегов для объекта избранного
	 *
	 * @param string $sTargetType	Тип владельца
	 * @param int $iTargetId	ID владельца
	 * @return bool|array
	 */
	public function GetTagsTarget($sTargetType,$iTargetId) {
		$sMethod = 'GetTagsTarget'.func_camelize($sTargetType);
		if (method_exists($this,$sMethod)) {
			return $this->$sMethod($iTargetId);
		}
		return false;
	}
	/**
	 * Возвращает наиболее часто используемые теги
	 *
	 * @param int $iUserId	ID пользователя
	 * @param string $sTargetType	Тип владельца
	 * @param bool $bIsUser	Возвращает все теги ли только пользовательские
	 * @param int $iLimit	Количество элементов
	 * @return array
	 */
	public function GetGroupTags($iUserId,$sTargetType,$bIsUser,$iLimit) {
		return $this->oMapper->GetGroupTags($iUserId,$sTargetType,$bIsUser,$iLimit);
	}
	/**
	 * Возвращает список тегов по фильтру
	 *
	 * @param array $aFilter	Фильтр
	 * @param array $aOrder	Сортировка
	 * @param int $iCurrPage	Номер страницы
	 * @param int $iPerPage	Количество элементов на страницу
	 * @return array('collection'=>array,'count'=>int)
	 */
	public function GetTags($aFilter,$aOrder,$iCurrPage,$iPerPage) {
		return array('collection'=>$this->oMapper->GetTags($aFilter,$aOrder,$iCount,$iCurrPage,$iPerPage),'count'=>$iCount);
	}
	/**
	 * Возвращает список тегов для топика, название метода формируется автоматически из GetTagsTarget()
	 * @see GetTagsTarget
	 *
	 * @param int $iTargetId	ID владельца
	 * @return bool|array
	 */
	public function GetTagsTargetTopic($iTargetId) {
		if ($oTopic=LS::Make(ModuleTopic::class)->GetTopicById($iTargetId)) {
			return $oTopic->getTagsArray();
		}
		return false;
	}
}
