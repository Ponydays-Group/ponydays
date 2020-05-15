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

use App\Entities\EntityFeedbacksAction;
use App\Entities\EntityVote;
use App\Mappers\MapperVote;
use Engine\Config;
use Engine\Engine;
use Engine\LS;
use Engine\Module;
use Engine\Modules\ModuleCache;
use Zend_Cache;

/**
 * Модуль для работы с голосованиями
 *
 * @package modules.vote
 * @since 1.0
 */
class ModuleVote extends Module {
	/**
	 * Объект маппера
	 *
	 * @var MapperVote
	 */
	protected $oMapper;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		$this->oMapper=Engine::MakeMapper(MapperVote::class);
	}
	/**
	 * Добавляет голосование
	 *
	 * @param EntityVote $oVote Объект голосования
	 *
	 * @return bool
	 */
	public function AddVote(EntityVote $oVote){
			if( ! $oVote->getIp() ){
				$oVote->setIp(func_getIp());
			}
			if( $this->oMapper->AddVote($oVote) ){
                /** @var ModuleCache $cache */
                $cache = LS::Make(ModuleCache::class);
				$cache->Delete("vote_{$oVote->getTargetType()}_{$oVote->getTargetId()}_{$oVote->getVoterId()}");
				$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("vote_update_{$oVote->getTargetType()}_{$oVote->getVoterId()}"));

				if( in_array($oVote->getTargetType(), array('topic', 'comment', 'user')) ){
						$oAction = new EntityFeedbacksAction();
						$oAction->setUserIdFrom($oVote->getVoterId());
						$oAction->setId(null);
						$oAction->setAddDatetime(time());
						$oAction->setDestinationObjectId($oVote->getTargetId());

						if($oVote->getTargetType() == 'topic'){
							$oTopic = LS::Make(ModuleTopic::class)->GetTopicById($oVote->getTargetId());
							$oAction->setUserIdTo($oTopic->getUserId());

							if($oVote->getDirection() > 0)
								$oAction->setActionType('VoteTopic');
							if($oVote->getDirection() < 0)
								$oAction->setActionType('VoteDownTopic');
							if($oVote->getDirection() == 0)
								$oAction->setActionType('VoteAbstainTopic');
						}

						if($oVote->getTargetType() == 'comment'){
							$oComment	= LS::Make(ModuleComment::class)->GetCommentById($oVote->getTargetId());
							$oAction->setUserIdTo($oComment->getUserId());
							if($oVote->getDirection() > 0)
								$oAction->setActionType('VoteComment');
							else
								$oAction->setActionType('VoteDownComment');
						}

					if($oVote->getTargetType() == 'user'){
							$oAction->setUserIdTo($oVote->getTargetId());
							if($oVote->getDirection() > 0)
								$oAction->setActionType('VoteUser');
							else
								$oAction->setActionType('VoteDownUser');
						}
                        return true; //FIXME: Unreachable statement
						LS::Make(ModuleFeedbacks::class)->SaveAction($oAction);
				}

				return true;
			}

			return false;
		}

	/**
	 * Получает голосование
	 *
	 * @param int $sTargetId	ID владельца
	 * @param string $sTargetType	Тип владельца
	 * @param int $sUserId	ID пользователя
	 *
	 * @return \App\Entities\EntityVote|null
	 */
	public function GetVote($sTargetId,$sTargetType,$sUserId) {
		$data=$this->GetVoteByArray($sTargetId,$sTargetType,$sUserId);
		if (isset($data[$sTargetId])) {
			return $data[$sTargetId];
		}
		return null;
	}
	/**
	 * Получить список голосований по списку айдишников
	 *
	 * @param array $aTargetId	Список ID владельцев
	 * @param string $sTargetType	Тип владельца
	 * @param int $sUserId	ID пользователя
	 * @return array
	 */
	public function GetVoteByArray($aTargetId,$sTargetType,$sUserId) {
		if (!$aTargetId) {
			return array();
		}
		if (Config::Get('sys.cache.solid')) {
			return $this->GetVoteByArraySolid($aTargetId,$sTargetType,$sUserId);
		}
		if (!is_array($aTargetId)) {
			$aTargetId=array($aTargetId);
		}
		$aTargetId=array_unique($aTargetId);
		$aVote=array();
		$aIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$aCacheKeys=func_build_cache_keys($aTargetId,"vote_{$sTargetType}_",'_'.$sUserId);
		if (false !== ($data = $cache->Get($aCacheKeys))) {
			/**
			 * проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {
					if ($data[$sKey]) {
						$aVote[$data[$sKey]->getTargetId()]=$data[$sKey];
					} else {
						$aIdNotNeedQuery[]=$sValue;
					}
				}
			}
		}
		/**
		 * Смотрим каких топиков не было в кеше и делаем запрос в БД
		 */
		$aIdNeedQuery=array_diff($aTargetId,array_keys($aVote));
		$aIdNeedQuery=array_diff($aIdNeedQuery,$aIdNotNeedQuery);
		$aIdNeedStore=$aIdNeedQuery;
		if ($data = $this->oMapper->GetVoteByArray($aIdNeedQuery,$sTargetType,$sUserId)) {
			foreach ($data as $oVote) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aVote[$oVote->getTargetId()]=$oVote;
				$cache->Set($oVote, "vote_{$oVote->getTargetType()}_{$oVote->getTargetId()}_{$oVote->getVoterId()}", array(), 60*60*24*7);
				$aIdNeedStore=array_diff($aIdNeedStore,array($oVote->getTargetId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aIdNeedStore as $sId) {
			$cache->Set(null, "vote_{$sTargetType}_{$sId}_{$sUserId}", array(), 60*60*24*7);
		}
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aVote=func_array_sort_by_keys($aVote,$aTargetId);
		return $aVote;
	}

	public function GetVoteById($sId,$sTargetType) {
		if (!$sId) {
			return array();
		}
		$data = $this->oMapper->GetVoteById($sId,$sTargetType);
		return $data;
	}
	/**
	 * Получить список голосований по списку айдишников, но используя единый кеш
	 *
	 * @param array $aTargetId	Список ID владельцев
	 * @param string $sTargetType	Тип владельца
	 * @param int $sUserId	ID пользователя
	 * @return array
	 */
	public function GetVoteByArraySolid($aTargetId,$sTargetType,$sUserId) {
		if (!is_array($aTargetId)) {
			$aTargetId=array($aTargetId);
		}
		$aTargetId=array_unique($aTargetId);
		$aVote=array();
		$s=join(',',$aTargetId);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("vote_{$sTargetType}_{$sUserId}_id_{$s}"))) {
			$data = $this->oMapper->GetVoteByArray($aTargetId,$sTargetType,$sUserId);
			foreach ($data as $oVote) {
				$aVote[$oVote->getTargetId()]=$oVote;
			}
			$cache->Set(
				$aVote, "vote_{$sTargetType}_{$sUserId}_id_{$s}",
				array("vote_update_{$sTargetType}_{$sUserId}","vote_update_{$sTargetType}"),
				60*60*24*1
			);
			return $aVote;
		}
		return $data;
	}
	/**
	 * Удаляет голосование из базы по списку идентификаторов таргета
	 *
	 * @param  array|int $aTargetId	Список ID владельцев
	 * @param  string    $sTargetType	Тип владельца
	 * @return bool
	 */
	public function DeleteVoteByTarget($aTargetId, $sTargetType) {
		if (!is_array($aTargetId)) $aTargetId=array($aTargetId);
		$aTargetId=array_unique($aTargetId);
		/**
		 * Чистим зависимые кеши
		 */
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("vote_update_{$sTargetType}"));
		return $this->oMapper->DeleteVoteByTarget($aTargetId,$sTargetType);
	}
	public function DeleteVote($sTargetId, $sTargetType, $sUserId) {
		/**
		 * Чистим зависимые кеши
		 */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("vote_update_{$sTargetType}"));
		return $this->oMapper->DeleteVote($sTargetId,$sTargetType,$sUserId);
	}
}
