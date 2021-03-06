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

use App\Entities\EntityBlog;
use App\Entities\EntityBlogUser;
use App\Entities\EntityUser;
use App\Mappers\MapperBlog;
use Engine\Config;
use Engine\Engine;
use Engine\LS;
use Engine\Module;
use Engine\Modules\ModuleCache;
use Engine\Modules\ModuleImage;
use Engine\Modules\ModuleLang;
use Zend_Cache;

/**
 * Модуль для работы с блогами
 *
 * @package modules.blog
 * @since   1.0
 */
class ModuleBlog extends Module
{
    /**
     * Возможные роли пользователя в блоге
     */
    const BLOG_USER_ROLE_GUEST = 0;
    const BLOG_USER_ROLE_USER = 1;
    const BLOG_USER_ROLE_MODERATOR = 2;
    const BLOG_USER_ROLE_ADMINISTRATOR = 4;
    /**
     * Пользователь, приглашенный админом блога в блог
     */
    const BLOG_USER_ROLE_INVITE = -1;
    /**
     * Пользователь, отклонивший приглашение админа
     */
    const BLOG_USER_ROLE_REJECT = -2;
    /**
     * Забаненный в блоге пользователь
     */
    const BLOG_USER_ROLE_BAN = -4;

    const BLOG_USER_ROLE_RO = 5;

    /**
     * Объект маппера
     *
     * @var MapperBlog
     */
    protected $oMapperBlog;
    /**
     * Объект текущего пользователя
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent = null;

    /**
     * Инициализация
     *
     */
    public function Init()
    {
        $this->oMapperBlog = Engine::MakeMapper(MapperBlog::class);
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
    }

    /**
     * Получает дополнительные данные(объекты) для блогов по их ID
     *
     * @param array $aBlogId    Список ID блогов
     * @param array $aAllowData Список типов дополнительных данных, которые нужно получить для блогов
     * @param array $aOrder     Порядок сортировки
     *
     * @return array
     */
    public function GetBlogsAdditionalData($aBlogId, $aAllowData = null, $aOrder = null)
    {
        if (is_null($aAllowData)) {
            $aAllowData = ['vote', 'owner' => [], 'relation_user'];
        }
        func_array_simpleflip($aAllowData);
        if (!is_array($aBlogId)) {
            $aBlogId = [$aBlogId];
        }
        /**
         * Получаем блоги
         */
        $aBlogs = $this->GetBlogsByArrayId($aBlogId, $aOrder);
        /**
         * Формируем ID дополнительных данных, которые нужно получить
         */
        $aUserId = [];
        foreach ($aBlogs as $oBlog) {
            if (isset($aAllowData['owner'])) {
                $aUserId[] = $oBlog->getOwnerId();
            }
        }
        /**
         * Получаем дополнительные данные
         */
        $aBlogUsers = [];
        $aBlogsVote = [];
        $aUsers = isset($aAllowData['owner']) && is_array($aAllowData['owner']) ? LS::Make(ModuleUser::class)
            ->GetUsersAdditionalData($aUserId, $aAllowData['owner'])
            : LS::Make(ModuleUser::class)->GetUsersAdditionalData($aUserId);
        if (isset($aAllowData['relation_user']) and $this->oUserCurrent) {
            $aBlogUsers = $this->GetBlogUsersByArrayBlog($aBlogId, $this->oUserCurrent->getId());
        }
        if (isset($aAllowData['vote']) and $this->oUserCurrent) {
            $aBlogsVote = LS::Make(ModuleVote::class)->GetVoteByArray($aBlogId, 'blog', $this->oUserCurrent->getId());
        }
        /**
         * Добавляем данные к результату - списку блогов
         */
        foreach ($aBlogs as $oBlog) {
            if (isset($aUsers[$oBlog->getOwnerId()])) {
                $oBlog->setOwner($aUsers[$oBlog->getOwnerId()]);
            } else {
                $oBlog->setOwner(null); // или $oBlog->setOwner(new ModuleUser_EntityUser());
            }
            if (isset($aBlogUsers[$oBlog->getId()])) {
                $oBlog->setUserIsJoin(true);
                $oBlog->setUserIsAdministrator($aBlogUsers[$oBlog->getId()]->getIsAdministrator());
                $oBlog->setUserIsModerator($aBlogUsers[$oBlog->getId()]->getIsModerator());
            } else {
                $oBlog->setUserIsJoin(false);
                $oBlog->setUserIsAdministrator(false);
                $oBlog->setUserIsModerator(false);
            }
            if (isset($aBlogsVote[$oBlog->getId()])) {
                $oBlog->setVote($aBlogsVote[$oBlog->getId()]);
            } else {
                $oBlog->setVote(null);
            }
        }

        return $aBlogs;
    }

    /**
     * Возвращает список блогов по ID
     *
     * @param array      $aBlogId Список ID блогов
     * @param array|null $aOrder  Порядок сортировки
     *
     * @return array
     */
    public function GetBlogsByArrayId($aBlogId, $aOrder = null)
    {
        if (!$aBlogId) {
            return [];
        }
        if (Config::Get('sys.cache.solid')) {
            return $this->GetBlogsByArrayIdSolid($aBlogId, $aOrder);
        }
        if (!is_array($aBlogId)) {
            $aBlogId = [$aBlogId];
        }
        $aBlogId = array_unique($aBlogId);
        $aBlogs = [];
        $aBlogIdNotNeedQuery = [];
        /**
         * Делаем мульти-запрос к кешу
         */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $aCacheKeys = func_build_cache_keys($aBlogId, 'blog_');
        if (false !== ($data = $cache->Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey]) {
                        $aBlogs[$data[$sKey]->getId()] = $data[$sKey];
                    } else {
                        $aBlogIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }
        /**
         * Смотрим каких блогов не было в кеше и делаем запрос в БД
         */
        $aBlogIdNeedQuery = array_diff($aBlogId, array_keys($aBlogs));
        $aBlogIdNeedQuery = array_diff($aBlogIdNeedQuery, $aBlogIdNotNeedQuery);
        $aBlogIdNeedStore = $aBlogIdNeedQuery;
        if ($data = $this->oMapperBlog->GetBlogsByArrayId($aBlogIdNeedQuery)) {
            foreach ($data as $oBlog) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aBlogs[$oBlog->getId()] = $oBlog;
                $cache->Set($oBlog, "blog_{$oBlog->getId()}", [], 60 * 60 * 24 * 4);
                $aBlogIdNeedStore = array_diff($aBlogIdNeedStore, [$oBlog->getId()]);
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aBlogIdNeedStore as $sId) {
            $cache->Set(null, "blog_{$sId}", [], 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aBlogs = func_array_sort_by_keys($aBlogs, $aBlogId);

        return $aBlogs;
    }

    /**
     * Возвращает список блогов по ID, но используя единый кеш
     *
     * @param array      $aBlogId Список ID блогов
     * @param array|null $aOrder  Сортировка блогов
     *
     * @return array
     */
    public function GetBlogsByArrayIdSolid($aBlogId, $aOrder = null)
    {
        if (!is_array($aBlogId)) {
            $aBlogId = [$aBlogId];
        }
        $aBlogId = array_unique($aBlogId);
        $aBlogs = [];
        $s = join(',', $aBlogId);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("blog_id_{$s}"))) {
            $data = $this->oMapperBlog->GetBlogsByArrayId($aBlogId, $aOrder);
            foreach ($data as $oBlog) {
                $aBlogs[$oBlog->getId()] = $oBlog;
            }
            $cache->Set($aBlogs, "blog_id_{$s}", ["blog_update"], 60 * 60 * 24 * 1);

            return $aBlogs;
        }

        return $data;
    }

    /**
     * Получить персональный блог юзера
     *
     * @param int $sUserId ID пользователя
     *
     * @return \App\Entities\EntityBlog
     */
    public function GetPersonalBlogByUserId($sUserId)
    {
        $id = $this->oMapperBlog->GetPersonalBlogByUserId($sUserId);

        return $this->GetBlogById($id);
    }

    /**
     * Получить блог по айдишнику(номеру)
     *
     * @param int $sBlogId ID блога
     *
     * @return EntityBlog|null
     */
    public function GetBlogById($sBlogId)
    {
        if (!is_numeric($sBlogId)) {
            return null;
        }
        $aBlogs = $this->GetBlogsAdditionalData($sBlogId);
        if (isset($aBlogs[$sBlogId])) {
            return $aBlogs[$sBlogId];
        }

        return null;
    }

    /**
     * Получить блог по айдишнику(номеру)
     *
     * @param int $sBlogId ID блога
     *
     * @return \App\Entities\EntityBlog|null
     */
    public function GetDeletedBlogById($sBlogId)
    {
        if (!is_numeric($sBlogId)) {
            return null;
        }
        $aBlogs = $this->GetBlogsAdditionalData($sBlogId);
        if (isset($aBlogs[$sBlogId])) {
            return $aBlogs[$sBlogId];
        }

        return null;
    }

    /**
     * Получить блог по УРЛу
     *
     * @param string $sBlogUrl URL блога
     *
     * @return \App\Entities\EntityBlog|null
     */
    public function GetBlogByUrl($sBlogUrl)
    {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($id = $cache->Get("blog_url_{$sBlogUrl}"))) {
            if ($id = $this->oMapperBlog->GetBlogByUrl($sBlogUrl)) {
                $cache->Set($id, "blog_url_{$sBlogUrl}", ["blog_update_{$id}"], 60 * 60 * 24 * 2);
            } else {
                $cache->Set(null, "blog_url_{$sBlogUrl}", ['blog_update', 'blog_new'], 60 * 60);
            }
        }

        return $this->GetBlogById($id);
    }

    /**
     * Получить блог по названию
     *
     * @param string $sTitle Название блога
     *
     * @return \App\Entities\EntityBlog|null
     */
    public function GetBlogByTitle($sTitle)
    {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($id = $cache->Get("blog_title_{$sTitle}"))) {
            if ($id = $this->oMapperBlog->GetBlogByTitle($sTitle)) {
                $cache->Set($id, "blog_title_{$sTitle}", ["blog_update_{$id}", 'blog_new'], 60 * 60 * 24 * 2);
            } else {
                $cache->Set(null, "blog_title_{$sTitle}", ['blog_update', 'blog_new'], 60 * 60);
            }
        }

        return $this->GetBlogById($id);
    }

    /**
     * Создаёт персональный блог
     *
     * @param \App\Entities\EntityUser $oUser Пользователь
     *
     * @return EntityBlog|bool
     */
    public function CreatePersonalBlog(EntityUser $oUser)
    {
        /** @var \Engine\Modules\ModuleLang $cache */
        $lang = LS::Make(ModuleLang::class);
        $oBlog = new EntityBlog();
        $oBlog->setOwnerId($oUser->getId());
        $oBlog->setTitle($lang->Get('blogs_personal_title').' '.$oUser->getLogin());
        $oBlog->setType('personal');
        $oBlog->setDescription($lang->Get('blogs_personal_description'));
        $oBlog->setDateAdd(date("Y-m-d H:i:s"));
        $oBlog->setLimitRatingTopic(-1000);
        $oBlog->setUrl(null);
        $oBlog->setAvatar(null);

        return $this->AddBlog($oBlog);
    }

    /**
     * Добавляет блог
     *
     * @param \App\Entities\EntityBlog $oBlog Блог
     *
     * @return \App\Entities\EntityBlog|bool
     */
    public function AddBlog(EntityBlog $oBlog)
    {
        if ($sId = $this->oMapperBlog->AddBlog($oBlog)) {
            $oBlog->setId($sId);
            //чистим зависимые кеши
            /** @var \Engine\Modules\ModuleCache $cache */
            $cache = LS::Make(ModuleCache::class);
            $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['blog_new']);

            return $oBlog;
        }

        return false;
    }

    /**
     * Обновляет блог
     *
     * @param \App\Entities\EntityBlog $oBlog Блог
     *
     * @return \App\Entities\EntityBlog|bool
     */
    public function UpdateBlog(EntityBlog $oBlog)
    {
        $oBlog->setDateEdit(date("Y-m-d H:i:s"));
        $res = $this->oMapperBlog->UpdateBlog($oBlog);
        if ($res) {
            //чистим зависимые кеши
            /** @var \Engine\Modules\ModuleCache $cache */
            $cache = LS::Make(ModuleCache::class);
            $cache->Clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                ['blog_update', "blog_update_{$oBlog->getId()}", "topic_update"]
            );
            $cache->Delete("blog_{$oBlog->getId()}");

            return true;
        }

        return false;
    }

    /**
     * Добавляет отношение юзера к блогу, по сути присоединяет к блогу
     *
     * @param \App\Entities\EntityBlogUser $oBlogUser Объект связи(отношения) блога с пользователем
     *
     * @return bool
     */
    public function AddRelationBlogUser(EntityBlogUser $oBlogUser)
    {
        if ($this->oMapperBlog->AddRelationBlogUser($oBlogUser)) {
            /** @var \Engine\Modules\ModuleCache $cache */
            $cache = LS::Make(ModuleCache::class);
            $cache->Clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                [
                    "blog_relation_change_{$oBlogUser->getUserId()}",
                    "blog_relation_change_blog_{$oBlogUser->getBlogId()}"
                ]
            );
            $cache->Delete("blog_relation_user_{$oBlogUser->getBlogId()}_{$oBlogUser->getUserId()}");

            return true;
        }

        return false;
    }

    /**
     * Удалет отношение юзера к блогу, по сути отключает от блога
     *
     * @param \App\Entities\EntityBlogUser $oBlogUser Объект связи(отношения) блога с пользователем
     *
     * @return bool
     */
    public function DeleteRelationBlogUser(EntityBlogUser $oBlogUser)
    {
        if ($this->oMapperBlog->DeleteRelationBlogUser($oBlogUser)) {
            /** @var \Engine\Modules\ModuleCache $cache */
            $cache = LS::Make(ModuleCache::class);
            $cache->Clean(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                [
                    "blog_relation_change_{$oBlogUser->getUserId()}",
                    "blog_relation_change_blog_{$oBlogUser->getBlogId()}"
                ]
            );
            $cache->Delete("blog_relation_user_{$oBlogUser->getBlogId()}_{$oBlogUser->getUserId()}");

            return true;
        }

        return false;
    }

    /**
     * Получает список блогов по хозяину
     *
     * @param int  $sUserId       ID пользователя
     * @param bool $bReturnIdOnly Возвращать только ID блогов или полные объекты
     *
     * @return array
     */
    public function GetBlogsByOwnerId($sUserId, $bReturnIdOnly = false)
    {
        $data = $this->oMapperBlog->GetBlogsByOwnerId($sUserId);
        /**
         * Возвращаем только иденитификаторы
         */
        if ($bReturnIdOnly) {
            return $data;
        }

        $data = $this->GetBlogsAdditionalData($data);

        return $data;
    }

    /**
     * Получает список всех НЕ персональных блогов
     *
     * @param bool $bReturnIdOnly Возвращать только ID блогов или полные объекты
     *
     * @return array
     */
    public function GetBlogs($bReturnIdOnly = false)
    {
        $data = $this->oMapperBlog->GetBlogs();
        /**
         * Возвращаем только иденитификаторы
         */
        if ($bReturnIdOnly) {
            return $data;
        }

        $data = $this->GetBlogsAdditionalData($data);

        return $data;
    }

    /**
     * Получает список пользователей блога.
     * Если роль не указана, то считаем что поиск производиться по положительным значениям (статусом выше GUEST).
     *
     * @param int      $sBlogId  ID блога
     * @param int|null $iRole    Роль пользователей в блоге
     * @param int      $iPage    Номер текущей страницы
     * @param int      $iPerPage Количество элементов на одну страницу
     *
     * @return array
     */
    public function GetBlogUsersByBlogId($sBlogId, $iRole = null, $iPage = 1, $iPerPage = 100, $sLoginFilter = null)
    {
        $aFilter = [
            'blog_id' => $sBlogId,
        ];
        if ($iRole !== null) {
            $aFilter['user_role'] = $iRole;
        }
        if ($sLoginFilter !== null) {
            $aFilter['user_role'] = $iRole;
        }

        $s = serialize($aFilter);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("blog_relation_user_by_filter_{$s}_{$iPage}_{$iPerPage}"))) {
            $data = [
                'collection' => $this->oMapperBlog->GetBlogUsers($aFilter, $iCount, $iPage, $iPerPage),
                'count'      => $iCount
            ];
            $cache->Set(
                $data,
                "blog_relation_user_by_filter_{$s}_{$iPage}_{$iPerPage}",
                ["blog_relation_change_blog_{$sBlogId}"],
                60 * 60 * 24 * 3
            );
        }
        /**
         * Достаем дополнительные данные, для этого формируем список юзеров и делаем мульти-запрос
         */
        if ($data['collection']) {
            $aUserId = [];
            foreach ($data['collection'] as $oBlogUser) {
                $aUserId[] = $oBlogUser->getUserId();
            }
            $aUsers = LS::Make(ModuleUser::class)->GetUsersAdditionalData($aUserId);
            $aBlogs = LS::Make(ModuleBlog::class)->GetBlogsAdditionalData($sBlogId);

            $aResults = [];
            foreach ($data['collection'] as $oBlogUser) {
                if (isset($aUsers[$oBlogUser->getUserId()])) {
                    $oBlogUser->setUser($aUsers[$oBlogUser->getUserId()]);
                } else {
                    $oBlogUser->setUser(null);
                }
                if (isset($aBlogs[$oBlogUser->getBlogId()])) {
                    $oBlogUser->setBlog($aBlogs[$oBlogUser->getBlogId()]);
                } else {
                    $oBlogUser->setBlog(null);
                }
                $aResults[$oBlogUser->getUserId()] = $oBlogUser;
            }
            $data['collection'] = $aResults;
        }

        return $data;
    }

    public function GetBlogUsersByBlogIdLike($sBlogId, $iRole = null, $iPage = 1, $iPerPage = 100, $sLoginFilter = null)
    {
        $aFilter = [
            'blog_id' => $sBlogId,
        ];
        if ($iRole !== null) {
            $aFilter['user_role'] = $iRole;
        }
        if ($sLoginFilter !== null) {
//        	echo "NOT NULL!";
            $aFilter['user_login'] = $sLoginFilter;
//            var_dump($aFilter);
        }

        $s = serialize($aFilter);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("blog_relation_user_by_filter_{$s}_{$iPage}_{$iPerPage}"))) {
            $data = [
                'collection' => $this->oMapperBlog->GetBlogUsersLike($aFilter, $iCount, $iPage, $iPerPage),
                'count'      => $iCount
            ];
            $cache->Set(
                $data,
                "blog_relation_user_by_filter_{$s}_{$iPage}_{$iPerPage}",
                ["blog_relation_change_blog_{$sBlogId}"],
                60 * 60 * 24 * 3
            );
        }
        /**
         * Достаем дополнительные данные, для этого формируем список юзеров и делаем мульти-запрос
         */
        if ($data['collection']) {
            $aUserId = [];
            foreach ($data['collection'] as $oBlogUser) {
                $aUserId[] = $oBlogUser->getUserId();
            }
            $aUsers = LS::Make(ModuleUser::class)->GetUsersAdditionalData($aUserId);
            $aBlogs = LS::Make(ModuleBlog::class)->GetBlogsAdditionalData($sBlogId);

            $aResults = [];
            foreach ($data['collection'] as $oBlogUser) {
                if (isset($aUsers[$oBlogUser->getUserId()])) {
                    $oBlogUser->setUser($aUsers[$oBlogUser->getUserId()]);
                } else {
                    $oBlogUser->setUser(null);
                }
                if (isset($aBlogs[$oBlogUser->getBlogId()])) {
                    $oBlogUser->setBlog($aBlogs[$oBlogUser->getBlogId()]);
                } else {
                    $oBlogUser->setBlog(null);
                }
                $aResults[$oBlogUser->getUserId()] = $oBlogUser;
            }
            $data['collection'] = $aResults;
        }

        return $data;
    }

    /**
     * Получает отношения юзера к блогам(состоит в блоге или нет)
     *
     * @param int      $sUserId       ID пользователя
     * @param int|null $iRole         Роль пользователя в блоге
     * @param bool     $bReturnIdOnly Возвращать только ID блогов или полные объекты
     *
     * @return array
     */
    public function GetBlogUsersByUserId($sUserId, $iRole = null, $bReturnIdOnly = false)
    {
        $aFilter = [
            'user_id' => $sUserId
        ];
        if ($iRole !== null) {
            $aFilter['user_role'] = $iRole;
        }
        $s = serialize($aFilter);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("blog_relation_user_by_filter_$s"))) {
            $data = $this->oMapperBlog->GetBlogUsers($aFilter);
            $cache->Set(
                $data,
                "blog_relation_user_by_filter_$s",
                ["blog_update", "blog_relation_change_{$sUserId}"],
                60 * 60 * 24 * 3
            );
        }
        /**
         * Достаем дополнительные данные, для этого формируем список блогов и делаем мульти-запрос
         */
        $aBlogId = [];
        if ($data) {
            foreach ($data as $oBlogUser) {
                $aBlogId[] = $oBlogUser->getBlogId();
            }
            /**
             * Если указано возвращать полные объекты
             */
            if (!$bReturnIdOnly) {
                $aUsers = LS::Make(ModuleUser::class)->GetUsersAdditionalData($sUserId);
                $aBlogs = LS::Make(ModuleBlog::class)->GetBlogsAdditionalData($aBlogId);
                foreach ($data as $oBlogUser) {
                    if (isset($aUsers[$oBlogUser->getUserId()])) {
                        $oBlogUser->setUser($aUsers[$oBlogUser->getUserId()]);
                    } else {
                        $oBlogUser->setUser(null);
                    }
                    if (isset($aBlogs[$oBlogUser->getBlogId()])) {
                        $oBlogUser->setBlog($aBlogs[$oBlogUser->getBlogId()]);
                    } else {
                        $oBlogUser->setBlog(null);
                    }
                }
            }
        }

        return ($bReturnIdOnly) ? $aBlogId : $data;
    }

    /**
     * Состоит ли юзер в конкретном блоге
     *
     * @param int $sBlogId ID блога
     * @param int $sUserId ID пользователя
     *
     * @return \App\Entities\EntityBlogUser|null
     */
    public function GetBlogUserByBlogIdAndUserId($sBlogId, $sUserId)
    {
        if ($aBlogUser = $this->GetBlogUsersByArrayBlog($sBlogId, $sUserId)) {
            if (isset($aBlogUser[$sBlogId])) {
                return $aBlogUser[$sBlogId];
            }
        }

        return null;
    }

    /**
     * Получить список отношений блог-юзер по списку айдишников
     *
     * @param array $aBlogId Список ID блогов
     * @param int   $sUserId ID пользователя
     *
     * @return array
     */
    public function GetBlogUsersByArrayBlog($aBlogId, $sUserId)
    {
        if (!$aBlogId) {
            return [];
        }
        if (Config::Get('sys.cache.solid')) {
            return $this->GetBlogUsersByArrayBlogSolid($aBlogId, $sUserId);
        }
        if (!is_array($aBlogId)) {
            $aBlogId = [$aBlogId];
        }
        $aBlogId = array_unique($aBlogId);
        $aBlogUsers = [];
        $aBlogIdNotNeedQuery = [];
        /**
         * Делаем мульти-запрос к кешу
         */
        $aCacheKeys = func_build_cache_keys($aBlogId, 'blog_relation_user_', '_'.$sUserId);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false !== ($data = $cache->Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey]) {
                        $aBlogUsers[$data[$sKey]->getBlogId()] = $data[$sKey];
                    } else {
                        $aBlogIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }
        /**
         * Смотрим каких блогов не было в кеше и делаем запрос в БД
         */
        $aBlogIdNeedQuery = array_diff($aBlogId, array_keys($aBlogUsers));
        $aBlogIdNeedQuery = array_diff($aBlogIdNeedQuery, $aBlogIdNotNeedQuery);
        $aBlogIdNeedStore = $aBlogIdNeedQuery;
        if ($data = $this->oMapperBlog->GetBlogUsersByArrayBlog($aBlogIdNeedQuery, $sUserId)) {
            foreach ($data as $oBlogUser) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aBlogUsers[$oBlogUser->getBlogId()] = $oBlogUser;
                $cache->Set(
                    $oBlogUser,
                    "blog_relation_user_{$oBlogUser->getBlogId()}_{$oBlogUser->getUserId()}",
                    [],
                    60 * 60 * 24 * 4
                );
                $aBlogIdNeedStore = array_diff($aBlogIdNeedStore, [$oBlogUser->getBlogId()]);
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aBlogIdNeedStore as $sId) {
            $cache->Set(null, "blog_relation_user_{$sId}_{$sUserId}", [], 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aBlogUsers = func_array_sort_by_keys($aBlogUsers, $aBlogId);

        return $aBlogUsers;
    }

    /**
     * Получить список отношений блог-юзер по списку айдишников используя общий кеш
     *
     * @param array $aBlogId Список ID блогов
     * @param int   $sUserId ID пользователя
     *
     * @return array
     */
    public function GetBlogUsersByArrayBlogSolid($aBlogId, $sUserId)
    {
        if (!is_array($aBlogId)) {
            $aBlogId = [$aBlogId];
        }
        $aBlogId = array_unique($aBlogId);
        $aBlogUsers = [];
        $s = join(',', $aBlogId);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("blog_relation_user_{$sUserId}_id_{$s}"))) {
            $data = $this->oMapperBlog->GetBlogUsersByArrayBlog($aBlogId, $sUserId);
            foreach ($data as $oBlogUser) {
                $aBlogUsers[$oBlogUser->getBlogId()] = $oBlogUser;
            }
            $cache->Set(
                $aBlogUsers,
                "blog_relation_user_{$sUserId}_id_{$s}",
                ["blog_update", "blog_relation_change_{$sUserId}"],
                60 * 60 * 24 * 1
            );

            return $aBlogUsers;
        }

        return $data;
    }

    /**
     * Обновляет отношения пользователя с блогом
     *
     * @param \App\Entities\EntityBlogUser $oBlogUser Объект отновшения
     *
     * @return bool
     */
    public function UpdateRelationBlogUser(EntityBlogUser $oBlogUser)
    {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            ["blog_relation_change_{$oBlogUser->getUserId()}", "blog_relation_change_blog_{$oBlogUser->getBlogId()}"]
        );
        $cache->Delete("blog_relation_user_{$oBlogUser->getBlogId()}_{$oBlogUser->getUserId()}");

        return $this->oMapperBlog->UpdateRelationBlogUser($oBlogUser);
    }

    /**
     * Возвращает список блогов по фильтру
     *
     * @param array $aFilter    Фильтр выборки блогов
     * @param array $aOrder     Сортировка блогов
     * @param int   $iCurrPage  Номер текущей страницы
     * @param int   $iPerPage   Количество элементов на одну страницу
     * @param array $aAllowData Список типов данных, которые нужно подтянуть к списку блогов
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public function GetBlogsByFilter($aFilter, $aOrder, $iCurrPage, $iPerPage, $aAllowData = null)
    {
        if (is_null($aAllowData)) {
            $aAllowData = ['owner' => [], 'relation_user'];
        }
        $sKey = "blog_filter_".serialize($aFilter).serialize($aOrder)."_{$iCurrPage}_{$iPerPage}";
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get($sKey))) {
            $data = [
                'collection' => $this->oMapperBlog->GetBlogsByFilter(
                    $aFilter,
                    $aOrder,
                    $iCount,
                    $iCurrPage,
                    $iPerPage
                ),
                'count'      => $iCount
            ];
            $cache->Set($data, $sKey, ["blog_update", "blog_new"], 60 * 60 * 24 * 2);
        }
        $data['collection'] = $this->GetBlogsAdditionalData($data['collection'], $aAllowData);

        return $data;
    }

    /**
     * Получает список блогов по рейтингу
     *
     * @param int $iCurrPage Номер текущей страницы
     * @param int $iPerPage  Количество элементов на одну страницу
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public function GetBlogsRating($iCurrPage, $iPerPage)
    {
        return $this->GetBlogsByFilter(['blog_rating' => 'desc'], $iCurrPage, $iPerPage);
    }

    /**
     * Список подключенных блогов по рейтингу
     *
     * @param int $sUserId ID пользователя
     * @param int $iLimit  Ограничение на количество в ответе
     *
     * @return array
     */
    public function GetBlogsRatingJoin($sUserId, $iLimit)
    {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("blog_rating_join_{$sUserId}_{$iLimit}"))) {
            $data = $this->oMapperBlog->GetBlogsRatingJoin($sUserId, $iLimit);
            $cache->Set(
                $data,
                "blog_rating_join_{$sUserId}_{$iLimit}",
                ['blog_update', "blog_relation_change_{$sUserId}"],
                60 * 60 * 24
            );
        }

        return $data;
    }

    /**
     * Список своих блогов по рейтингу
     *
     * @param int $sUserId ID пользователя
     * @param int $iLimit  Ограничение на количество в ответе
     *
     * @return array
     */
    public function GetBlogsRatingSelf($sUserId, $iLimit)
    {
        $aResult = $this->GetBlogsByFilter(['user_owner_id' => $sUserId], ['blog_rating' => 'desc'], 1, $iLimit);

        return $aResult['collection'];
    }

    /**
     * Получает список блогов в которые может постить юзер
     *
     * @param \App\Entities\EntityUser $oUser Объект пользователя
     *
     * @return array
     */
    public function GetBlogsAllowByUser($oUser)
    {
        if ($oUser->isAdministrator()) {
            return $this->GetBlogs();
        } else {
            $aAllowBlogsUser = $this->GetBlogsByOwnerId($oUser->getId());
            $aBlogUsers = $this->GetBlogUsersByUserId($oUser->getId());
            foreach ($aBlogUsers as $oBlogUser) {
                $oBlog = $oBlogUser->getBlog();
                if (LS::Make(ModuleACL::class)->CanAddTopic($oUser, $oBlog) or $oBlogUser->getIsAdministrator()
                    or $oBlogUser->getIsModerator()
                ) {
                    $aAllowBlogsUser[$oBlog->getId()] = $oBlog;
                }
            }

            return $aAllowBlogsUser;
        }
    }

    /**
     * Получаем массив блогов, которые являются открытыми для пользователя
     *
     * @param  \App\Entities\EntityUser $oUser Объект пользователя
     *
     * @return array
     */
    public function GetAccessibleBlogsByUser($oUser)
    {
        if ($oUser->isAdministrator()) {
            return $this->GetBlogs(true);
        }
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($aOpenBlogsUser = $cache->Get("blog_accessible_user_{$oUser->getId()}"))) {
            /**
             * Заносим блоги, созданные пользователем
             */
            $aOpenBlogsUser = $this->GetBlogsByOwnerId($oUser->getId(), true);
            /**
             * Добавляем блоги, в которых состоит пользователь
             * (читателем, модератором, или администратором)
             */
            $aOpenBlogsUser = array_merge($aOpenBlogsUser, $this->GetBlogUsersByUserId($oUser->getId(), null, true));
            $cache->Set(
                $aOpenBlogsUser,
                "blog_accessible_user_{$oUser->getId()}",
                ['blog_new', 'blog_update', "blog_relation_change_{$oUser->getId()}"],
                60 * 60 * 24
            );
        }

        return $aOpenBlogsUser;
    }

    /**
     * Получаем массив идентификаторов блогов, которые являются закрытыми для пользователя
     *
     * @param  \App\Entities\EntityUser|null $oUser Пользователь
     *
     * @return array
     */
    public function GetInaccessibleBlogsByUser($oUser = null)
    {
        if ($oUser && $oUser->isAdministrator()) {
            return [];
        }
        $sUserId = $oUser ? $oUser->getId() : 'quest';
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($aCloseBlogs = $cache->Get("blog_inaccessible_user_{$sUserId}"))) {
            $aCloseBlogs = array_merge($this->oMapperBlog->GetHalfcloseBlogs(), $this->oMapperBlog->GetCloseBlogs());

            if ($oUser) {
                /**
                 * Получаем массив идентификаторов блогов,
                 * которые являются откытыми для данного пользователя
                 */
                $aOpenBlogs = $this->GetBlogUsersByUserId($oUser->getId(), null, true);
                /**
                 * Получаем закрытые блоги, где пользователь является автором
                 */

                $aOwnerBlogsClose =
                    $this->GetBlogsByFilter(['type' => 'close', 'user_owner_id' => $oUser->getId()], [], 1, 100, []);
                $aOwnerBlogsInvite =
                    $this->GetBlogsByFilter(['type' => 'invite', 'user_owner_id' => $oUser->getId()], [], 1, 100, []);
                $aOwnerBlogs = array_merge(
                    array_keys($aOwnerBlogsInvite['collection']),
                    array_keys($aOwnerBlogsClose['collection'])
                );
                $aCloseBlogs = array_diff($aCloseBlogs, $aOpenBlogs, $aOwnerBlogs);
            }
            /**
             * Сохраняем в кеш
             */
            if ($oUser) {
                $cache->Set(
                    $aCloseBlogs,
                    "blog_inaccessible_user_{$sUserId}",
                    ['blog_new', 'blog_update', "blog_relation_change_{$oUser->getId()}"],
                    60 * 60 * 24
                );
            } else {
                $cache->Set(
                    $aCloseBlogs,
                    "blog_inaccessible_user_{$sUserId}",
                    ['blog_new', 'blog_update'],
                    60 * 60 * 24 * 3
                );
            }
        }

        return $aCloseBlogs;
    }

    /**
     * Удаляет блог
     *
     * @param  int $iBlogId ID блога
     *
     * @return bool
     */
    public function DeleteBlog($iBlogId)
    {
        //FIXME: unreachable
        return false;

        /** @noinspection PhpUnreachableStatementInspection */
        if ($iBlogId instanceof EntityBlog) {
            $iBlogId = $iBlogId->getId();
        }
        /**
         * Получаем идентификаторы топиков блога. Удаляем топики блога.
         * При удалении топиков удаляются комментарии к ним и голоса.
         */
        /** @var ModuleTopic $topic */
        $topic = LS::Make(ModuleTopic::class);
        $aTopicIds = $topic->GetTopicsByBlogId($iBlogId);
        /**
         * Если блог не удален, возвращаем false
         */
        if (!$this->oMapperBlog->DeleteBlog($iBlogId)) {
            return false;
        }
        /**
         * Чистим кеш
         */
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            [
                "blog_update",
                "blog_relation_change_blog_{$iBlogId}",
                "topic_update",
                "comment_online_update_topic",
                "comment_update"
            ]
        );
        $cache->Delete("blog_{$iBlogId}");

        if (is_array($aTopicIds) and count($aTopicIds)) {
            /**
             * Удаляем топики
             */
            foreach ($aTopicIds as $iTopicId) {
                $cache->Delete("topic_{$iTopicId}");
                if (Config::Get('db.tables.engine') == "InnoDB") {
                    $topic->DeleteTopicAdditionalData($iTopicId);
                } else {
                    $topic->DeleteTopic($iTopicId);
                }
            }
        }
        /**
         * Удаляем связи пользователей блога.
         */
        if (Config::Get('db.tables.engine') != "InnoDB") {
            $this->oMapperBlog->DeleteBlogUsersByBlogId($iBlogId);
        }
        /**
         * Удаляем голосование за блог
         */
        LS::Make(ModuleVote::class)->DeleteVoteByTarget($iBlogId, 'blog');

        return true;
    }

    /**
     * Загружает аватар в блог
     *
     * @param array                    $aFile Массив $_FILES при загрузке аватара
     * @param \App\Entities\EntityBlog $oBlog Блог
     *
     * @return bool
     */
    public function UploadBlogAvatar($aFile, $oBlog)
    {
        if (!is_array($aFile) || !isset($aFile['tmp_name'])) {
            return false;
        }

        $sFileTmp = Config::Get('sys.cache.dir').func_generator();
        if (!move_uploaded_file($aFile['tmp_name'], $sFileTmp)) {
            return false;
        }

        /** @var \Engine\Modules\ModuleImage $image */
        $image = LS::Make(ModuleImage::class);

        $sPath = $image->GetIdDir($oBlog->getOwnerId())."blogs/".$oBlog->getId()."/";
        $aParams = $image->BuildParams('avatar');

        $oImage = $image->CreateImageObject($sFileTmp);
        /**
         * Если объект изображения не создан,
         * возвращаем ошибку
         */
        if ($sError = $oImage->get_last_error()) {
            // Вывод сообщения об ошибки, произошедшей при создании объекта изображения
            // LS::Make(ModuleMessage::class)->AddError($sError,LS::Make(ModuleLang::class)->Get('error'));
            @unlink($sFileTmp);

            return false;
        }
        /**
         * Срезаем квадрат
         */
        $oImage = $image->CropSquare($oImage);

        $aSize = Config::Get('module.blog.avatar_size');
        rsort($aSize, SORT_NUMERIC);
        $sSizeBig = array_shift($aSize);
        if ($oImage
            && $sFileAvatar = $image->Resize(
                $sFileTmp,
                $sPath,
                "avatar_blog_{$oBlog->getUrl()}_{$sSizeBig}x{$sSizeBig}",
                Config::Get('view.img_max_width'),
                Config::Get('view.img_max_height'),
                $sSizeBig,
                $sSizeBig,
                false,
                $aParams,
                $oImage
            )
        ) {
            foreach ($aSize as $iSize) {
                if ($iSize == 0) {
                    $image->Resize(
                        $sFileTmp,
                        $sPath,
                        "avatar_blog_{$oBlog->getUrl()}",
                        Config::Get('view.img_max_width'),
                        Config::Get('view.img_max_height'),
                        null,
                        null,
                        false,
                        $aParams,
                        $oImage
                    );
                } else {
                    $image->Resize(
                        $sFileTmp,
                        $sPath,
                        "avatar_blog_{$oBlog->getUrl()}_{$iSize}x{$iSize}",
                        Config::Get('view.img_max_width'),
                        Config::Get('view.img_max_height'),
                        $iSize,
                        $iSize,
                        false,
                        $aParams,
                        $oImage
                    );
                }
            }
            @unlink($sFileTmp);

            /**
             * Если все нормально, возвращаем расширение загруженного аватара
             */
            return $image->GetWebPath($sFileAvatar);
        }
        @unlink($sFileTmp);

        /**
         * В случае ошибки, возвращаем false
         */
        return false;
    }

    /**
     * Удаляет аватар блога с сервера
     *
     * @param \App\Entities\EntityBlog $oBlog Блог
     */
    public function DeleteBlogAvatar($oBlog)
    {
        /**
         * Если аватар есть, удаляем его и его рейсайзы
         */
        /** @var \Engine\Modules\ModuleImage $image */
        $image = LS::Make(ModuleImage::class);
        if ($oBlog->getAvatar()) {
            $aSize = array_merge(Config::Get('module.blog.avatar_size'), [48]);
            foreach ($aSize as $iSize) {
                $image->RemoveFile($image->GetServerPath($oBlog->getAvatarPath($iSize)));
            }
        }
    }

    /**
     * Пересчет количества топиков в блогах
     *
     * @return bool
     */
    public function RecalculateCountTopic()
    {
        //чистим зависимые кеши
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['blog_update']);

        return $this->oMapperBlog->RecalculateCountTopic();
    }

    /**
     * Пересчет количества топиков в конкретном блоге
     *
     * @param int $iBlogId ID блога
     *
     * @return bool
     */
    public function RecalculateCountTopicByBlogId($iBlogId)
    {
        //чистим зависимые кеши
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['blog_update', "blog_update_{$iBlogId}"]);
        $cache->Delete("blog_{$iBlogId}");

        return $this->oMapperBlog->RecalculateCountTopic($iBlogId);
    }

    /**
     * Алиас для корректной работы ORM
     *
     * @param array $aBlogId Список ID блогов
     *
     * @return array
     */
    public function GetBlogItemsByArrayId($aBlogId)
    {
        return $this->GetBlogsByArrayId($aBlogId);
    }
}
