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

use App\Entities\EntityComment;
use App\Entities\EntityCommentOnline;
use App\Mappers\MapperComment;
use App\Entities\EntityFavourite;
use App\Entities\EntityUser;
use Engine\Config;
use Engine\Engine;
use Engine\LS;
use Engine\Module;
use Engine\Modules\ModuleCache;
use Engine\Modules\ModuleText;
use Engine\Modules\ModuleViewer;
use Zend_Cache;

/**
 * Модуль для работы с комментариями
 *
 * @package modules.comment
 * @since 1.0
 */
class ModuleComment extends Module {
	/**
	 * Объект маппера
	 *
	 * @var \App\Mappers\MapperComment
	 */
	protected $oMapper;
	/**
	 * Объект текущего пользователя
	 *
	 * @var \App\Modules\User\EntityUser|null
	 */
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		$this->oMapper=Engine::MakeMapper(MapperComment::class);
		$this->oUserCurrent=LS::Make(ModuleUser::class)->GetUserCurrent();
	}
	/**
	 * Получить коммент по айдишнику
	 *
	 * @param int $sId	ID комментария
	 *
	 * @return \App\Entities\EntityComment|null
	 */
	public function GetCommentById($sId) {
		if (!is_numeric($sId)) {
			return null;
		}
		$aComments=$this->GetCommentsAdditionalData($sId);
		if (isset($aComments[$sId])) {
			return $aComments[$sId];
		}
		return null;
	}
	/**
	 * Получает уникальный коммент, это помогает спастись от дублей комментов
	 *
	 * @param int $sTargetId	ID владельца комментария
	 * @param string $sTargetType	Тип владельца комментария
	 * @param int $sUserId	ID пользователя
	 * @param int $sCommentPid	ID родительского комментария
	 * @param string $sHash	Хеш строка текста комментария
	 *
	 * @return EntityComment|null
	 */
	public function GetCommentUnique($sTargetId,$sTargetType,$sUserId,$sCommentPid,$sHash) {
		$sId=$this->oMapper->GetCommentUnique($sTargetId,$sTargetType,$sUserId,$sCommentPid,$sHash);
		return $this->GetCommentById($sId);
	}
	/**
	 * Получить все комменты
	 *
	 * @param string $sTargetType	Тип владельца комментария
	 * @param int $iPage	Номер страницы
	 * @param int $iPerPage	Количество элементов на страницу
	 * @param array $aExcludeTarget	Список ID владельцев, которые необходимо исключить из выдачи
	 * @param array $aExcludeParentTarget	Список ID родителей владельцев, которые необходимо исключить из выдачи, например, исключить комментарии топиков к определенным блогам(закрытым)
	 * @return array('collection'=>array,'count'=>int)
	 */
	public function GetCommentsAll($sTargetType,$iPage,$iPerPage,$aExcludeTarget=array(),$aExcludeParentTarget=array()) {
		$s=serialize($aExcludeTarget).serialize($aExcludeParentTarget);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("comment_all_{$sTargetType}_{$iPage}_{$iPerPage}_{$s}"))) {
			$data = array('collection'=>$this->oMapper->GetCommentsAll($sTargetType,$iCount,$iPage,$iPerPage,$aExcludeTarget,$aExcludeParentTarget),'count'=>$iCount);
			$cache->Set($data, "comment_all_{$sTargetType}_{$iPage}_{$iPerPage}_{$s}", array("comment_new_{$sTargetType}","comment_update_status_{$sTargetType}"), 60*60*24*1);
		}
		$data['collection']=$this->GetCommentsAdditionalData($data['collection'],array('target','favourite','user'=>array()));
		return $data;
	}
	/**
	 * Получает дополнительные данные(объекты) для комментов по их ID
	 *
	 * @param array $aCommentId	Список ID комментов
	 * @param array|null $aAllowData	Список типов дополнительных данных, которые нужно получить для комментариев
	 * @return array
	 */
	public function GetCommentsAdditionalData($aCommentId,$aAllowData=null) {
		if (is_null($aAllowData)) {
			$aAllowData=array('vote','target','favourite','user'=>array(),'delete_reason','delete_user_id');
		}
		func_array_simpleflip($aAllowData);
		if (!is_array($aCommentId)) {
			$aCommentId=array($aCommentId);
		}
		/**
		 * Получаем комменты
		 */
		$aComments=$this->GetCommentsByArrayId($aCommentId);
		/**
		 * Формируем ID дополнительных данных, которые нужно получить
		 */
		$aUserId=array();
		$aTargetId=array('topic'=>array(),'talk'=>array());
		foreach ($aComments as $oComment) {
			if (isset($aAllowData['user'])) {
				$aUserId[]=$oComment->getUserId();
			}
			if (isset($aAllowData['target'])) {
				$aTargetId[$oComment->getTargetType()][]=$oComment->getTargetId();
			}
		}
		/**
		 * Получаем дополнительные данные
		 */
		$aUsers=isset($aAllowData['user']) && is_array($aAllowData['user']) ? LS::Make(ModuleUser::class)->GetUsersAdditionalData($aUserId,$aAllowData['user']) : LS::Make(ModuleUser::class)->GetUsersAdditionalData($aUserId);
		/**
		 * В зависимости от типа target_type достаем данные
		 */
		$aTargets=array();
		//$aTargets['topic']=isset($aAllowData['target']) && is_array($aAllowData['target']) ? LS::Make(ModuleTopic::class)->GetTopicsAdditionalData($aTargetId['topic'],$aAllowData['target']) : LS::Make(ModuleTopic::class)->GetTopicsAdditionalData($aTargetId['topic']);
		$aTargets['topic']=LS::Make(ModuleTopic::class)->GetTopicsAdditionalData($aTargetId['topic'],array('blog'=>array('owner'=>array()),'user'=>array(), 'comment_new'));
		$aVote=array();
		if (isset($aAllowData['vote']) and $this->oUserCurrent) {
			$aVote=LS::Make(ModuleVote::class)->GetVoteByArray($aCommentId,'comment',$this->oUserCurrent->getId());
		}
		if (isset($aAllowData['favourite']) and $this->oUserCurrent) {
			$aFavouriteComments=LS::Make(ModuleFavourite::class)->GetFavouritesByArray($aCommentId,'comment',$this->oUserCurrent->getId());
		}
		/**
		 * Добавляем данные к результату
		 */
		foreach ($aComments as $oComment) {
			if (isset($aUsers[$oComment->getUserId()])) {
				$oComment->setUser($aUsers[$oComment->getUserId()]);
				if ($oComment->getDelete()) {
					$oComment->setUserDelete(LS::Make(ModuleUser::class)->GetUserById($oComment->getDeleteUserId()));
				}
			} else {
				$oComment->setUser(null); // или $oComment->setUser(new ModuleUser_EntityUser());
			}
			if (isset($aTargets[$oComment->getTargetType()][$oComment->getTargetId()])) {
				$oComment->setTarget($aTargets[$oComment->getTargetType()][$oComment->getTargetId()]);
			} else {
				$oComment->setTarget(null);
			}
			if (isset($aVote[$oComment->getId()])) {
				$oComment->setVote($aVote[$oComment->getId()]);
			} else {
				$oComment->setVote(null);
			}
			if (isset($aFavouriteComments[$oComment->getId()])) {
				$oComment->setIsFavourite(true);
			} else {
				$oComment->setIsFavourite(false);
			}
		}
		return $aComments;
	}
	/**
	 * Список комментов по ID
	 *
	 * @param array $aCommentId	Список ID комментариев
	 * @return array
	 */
	public function GetCommentsByArrayId($aCommentId) {
		if (!$aCommentId) {
			return array();
		}
		if (Config::Get('sys.cache.solid')) {
			return $this->GetCommentsByArrayIdSolid($aCommentId);
		}
		if (!is_array($aCommentId)) {
			$aCommentId=array($aCommentId);
		}
		$aCommentId=array_unique($aCommentId);
		$aComments=array();
		$aCommentIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys=func_build_cache_keys($aCommentId,'comment_');
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false !== ($data = $cache->Get($aCacheKeys))) {
			/**
			 * Проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {
					if ($data[$sKey]) {
						$aComments[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aCommentIdNotNeedQuery[]=$sValue;
					}
				}
			}
		}
		/**
		 * Смотрим каких комментов не было в кеше и делаем запрос в БД
		 */
		$aCommentIdNeedQuery=array_diff($aCommentId,array_keys($aComments));
		$aCommentIdNeedQuery=array_diff($aCommentIdNeedQuery,$aCommentIdNotNeedQuery);
		$aCommentIdNeedStore=$aCommentIdNeedQuery;
		if ($data = $this->oMapper->GetCommentsByArrayId($aCommentIdNeedQuery)) {
			foreach ($data as $oComment) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aComments[$oComment->getId()]=$oComment;
				$cache->Set($oComment, "comment_{$oComment->getId()}", array(), 60*60*24*4);
				$aCommentIdNeedStore=array_diff($aCommentIdNeedStore,array($oComment->getId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aCommentIdNeedStore as $sId) {
			$cache->Set(null, "comment_{$sId}", array(), 60*60*24*4);
		}
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		$aComments=func_array_sort_by_keys($aComments,$aCommentId);
		return $aComments;
	}
	public function GetCommentsOlderThenEdited($sTargetType, $iTargetId, $iCommentId) {
		return $this->GetCommentsByArrayId($this->oMapper->GetCommentsOlderThenEdited($sTargetType, $iTargetId, $iCommentId));
	}
	/**
	 * Получает список комментариев по ID используя единый кеш
	 *
	 * @param array $aCommentId Список ID комментариев
	 * @return array
	 */
	public function GetCommentsByArrayIdSolid($aCommentId) {
		if (!is_array($aCommentId)) {
			$aCommentId=array($aCommentId);
		}
		$aCommentId=array_unique($aCommentId);
		$aComments=array();
		$s=join(',',$aCommentId);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("comment_id_{$s}"))) {
			$data = $this->oMapper->GetCommentsByArrayId($aCommentId);
			foreach ($data as $oComment) {
				$aComments[$oComment->getId()]=$oComment;
			}
			$cache->Set($aComments, "comment_id_{$s}", array("comment_update"), 60*60*24*1);
			return $aComments;
		}
		return $data;
	}
	/**
	 * Получить все комменты сгрупированные по типу(для вывода прямого эфира)
	 *
	 * @param string $sTargetType	Тип владельца комментария
	 * @param int $iLimit	Количество элементов
	 * @return array
	 */
	public function GetCommentsOnline($sTargetType,$iLimit) {
		/**
		 * Исключаем из выборки идентификаторы закрытых блогов (target_parent_id)
		 */
        /** @var \App\Modules\ModuleBlog $blog */
        $blog = LS::Make(ModuleBlog::class);
		$aCloseBlogs = ($this->oUserCurrent)
			? $blog->GetInaccessibleBlogsByUser($this->oUserCurrent)
			: $blog->GetInaccessibleBlogsByUser();

		$data = $this->oMapper->GetCommentsOnline($sTargetType,$aCloseBlogs,$iLimit);
		$data=$this->GetCommentsAdditionalData($data);
		return $data;
	}
	/**
	 * Получить комменты по юзеру
	 *
	 * @param  int $sId	ID пользователя
	 * @param  string $sTargetType	Тип владельца комментария
	 * @param  int    $iPage	Номер страницы
	 * @param  int    $iPerPage	Количество элементов на страницу
	 * @return array
	 */
	public function GetCommentsByUserId($sId,$sTargetType,$iPage,$iPerPage) {
		/**
		 * Исключаем из выборки идентификаторы закрытых блогов
		 */
		$aCloseBlogs = ($this->oUserCurrent && $sId==$this->oUserCurrent->getId())
			? array()
			: LS::Make(ModuleBlog::class)->GetInaccessibleBlogsByUser($this->oUserCurrent);
		$s=serialize($aCloseBlogs);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("comment_user_{$sId}_{$sTargetType}_{$iPage}_{$iPerPage}_{$s}"))) {
			$data = array('collection'=>$this->oMapper->GetCommentsByUserId($sId,$sTargetType,$iCount,$iPage,$iPerPage,array(),$aCloseBlogs),'count'=>$iCount);
			$cache->Set($data, "comment_user_{$sId}_{$sTargetType}_{$iPage}_{$iPerPage}_{$s}", array("comment_new_user_{$sId}_{$sTargetType}","comment_update_status_{$sTargetType}"), 60*60*24*2);
		}
		$data['collection']=$this->GetCommentsAdditionalData($data['collection']);
		return $data;
	}
	/**
	 * Получает количество комментариев одного пользователя
	 *
	 * @param  int $sId ID пользователя
	 * @param  string $sTargetType	Тип владельца комментария
	 * @return int
	 */
	public function GetCountCommentsByUserId($sId,$sTargetType) {
		/**
		 * Исключаем из выборки идентификаторы закрытых блогов
		 */
		/*$aCloseBlogs = ($this->oUserCurrent && $sId==$this->oUserCurrent->getId())
			? array()
			: LS::Make(ModuleBlog::class)->GetInaccessibleBlogsByUser();*/
		$s=serialize(array());
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("comment_count_user_{$sId}_{$sTargetType}_{$s}"))) {
			$data = $this->oMapper->GetCountCommentsByUserId($sId,$sTargetType,array(),array());
			$cache->Set($data, "comment_count_user_{$sId}_{$sTargetType}", array("comment_new_user_{$sId}_{$sTargetType}","comment_update_status_{$sTargetType}"), 60*60*24*2);
		}
		return $data;
	}
	/**
	 * Получить комменты по рейтингу и дате
	 *
	 * @param  string $sDate	Дата за которую выводить рейтинг, т.к. кеширование происходит по дате, то дату лучше передавать с точностью до часа (минуты и секунды как 00:00)
	 * @param  string $sTargetType	Тип владельца комментария
	 * @param  int    $iLimit	Количество элементов
	 * @return array
	 */
	public function GetCommentsRatingByDate($sDate,$sTargetType,$iLimit=20) {
		/**
		 * Выбираем топики, комметарии к которым являются недоступными для пользователя
		 */
        /** @var \App\Modules\ModuleBlog $blog */
        $blog = LS::Make(ModuleBlog::class);
		$aCloseBlogs = ($this->oUserCurrent)
			? $blog->GetInaccessibleBlogsByUser($this->oUserCurrent)
			: $blog->GetInaccessibleBlogsByUser();
		$s=serialize($aCloseBlogs);
		/**
		 * Т.к. время передаётся с точностью 1 час то можно по нему замутить кеширование
		 */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($data = $cache->Get("comment_rating_{$sDate}_{$sTargetType}_{$iLimit}_{$s}"))) {
			$data = $this->oMapper->GetCommentsRatingByDate($sDate,$sTargetType,$iLimit,array(),$aCloseBlogs);
			$cache->Set($data, "comment_rating_{$sDate}_{$sTargetType}_{$iLimit}_{$s}", array("comment_new_{$sTargetType}","comment_update_status_{$sTargetType}","comment_update_rating_{$sTargetType}"), 60*60*24*2);
		}
		$data=$this->GetCommentsAdditionalData($data);
		return $data;
	}
	/**
	 * Получить комменты по владельцу
	 *
	 * @param  int $sId	ID владельца коммента
	 * @param  string $sTargetType	Тип владельца комментария
	 * @param  int $iPage	Номер страницы
	 * @param  int $iPerPage	Количество элементов на страницу
	 * @return array('comments'=>array,'iMaxIdComment'=>int)
	 */
	public function GetCommentsByTargetId($sId,$sTargetType,$iPage=1,$iPerPage=0) {
		if (Config::Get('module.comment.use_nested')) {
			return $this->GetCommentsTreeByTargetId($sId,$sTargetType,$iPage,$iPerPage);
		}

        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($aCommentsRec = $cache->Get("comment_target_{$sId}_{$sTargetType}"))) {
			$aCommentsRow=$this->oMapper->GetCommentsByTargetId($sId,$sTargetType);
			if (count($aCommentsRow)) {
				$aCommentsRec=$this->BuildCommentsRecursive($aCommentsRow);
			}
			$cache->Set($aCommentsRec, "comment_target_{$sId}_{$sTargetType}", array("comment_new_{$sTargetType}_{$sId}"), 60*60*24*2);
		}
		if (!isset($aCommentsRec['comments'])) {
			return array('comments'=>array(),'iMaxIdComment'=>0);
		}
		$aComments=$aCommentsRec;
		$aComments['comments']=$this->GetCommentsAdditionalData(array_keys($aCommentsRec['comments']));
		foreach ($aComments['comments'] as $oComment) {
			$oComment->setLevel($aCommentsRec['comments'][$oComment->getId()]);
		}
		return $aComments;

	}
	/**
	 * Получает комменты используя nested set
	 *
	 * @param int $sId	ID владельца коммента
	 * @param string $sTargetType	Тип владельца комментария
	 * @param  int $iPage	Номер страницы
	 * @param  int $iPerPage	Количество элементов на страницу
	 * @return array('comments'=>array,'iMaxIdComment'=>int,'count'=>int)
	 */
	public function GetCommentsTreeByTargetId($sId,$sTargetType,$iPage=1,$iPerPage=0) {
		if (!Config::Get('module.comment.nested_page_reverse') and $iPerPage and $iCountPage=ceil($this->GetCountCommentsRootByTargetId($sId,$sTargetType)/$iPerPage)) {
			$iPage=$iCountPage-$iPage+1;
		}
		$iPage=$iPage<1 ? 1 : $iPage;
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($aReturn = $cache->Get("comment_tree_target_{$sId}_{$sTargetType}_{$iPage}_{$iPerPage}"))) {

			/**
			 * Нужно или нет использовать постраничное разбиение комментариев
			 */
			if ($iPerPage) {
				$aComments=$this->oMapper->GetCommentsTreePageByTargetId($sId,$sTargetType,$iCount,$iPage,$iPerPage);
			} else {
				$aComments=$this->oMapper->GetCommentsTreeByTargetId($sId,$sTargetType);
				$iCount=count($aComments);
			}
			$iMaxIdComment=count($aComments) ? max($aComments) : 0;
			$aReturn=array('comments'=>$aComments,'iMaxIdComment'=>$iMaxIdComment,'count'=>$iCount);
			$cache->Set($aReturn, "comment_tree_target_{$sId}_{$sTargetType}_{$iPage}_{$iPerPage}", array("comment_new_{$sTargetType}_{$sId}"), 60*60*24*2);
		}
		$aReturn['comments']=$this->GetCommentsAdditionalData($aReturn['comments']);
		return $aReturn;
	}
	/**
	 * Возвращает количество дочерних комментариев у корневого коммента
	 *
	 * @param int $sId	ID владельца коммента
	 * @param string $sTargetType	Тип владельца комментария
	 * @return int
	 */
	public function GetCountCommentsRootByTargetId($sId,$sTargetType) {
		return $this->oMapper->GetCountCommentsRootByTargetId($sId,$sTargetType);
	}
	/**
	 * Возвращает номер страницы, на которой расположен комментарий
	 *
	 * @param int                         $sId         ID владельца коммента
	 * @param string                      $sTargetType Тип владельца комментария
	 * @param \App\Entities\EntityComment $oComment    Объект комментария
	 *
	 * @return bool|int
	 */
	public function GetPageCommentByTargetId($sId,$sTargetType,$oComment) {
		if (!Config::Get('module.comment.nested_per_page')) {
			return 1;
		}
		if (is_numeric($oComment)) {
			if (!($oComment=$this->GetCommentById($oComment))) {
				return false;
			}
			if ($oComment->getTargetId()!=$sId or $oComment->getTargetType()!=$sTargetType) {
				return false;
			}
		}
		/**
		 * Получаем корневого родителя
		 */
		if ($oComment->getPid()) {
			if (!($oCommentRoot=$this->oMapper->GetCommentRootByTargetIdAndChildren($sId,$sTargetType,$oComment->getLeft()))) {
				return false;
			}
		} else {
			$oCommentRoot=$oComment;
		}
		$iCount=ceil($this->oMapper->GetCountCommentsAfterByTargetId($sId,$sTargetType,$oCommentRoot->getLeft())/Config::Get('module.comment.nested_per_page'));

		if (!Config::Get('module.comment.nested_page_reverse') and $iCountPage=ceil($this->GetCountCommentsRootByTargetId($sId,$sTargetType)/Config::Get('module.comment.nested_per_page'))) {
			$iCount=$iCountPage-$iCount+1;
		}
		return $iCount ? $iCount : 1;
	}
	/**
	 * Добавляет коммент
	 *
     * @param  \App\Entities\EntityComment $oComment Объект комментария
	 * @param  bool                        $bMark    Использовать ли mark
	 *
	 * @return bool|\App\Entities\EntityComment
	 */
	public function AddComment(EntityComment $oComment, $bMark= false) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
	    if (Config::Get('module.comment.use_nested')) {
			$sId=$this->oMapper->AddCommentTree($oComment);
			$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_update"));
		} else {
            $oComment->setText(LS::Make(ModuleText::class)->CommentParser($oComment, true));
			$sId=$this->oMapper->AddComment($oComment);
		}
        /** @var ModuleTopic $topic */
        $topic = LS::Make(ModuleTopic::class);
		if ($sId) {
			if ($oComment->getTargetType()=='topic') {
				$topic->increaseTopicCountComment($oComment->getTargetId());
			}
			//чистим зависимые кеши
            $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_new_{$oComment->getTargetType()}","comment_new_user_{$oComment->getUserId()}_{$oComment->getTargetType()}","comment_new_{$oComment->getTargetType()}_{$oComment->getTargetId()}"));
            $oComment->setId($sId);
            $oTarget = $topic->GetTopicById($oComment->getTargetId());
            $sTextOld = $oComment->getText();
            $sText = preg_replace_callback('/@(.+?)\((.+?)\)/',
                function ($matches) use ($oComment) {
                    $sLogin = $matches[1];
                    $sNick = $matches[2];
                    $r = "<a href=\"/profile/" . $sLogin . "/\" class=\"summon-user\">&#64;" . $sNick . "</a>";
                    if ($oTargetUser = LS::Make(ModuleUser::class)->getUserByLogin($sLogin)) {
                        if ($oComment->getTargetType()=="topic")
                        	LS::Make(ModuleCast::class)->sendCastNotifyToUser("comment", $oComment, LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId()), $oTargetUser);
                        return $r;
                    }
                    return $matches[0];
                }, $sTextOld);
            $sText = preg_replace_callback('/@(.[^\s\<\>,?!]+)/',
                function ($matches) use ($oComment) {
                    $sLogin = $matches[1];
                    $r = "<a href=\"/profile/" . $sLogin . "/\" class=\"summon-user\">&#64;" . $sLogin . "</a>";
                    if ($oTargetUser = LS::Make(ModuleUser::class)->getUserByLogin($sLogin)) {
                    	if ($oComment->getTargetType()=="topic")
                        	LS::Make(ModuleCast::class)->sendCastNotifyToUser("comment", $oComment, LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId()), $oTargetUser);
                        return $r;
                    }
                    return $matches[0];
                }, $sText);
            if ($sTextOld != $sText) {
                $oComment->setText($sText);
                $oComment->setCountVote(0);
                $oComment->setCountFavourite(0);
                $oComment->setDelete(false);
                LS::Make(ModuleComment::class)->UpdateComment($oComment);
            }

			if (strstr($oComment->getText(), "@moderator")) {
				if ($oTarget->getBlog()->getType() == "open") {
					LS::Make(ModuleTalk::class)->SendTalk("Вызов модератора в пост " . $oTarget->getTitle(), "Я <a target='_blank' href='".$oTarget->getUrl()."#comment".$sId."'>прошу</a> модераторов посмотреть пост <a href='" . $oTarget->getUrl() . "'>" . $oTarget->getTitle() . "</a> и комментарии к нему на соответствие правилам.", $oComment->getUserId(), Config::Get("moderator"));
				} else {
					$aModersCollection = LS::Make(ModuleBlog::class)->GetBlogUsersByBlogId($oTarget->getBlog()->getId(),ModuleBlog::BLOG_USER_ROLE_MODERATOR)['collection'];
					$aModers = array();
					foreach ($aModersCollection as $oBlogUser) {
						$aModers[] = $oBlogUser->getUserId();
					}
					LS::Make(ModuleTalk::class)->SendTalk("Вызов модератора в пост " . $oTarget->getTitle(), "Я <a target='_blank' href='".$oTarget->getUrl()."#comment".$sId."'>прошу</a> модераторов посмотреть пост <a href='" . $oTarget->getUrl() . "'>" . $oTarget->getTitle() . "</a> и комментарии к нему на соответствие правилам.", $oComment->getUserId(), $aModers);//$aModers);
				}
			}
			return $oComment;
		}
		return false;
	}
	/**
	 * Обновляет коммент
	 *
	 * @param  EntityComment $oComment Объект комментария
	 *
	 * @return bool
	 */
	public function UpdateComment(EntityComment $oComment) {
		if ($this->oMapper->UpdateComment($oComment)) {
			//чистим зависимые кеши
            /** @var \Engine\Modules\ModuleCache $cache */
            $cache = LS::Make(ModuleCache::class);
			$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_update","comment_update_{$oComment->getTargetType()}_{$oComment->getTargetId()}"));
			$cache->Delete("comment_{$oComment->getId()}");
			return true;
		}
		return false;
	}
	/**
	 * Обновляет рейтинг у коммента
	 *
     * @param  EntityComment $oComment Объект комментария
	 *
	 * @return bool
	 */
	public function UpdateCommentRating(EntityComment $oComment) {
		if ($this->oMapper->UpdateComment($oComment)) {
			//чистим зависимые кеши
            /** @var ModuleCache $cache */
            $cache = LS::Make(ModuleCache::class);
			$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_update_rating_{$oComment->getTargetType()}"));
			$cache->Delete("comment_{$oComment->getId()}");
			return true;
		}
		return false;
	}
	/**
	 * Обновляет статус у коммента - delete или publish
     *
     * @param  EntityComment $oComment	Объект комментария
	 *
	 * @return bool
	 */
	public function UpdateCommentStatus(EntityComment $oComment) {
		if ($this->oMapper->UpdateComment($oComment)) {
			/**
			 * Если комментарий удаляется, удаляем его из прямого эфира
			 */
			if($oComment->getDelete()) $this->DeleteCommentOnlineByArrayId($oComment->getId(),$oComment->getTargetType());
			/**
			 * Обновляем избранное
			 */
			LS::Make(ModuleFavourite::class)->SetFavouriteTargetPublish($oComment->getId(),'comment',!$oComment->getDelete());
			/**
			 * Чистим зависимые кеши
			 */
			LS::Order(function(ModuleCache $cache) use ($oComment) {
                if(Config::Get('sys.cache.solid')){
                    $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_update"));
                }
                $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_update_status_{$oComment->getTargetType()}"));
                $cache->Delete("comment_{$oComment->getId()}");
            });
			return true;
		}
		return false;
	}
	/**
	 * Устанавливает publish у коммента
	 *
	 * @param  int $sTargetId	ID владельца коммента
	 * @param  string $sTargetType	Тип владельца комментария
	 * @param  int    $iPublish	Статус отображать комментарии или нет
	 * @return bool
	 */
	public function SetCommentsPublish($sTargetId,$sTargetType,$iPublish) {
		if(!$aComments = $this->GetCommentsByTargetId($sTargetId,$sTargetType)) {
			return;
		}
		if(!isset($aComments['comments']) or count($aComments)==0) {
			return;
		}

        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_update_status_{$sTargetType}"));
		/**
		 * Если статус публикации успешно изменен, то меняем статус в отметке "избранное".
		 * Если комментарии снимаются с публикации, удаляем их из прямого эфира.
		 */
		if($this->oMapper->SetCommentsPublish($sTargetId,$sTargetType,$iPublish)){
			LS::Make(ModuleFavourite::class)->SetFavouriteTargetPublish(array_keys($aComments['comments']),'comment',$iPublish);
			if($iPublish!=1) $this->DeleteCommentOnlineByTargetId($sTargetId,$sTargetType);
			return;
		}
		return;
	}
	/**
	 * Удаляет коммент из прямого эфира
	 *
	 * @param  int $sTargetId	ID владельца коммента
	 * @param  string $sTargetType	Тип владельца комментария
	 * @return bool
	 */
	public function DeleteCommentOnlineByTargetId($sTargetId,$sTargetType) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_online_update_{$sTargetType}"));
		return $this->oMapper->DeleteCommentOnlineByTargetId($sTargetId,$sTargetType);
	}
	/**
	 * Добавляет новый коммент в прямой эфир
	 *
	 * @param EntityCommentOnline $oCommentOnline Объект онлайн комментария
	 *
	 * @return bool|int
	 */
	public function AddCommentOnline(EntityCommentOnline $oCommentOnline) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_online_update_{$oCommentOnline->getTargetType()}"));
		return $this->oMapper->AddCommentOnline($oCommentOnline);
	}
	/**
	 * Получить новые комменты для владельца
	 *
	 * @param int $sId	ID владельца коммента
	 * @param string $sTargetType	Тип владельца комментария
	 * @param int $sIdCommentLast ID последнего прочитанного комментария
	 * @return array('comments'=>array,'iMaxIdComment'=>int)
	 */
	public function GetCommentsNewByTargetId($sId,$sTargetType,$sIdCommentLast) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($aComments = $cache->Get("comment_target_{$sId}_{$sTargetType}_{$sIdCommentLast}"))) {
			$aComments=$this->oMapper->GetCommentsNewByTargetId($sId,$sTargetType,$sIdCommentLast);
			$cache->Set($aComments, "comment_target_{$sId}_{$sTargetType}_{$sIdCommentLast}", array("comment_new_{$sTargetType}_{$sId}"), 60*60*24*1);
		}
		if (count($aComments)==0) {
			return array('comments'=>array(),'iMaxIdComment'=>0);
		}

		$iMaxIdComment=max($aComments);
		$aCmts=$this->GetCommentsAdditionalData($aComments);
		$oViewerLocal=LS::Make(ModuleViewer::class)->GetLocalViewer();
		$oViewerLocal->Assign('oUserCurrent',LS::Make(ModuleUser::class)->GetUserCurrent());
		$oViewerLocal->Assign('bOneComment',true);
		if($sTargetType!='topic') {
			$oViewerLocal->Assign('bNoCommentFavourites',true);
		}
		$aCmt=array();
		foreach ($aCmts as $oComment) {
			$oViewerLocal->Assign('oComment',$oComment);
			$sText=$oViewerLocal->Fetch($this->GetTemplateCommentByTarget($sId,$sTargetType));
			$aCmt[]=array(
				'html' => $sText,
				'obj'  => $oComment,
			);
		}
		return array('comments'=>$aCmt,'iMaxIdComment'=>$iMaxIdComment);
	}

	public function GetCommentsNewByTargetIdWithoutHtml($sId,$sTargetType,$sIdCommentLast) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if (false === ($aComments = $cache->Get("comment_target_{$sId}_{$sTargetType}_{$sIdCommentLast}"))) {
			$aComments=$this->oMapper->GetCommentsNewByTargetId($sId,$sTargetType,$sIdCommentLast);
			$cache->Set($aComments, "comment_target_{$sId}_{$sTargetType}_{$sIdCommentLast}", array("comment_new_{$sTargetType}_{$sId}"), 60*60*24*1);
		}
		if (count($aComments)==0) {
			return array('comments'=>array(),'iMaxIdComment'=>0);
		}

		$iMaxIdComment=max($aComments);
		$aCmts=$this->GetCommentsAdditionalData($aComments);
		$oViewerLocal=LS::Make(ModuleViewer::class)->GetLocalViewer();
		$oViewerLocal->Assign('oUserCurrent',LS::Make(ModuleUser::class)->GetUserCurrent());
		$oViewerLocal->Assign('bOneComment',true);
		if($sTargetType!='topic') {
			$oViewerLocal->Assign('bNoCommentFavourites',true);
		}
		$aCmt=array();
		foreach ($aCmts as $oComment) {
			$oViewerLocal->Assign('oComment',$oComment);
			$sText=$oViewerLocal->Fetch($this->GetTemplateCommentByTarget($sId,$sTargetType));
			$aCmt[]=$oComment;
		}
		return array('comments'=>$aCmt,'iMaxIdComment'=>$iMaxIdComment);
	}
	/**
	 * Возвращает шаблон комментария для рендеринга
	 * Плагин может переопределить данный метод и вернуть свой шаблон в зависимости от типа
	 *
	 * @param int $iTargetId	ID объекта комментирования
	 * @param string $sTargetType	Типа объекта комментирования
	 * @return string
	 */
	public function GetTemplateCommentByTarget($iTargetId,$sTargetType) {
		return "comment.tpl";
	}
	/**
	 * Строит дерево комментариев
	 *
	 * @param array $aComments	Список комментариев
	 * @param bool $bBegin	Флаг начала построения дерева, для инициализации параметров внутри метода
	 * @return array('comments'=>array,'iMaxIdComment'=>int)
	 */
	protected function BuildCommentsRecursive($aComments,$bBegin=true) {
		static $aResultCommnets;
		static $iLevel;
		static $iMaxIdComment;
		if ($bBegin) {
			$aResultCommnets=array();
			$iLevel=0;
			$iMaxIdComment=0;
		}
		foreach ($aComments as $aComment) {
			$aTemp=$aComment;
			if ($aComment['comment_id']>$iMaxIdComment) {
				$iMaxIdComment=$aComment['comment_id'];
			}
			$aTemp['level']=$iLevel;
			unset($aTemp['childNodes']);
			$aResultCommnets[$aTemp['comment_id']]=$aTemp['level'];
			if (isset($aComment['childNodes']) and count($aComment['childNodes'])>0) {
				$iLevel++;
				$this->BuildCommentsRecursive($aComment['childNodes'],false);
			}
		}
		$iLevel--;
		return array('comments'=>$aResultCommnets,'iMaxIdComment'=>$iMaxIdComment);
	}
	/**
	 * Получает привязку комментария к ибранному(добавлен ли коммент в избранное у юзера)
	 *
	 * @param  int $sCommentId	ID комментария
	 * @param  int $sUserId	ID пользователя
	 *
	 * @return \App\Entities\EntityFavourite|null
	 */
	public function GetFavouriteComment($sCommentId,$sUserId) {
		return LS::Make(ModuleFavourite::class)->GetFavourite($sCommentId,'comment',$sUserId);
	}
	/**
	 * Получить список избранного по списку айдишников
	 *
	 * @param array $aCommentId	Список ID комментов
	 * @param int $sUserId	ID пользователя
	 * @return array
	 */
	public function GetFavouriteCommentsByArray($aCommentId,$sUserId) {
		return LS::Make(ModuleFavourite::class)->GetFavouritesByArray($aCommentId,'comment',$sUserId);
	}
	/**
	 * Получить список избранного по списку айдишников, но используя единый кеш
	 *
	 * @param array  $aCommentId	Список ID комментов
	 * @param int    $sUserId	ID пользователя
	 * @return array
	 */
	public function GetFavouriteCommentsByArraySolid($aCommentId,$sUserId) {
		return LS::Make(ModuleFavourite::class)->GetFavouritesByArraySolid($aCommentId,'comment',$sUserId);
	}
	/**
	 * Получает список комментариев из избранного пользователя
	 *
	 * @param  int $sUserId	ID пользователя
	 * @param  int    $iCurrPage	Номер страницы
	 * @param  int    $iPerPage	Количество элементов на страницу
	 * @return array
	 */
	public function GetCommentsFavouriteByUserId($sUserId,$iCurrPage,$iPerPage) {
		$aCloseTopics = array();
		/**
		 * Получаем список идентификаторов избранных комментов
		 */
        /** @var ModuleFavourite $fav */
        $fav = LS::Make(ModuleFavourite::class);
		$data = ($this->oUserCurrent && $sUserId==$this->oUserCurrent->getId())
			? $fav->GetFavouritesByUserId($sUserId,'comment',$iCurrPage,$iPerPage,$aCloseTopics)
			: $fav->GetFavouriteOpenCommentsByUserId($sUserId,$iCurrPage,$iPerPage);
		/**
		 * Получаем комменты по переданому массиву айдишников
		 */
		$data['collection']=$this->GetCommentsAdditionalData($data['collection']);
		return $data;
	}
	/**
	 * Возвращает число комментариев в избранном
	 *
	 * @param  int $sUserId	ID пользователя
	 * @return int
	 */
	public function GetCountCommentsFavouriteByUserId($sUserId) {
        /** @var ModuleFavourite $fav */
        $fav = LS::Make(ModuleFavourite::class);
		return ($this->oUserCurrent && $sUserId==$this->oUserCurrent->getId())
			? $fav->GetCountFavouritesByUserId($sUserId,'comment')
			: $fav->GetCountFavouriteOpenCommentsByUserId($sUserId);
	}
	/**
	 * Добавляет комментарий в избранное
	 *
	 * @param  \App\Entities\EntityFavourite $oFavourite Объект избранного
	 *
	 * @return bool|EntityFavourite
	 */
	public function AddFavouriteComment(EntityFavourite $oFavourite) {
		if( ($oFavourite->getTargetType()=='comment')
			&& ($oComment=LS::Make(ModuleComment::class)->GetCommentById($oFavourite->getTargetId()))
			&& in_array($oComment->getTargetType(),Config::get('module.comment.favourite_target_allow'))) {
			return LS::Make(ModuleFavourite::class)->AddFavourite($oFavourite);
		}
		return false;
	}
	/**
	 * Удаляет комментарий из избранного
	 *
	 * @param  EntityFavourite $oFavourite Объект избранного
	 *
	 * @return bool
	 */
	public function DeleteFavouriteComment(EntityFavourite $oFavourite) {
		if( ($oFavourite->getTargetType()=='comment')
			&& ($oComment=LS::Make(ModuleComment::class)->GetCommentById($oFavourite->getTargetId()))
			&& in_array($oComment->getTargetType(),Config::get('module.comment.favourite_target_allow'))) {
			return LS::Make(ModuleFavourite::class)->DeleteFavourite($oFavourite);
		}
		return false;
	}
	/**
	 * Удаляет комментарии из избранного по списку
	 *
	 * @param  array $aCommentId	Список ID комментариев
	 * @return bool
	 */
	public function DeleteFavouriteCommentsByArrayId($aCommentId) {
		return LS::Make(ModuleFavourite::class)->DeleteFavouriteByTargetId($aCommentId, 'comment');
	}
	/**
	 * Удаляет комментарии из базы данных
	 *
	 * @param   array|int $aTargetId	Список ID владельцев
	 * @param   string $sTargetType	Тип владельцев
	 * @return  bool
	 */
	public function DeleteCommentByTargetId($aTargetId,$sTargetType) {
		if(!is_array($aTargetId)) $aTargetId = array($aTargetId);
		/**
		 * Получаем список идентификаторов удаляемых комментариев
		 */
		$aCommentsId = array();
		foreach ($aTargetId as $sTargetId) {
			$aComments=$this->GetCommentsByTargetId($sTargetId,$sTargetType);
			$aCommentsId = array_merge($aCommentsId, array_keys($aComments['comments']));
		}
		/**
		 * Если ни одного комментария не найдено, выходим
		 */
		if(!count($aCommentsId)) return true;
		/**
		 * Чистим зависимые кеши
		 */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		if(Config::Get('sys.cache.solid')) {
			$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("comment_update","comment_target_{$sTargetId}_{$sTargetType}"));
		} else {
			$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("comment_target_{$sTargetId}_{$sTargetType}"));
			/**
			 * Удаляем кеш для каждого комментария
			 */
			foreach($aCommentsId as $iCommentId) $cache->Delete("comment_{$iCommentId}");
		}
		if($this->oMapper->DeleteCommentByTargetId($aTargetId,$sTargetType)){
			/**
			 * Удаляем комментарии из избранного
			 */
			$this->DeleteFavouriteCommentsByArrayId($aCommentsId);
			/**
			 * Удаляем комментарии к топику из прямого эфира
			 */
			$this->DeleteCommentOnlineByArrayId($aCommentsId,$sTargetType);
			/**
			 * Удаляем голосование за комментарии
			 */
			LS::Make(ModuleVote::class)->DeleteVoteByTarget($aCommentsId,'comment');
			return true;
		}
		return false;
	}
	/**
	 * Удаляет коммент из прямого эфира по массиву переданных идентификаторов
	 *
	 * @param  array|int $aCommentId
	 * @param  string      $sTargetType	Тип владельцев
	 * @return bool
	 */
	public function DeleteCommentOnlineByArrayId($aCommentId,$sTargetType) {
		if(!is_array($aCommentId)) $aCommentId = array($aCommentId);
		/**
		 * Чистим кеш
		 */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_online_update_{$sTargetType}"));
		return $this->oMapper->DeleteCommentOnlineByArrayId($aCommentId,$sTargetType);
	}
	/**
	 * Меняем target parent по массиву идентификаторов
	 *
	 * @param  int $sParentId	Новый ID родителя владельца
	 * @param  string $sTargetType	Тип владельца
	 * @param  array|int $aTargetId	Список ID владельцев
	 * @return bool
	 */
	public function UpdateTargetParentByTargetId($sParentId, $sTargetType, $aTargetId) {
		if(!is_array($aTargetId)) $aTargetId = array($aTargetId);
		// чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_new_{$sTargetType}"));

		return $this->oMapper->UpdateTargetParentByTargetId($sParentId, $sTargetType, $aTargetId);
	}
	/**
	 * Меняем target parent по массиву идентификаторов в таблице комментариев online
	 *
	 * @param  int $sParentId	Новый ID родителя владельца
	 * @param  string $sTargetType	Тип владельца
	 * @param  array|int $aTargetId	Список ID владельцев
	 * @return bool
	 */
	public function UpdateTargetParentByTargetIdOnline($sParentId, $sTargetType, $aTargetId) {
		if(!is_array($aTargetId)) $aTargetId = array($aTargetId);
		// чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_online_update_{$sTargetType}"));

		return $this->oMapper->UpdateTargetParentByTargetIdOnline($sParentId, $sTargetType, $aTargetId);
	}
	/**
	 * Меняет target parent на новый
	 *
	 * @param int $sParentId	Прежний ID родителя владельца
	 * @param string $sTargetType	Тип владельца
	 * @param int $sParentIdNew	Новый ID родителя владельца
	 * @return bool
	 */
	public function MoveTargetParent($sParentId, $sTargetType, $sParentIdNew) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_new_{$sTargetType}"));
		return $this->oMapper->MoveTargetParent($sParentId, $sTargetType, $sParentIdNew);
	}
	/**
	 * Меняет target parent на новый в прямом эфире
	 *
	 * @param int $sParentId	Прежний ID родителя владельца
	 * @param string $sTargetType	Тип владельца
	 * @param int $sParentIdNew	Новый ID родителя владельца
	 * @return bool
	 */
	public function MoveTargetParentOnline($sParentId, $sTargetType, $sParentIdNew) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
		$cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_online_update_{$sTargetType}"));
		return $this->oMapper->MoveTargetParentOnline($sParentId, $sTargetType, $sParentIdNew);
	}
	/**
	 * Перестраивает дерево комментариев
	 * Восстанавливает значения left, right и level
	 *
	 * @param int $aTargetId	Список ID владельцев
	 * @param string $sTargetType	Тип владельца
	 */
	public function RestoreTree($aTargetId=null,$sTargetType=null) {
		// обработать конкретную сущность
		if (!is_null($aTargetId) and !is_null($sTargetType)) {
			$this->oMapper->RestoreTree(null,0,-1,$aTargetId,$sTargetType);
			return ;
		}
		$aType=array();
		// обработать все сущности конкретного типа
		if (!is_null($sTargetType)) {
			$aType[]=$sTargetType;
		} else {
			// обработать все сущности всех типов
			$aType=$this->oMapper->GetCommentTypes();
		}
		foreach ($aType as $sTargetType) {
			// для каждого типа получаем порциями ID сущностей
			$iPage=1;
			$iPerPage=50;
			while ($aResult=$this->oMapper->GetTargetIdByType($sTargetType,$iPage,$iPerPage)) {
				foreach ($aResult as $Row) {
					$this->oMapper->RestoreTree(null,0,-1,$Row['target_id'],$sTargetType);
				}
				$iPage++;
			}
		}
	}
	/**
	 * Пересчитывает счетчик избранных комментариев
	 *
	 * @return bool
	 */
	public function RecalculateFavourite() {
		return $this->oMapper->RecalculateFavourite();
	}
	/**
	 * Получает список комментариев по фильтру
	 *
	 * @param array $aFilter	Фильтр выборки
	 * @param array $aOrder		Сортировка
	 * @param int $iCurrPage	Номер текущей страницы
	 * @param int $iPerPage		Количество элементов на одну страницу
	 * @param array $aAllowData		Список типов данных, которые нужно подтянуть к списку комментов
	 * @return array
	 */
	public function GetCommentsByFilter($aFilter,$aOrder,$iCurrPage,$iPerPage,$aAllowData=null) {
		if (is_null($aAllowData)) {
			$aAllowData=array('target','user'=>array());
		}
		$aCollection=$this->oMapper->GetCommentsByFilter($aFilter,$aOrder,$iCount,$iCurrPage,$iPerPage);
		return array('collection'=>$this->GetCommentsAdditionalData($aCollection,$aAllowData),'count'=>$iCount);
	}
	/**
	 * Алиас для корректной работы ORM
	 *
	 * @param array $aCommentId	Список ID комментариев
	 * @return array
	 */
	public function GetCommentItemsByArrayId($aCommentId) {
		return $this->GetCommentsByArrayId($aCommentId);
	}
	
	public function ConvertCommentToArray($oComment, $sReadlast=null, $bIgnoreDelete=false) {
		$oUser = LS::Make(ModuleUser::class)->GetUserById($oComment->getUserId());
		$aComment = array();
		$aComment['id'] = (string)$oComment->getId();
		$aComment['author'] = array("id"=>$oComment->getUserId(), "login"=>$oUser->getLogin(), "avatar"=>$oUser->getProfileAvatarPath(48));
		$aComment['date'] = gmdate('c',strtotime($oComment->getDate()));
		$aComment['text'] = $oComment->getText();
		if ($oComment->getDelete() && !$bIgnoreDelete && !LS::Make(ModuleACL::class)->UserCanDeleteComment($this->oUserCurrent, $oComment)) {
			$aComment['text'] = "";
		}
		/*if ($this->oUserCurrent) {
			if ($this->oUserCurrent->isAdministrator() || $this->oUserCurrent->isGlobalModerator()) {
				$aComment['text'] = $oComment->getText();
			}
		}*/
		$aComment['isBad'] = $oComment->isBad();
        $aComment['isDeleted'] = (int)$oComment->getDelete();
        $aComment['deleteReason'] = $oComment->getDeleteReason();
        $aComment['deleteUserId'] = $oComment->getDeleteUserId();
		$aComment['isFavourite'] = $oComment->getIsFavourite();
		$aComment['countFavourite'] = $oComment->getCountFavourite();
		$aComment['rating'] = $oComment->getRating();

		$oVote = $oComment->getVote();
		if ($oVote) {
			$aComment['voted'] = true;
			$aComment['voteDirection'] = $oVote->getDirection();
		} else {
			$aComment['voted'] = false;
			$aComment['voteDirection'] = null;
		}

		$aComment['targetType'] = $oComment->getTargetType();
		$aComment['targetId'] = $oComment->getTargetId();
		$aComment['level'] = $oComment->getLevel();
		$aComment['parentId'] = (string)$oComment->getPid();
		$aComment['isNew'] = $sReadlast? $sReadlast <= $oComment->getDate():true;
		$aComment['editCount'] = $oComment->getEditCount();
		return $aComment;
	}
}
