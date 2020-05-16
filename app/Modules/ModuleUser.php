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

use App\Entities\EntityUser;
use App\Entities\EntityUserChangemail;
use App\Entities\EntityUserField;
use App\Entities\EntityUserFriend;
use App\Entities\EntityUserInvite;
use App\Entities\EntityUserNote;
use App\Entities\EntityUserReminder;
use App\Entities\EntityUserSession;
use App\Mappers\MapperUser;
use Engine\Config;
use Engine\Engine;
use Engine\LS;
use Engine\Module;
use Engine\Modules\ModuleCache;
use Engine\Modules\ModuleImage;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleSession;
use Engine\Modules\ModuleViewer;
use Engine\Router;
use Zend_Cache;

/**
 * Модуль для работы с пользователями
 *
 * @package modules.user
 * @since   1.0
 */
class ModuleUser extends Module
{
    /**
     * Статусы дружбы между пользователями
     */
    const USER_FRIEND_OFFER  = 1;
    const USER_FRIEND_ACCEPT = 2;
    const USER_FRIEND_DELETE = 4;
    const USER_FRIEND_REJECT = 8;
    const USER_FRIEND_NULL   = 16;

    /**
     * Биты привилегий пользователя
     */
    const USER_PRIV_MODERATOR = 0b01;
    const USER_PRIV_QUOTES    = 0b10;

    const TYPE_IGNORE_COMMENTS = 'comments';
    const TYPE_IGNORE_TOPICS   = 'topics';
    /**
     * Объект маппера
     *
     * @var \App\Mappers\MapperUser
     */
    protected $oMapper;
    /**
     * Объект текущего пользователя
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent = null;
    /**
     * Объект сессии текущего пользователя
     *
     * @var \App\Entities\EntityUserSession|null
     */
    protected $oSession = null;
    /**
     * Список типов пользовательских полей
     *
     * @var array
     */
    protected $aUserFieldTypes = [
        'social',
        'contact'
    ];

    /**
     * Инициализация
     *
     */
    public function Init()
    {
        $this->oMapper = Engine::MakeMapper(MapperUser::class);
        /**
         * Проверяем есть ли у юзера сессия, т.е. залогинен или нет
         */

        $sUserId = LS::Make(ModuleSession::class)->Get('user_id');
        $oUser = null;
        if ($sUserId and $oUser = $this->GetUserById($sUserId) and $oUser->getActivate()) {
            if ($this->oSession = $oUser->getSession()) {
                /**
                 * Сюда можно вставить условие на проверку айпишника сессии
                 */
                $this->oUserCurrent = $oUser;
            }
        }

        if ($this->oUserCurrent && $this->oUserCurrent->isBanned()) {
            LS::Make(ModuleMessage::class)->AddNoticeSingle($oUser->getBanComment());
            $this->Logout();
            LS::Make(ModuleSession::class)->DropSession();
            Router::Action('error');

            return;
        }

        /**
         * Запускаем автозалогинивание
         * В куках стоит время на сколько запоминать юзера
         */
        $this->AutoLogin();
        /**
         * Обновляем сессию
         */
        if (isset($this->oSession)) {
            $this->UpdateSession();
        }
    }

    /**
     * Возвращает список типов полей
     *
     * @return array
     */
    public function GetUserFieldTypes()
    {
        return $this->aUserFieldTypes;
    }

    /**
     * Добавляет новый тип с пользовательские поля
     *
     * @param string $sType Тип
     *
     * @return bool
     */
    public function AddUserFieldTypes($sType)
    {
        if (!in_array($sType, $this->aUserFieldTypes)) {
            $this->aUserFieldTypes[] = $sType;

            return true;
        }

        return false;
    }

    /**
     * Получает дополнительные данные(объекты) для юзеров по их ID
     *
     * @param array      $aUserId    Список ID пользователей
     * @param array|null $aAllowData Список типод дополнительных данных для подгрузки у пользователей
     *
     * @return array
     */
    public function GetUsersAdditionalData($aUserId, $aAllowData = null)
    {
        if (is_null($aAllowData)) {
            $aAllowData = ['vote', 'session', 'friend', 'geo_target', 'note', 'ban'];
        }
        func_array_simpleflip($aAllowData);
        if (!is_array($aUserId)) {
            $aUserId = [$aUserId];
        }
        /**
         * Получаем юзеров
         */
        $aUsers = $this->GetUsersByArrayId($aUserId);
        /**
         * Получаем дополнительные данные
         */
        $aSessions = [];
        $aFriends = [];
        $aVote = [];
        $aGeoTargets = [];
        $aNotes = [];
        if (isset($aAllowData['session'])) {
            $aSessions = $this->GetSessionsByArrayId($aUserId);
        }
        if (isset($aAllowData['friend']) and $this->oUserCurrent) {
            $aFriends = $this->GetFriendsByArray($aUserId, $this->oUserCurrent->getId());
        }

        if (isset($aAllowData['vote']) and $this->oUserCurrent) {
            $aVote = LS::Make(ModuleVote::class)->GetVoteByArray($aUserId, 'user', $this->oUserCurrent->getId());
        }
        if (isset($aAllowData['geo_target'])) {
            $aGeoTargets = LS::Make(ModuleGeo::class)->GetTargetsByTargetArray('user', $aUserId);
        }
        if (isset($aAllowData['note']) and $this->oUserCurrent) {
            $aNotes = $this->GetUserNotesByArray($aUserId, $this->oUserCurrent->getId());
        }
        /**
         * Добавляем данные к результату
         */
        foreach ($aUsers as $oUser) {
            if (isset($aSessions[$oUser->getId()])) {
                $oUser->setSession($aSessions[$oUser->getId()]);
            } else {
                $oUser->setSession(null); // или $oUser->setSession(new ModuleUser_EntitySession());
            }
            if ($aFriends && isset($aFriends[$oUser->getId()])) {
                $oUser->setUserFriend($aFriends[$oUser->getId()]);
            } else {
                $oUser->setUserFriend(null);
            }

            if (isset($aVote[$oUser->getId()])) {
                $oUser->setVote($aVote[$oUser->getId()]);
            } else {
                $oUser->setVote(null);
            }
            if (isset($aGeoTargets[$oUser->getId()])) {
                $aTargets = $aGeoTargets[$oUser->getId()];
                $oUser->setGeoTarget(isset($aTargets[0]) ? $aTargets[0] : null);
            } else {
                $oUser->setGeoTarget(null);
            }
            if (isset($aAllowData['note'])) {
                if (isset($aNotes[$oUser->getId()])) {
                    $oUser->setUserNote($aNotes[$oUser->getId()]);
                } else {
                    $oUser->setUserNote(false);
                }
            }
        }

        return $aUsers;
    }

    /**
     * Список юзеров по ID
     *
     * @param array $aUserId Список ID пользователей
     *
     * @return array
     */
    public function GetUsersByArrayId($aUserId)
    {
        if (!$aUserId) {
            return [];
        }
        if (Config::Get('sys.cache.solid')) {
            return $this->GetUsersByArrayIdSolid($aUserId);
        }
        if (!is_array($aUserId)) {
            $aUserId = [$aUserId];
        }
        $aUserId = array_unique($aUserId);
        $aUsers = [];
        $aUserIdNotNeedQuery = [];
        /**
         * Делаем мульти-запрос к кешу
         */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $aCacheKeys = func_build_cache_keys($aUserId, 'user_');
        if (false !== ($data = $cache->Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey]) {
                        $aUsers[$data[$sKey]->getId()] = $data[$sKey];
                    } else {
                        $aUserIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }
        /**
         * Смотрим каких юзеров не было в кеше и делаем запрос в БД
         */
        $aUserIdNeedQuery = array_diff($aUserId, array_keys($aUsers));
        $aUserIdNeedQuery = array_diff($aUserIdNeedQuery, $aUserIdNotNeedQuery);
        $aUserIdNeedStore = $aUserIdNeedQuery;
        if ($data = $this->oMapper->GetUsersByArrayId($aUserIdNeedQuery)) {
            foreach ($data as $oUser) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aUsers[$oUser->getId()] = $oUser;
                $cache->Set($oUser, "user_{$oUser->getId()}", [], 60 * 60 * 24 * 4);
                $aUserIdNeedStore = array_diff($aUserIdNeedStore, [$oUser->getId()]);
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aUserIdNeedStore as $sId) {
            $cache->Set(null, "user_{$sId}", [], 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aUsers = func_array_sort_by_keys($aUsers, $aUserId);

        return $aUsers;
    }

    /**
     * Алиас для корректной работы ORM
     *
     * @param array $aUserId Список ID пользователей
     *
     * @return array
     */
    public function GetUserItemsByArrayId($aUserId)
    {
        return $this->GetUsersByArrayId($aUserId);
    }

    /**
     * Получение пользователей по списку ID используя общий кеш
     *
     * @param array $aUserId Список ID пользователей
     *
     * @return array
     */
    public function GetUsersByArrayIdSolid($aUserId)
    {
        if (!is_array($aUserId)) {
            $aUserId = [$aUserId];
        }
        $aUserId = array_unique($aUserId);
        $aUsers = [];
        $s = join(',', $aUserId);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("user_id_{$s}"))) {
            $data = $this->oMapper->GetUsersByArrayId($aUserId);
            foreach ($data as $oUser) {
                $aUsers[$oUser->getId()] = $oUser;
            }
            $cache->Set($aUsers, "user_id_{$s}", ["user_update", "user_new"], 60 * 60 * 24 * 1);

            return $aUsers;
        }

        return $data;
    }

    /**
     * Список сессий юзеров по ID
     *
     * @param array $aUserId Список ID пользователей
     *
     * @return array
     */
    public function GetSessionsByArrayId($aUserId)
    {
        if (!$aUserId) {
            return [];
        }
        if (Config::Get('sys.cache.solid')) {
            return $this->GetSessionsByArrayIdSolid($aUserId);
        }
        if (!is_array($aUserId)) {
            $aUserId = [$aUserId];
        }
        $aUserId = array_unique($aUserId);
        $aSessions = [];
        $aUserIdNotNeedQuery = [];
        /**
         * Делаем мульти-запрос к кешу
         */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $aCacheKeys = func_build_cache_keys($aUserId, 'user_session_');
        if (false !== ($data = $cache->Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey] and $data[$sKey]['session']) {
                        $aSessions[$data[$sKey]['session']->getUserId()] = $data[$sKey]['session'];
                    } else {
                        $aUserIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }
        /**
         * Смотрим каких юзеров не было в кеше и делаем запрос в БД
         */
        $aUserIdNeedQuery = array_diff($aUserId, array_keys($aSessions));
        $aUserIdNeedQuery = array_diff($aUserIdNeedQuery, $aUserIdNotNeedQuery);
        $aUserIdNeedStore = $aUserIdNeedQuery;
        if ($data = $this->oMapper->GetSessionsByArrayId($aUserIdNeedQuery)) {
            foreach ($data as $oSession) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aSessions[$oSession->getUserId()] = $oSession;
                $cache->Set(
                    ['time' => time(), 'session' => $oSession],
                    "user_session_{$oSession->getUserId()}",
                    [],
                    60 * 60 * 24 * 4
                );
                $aUserIdNeedStore = array_diff($aUserIdNeedStore, [$oSession->getUserId()]);
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aUserIdNeedStore as $sId) {
            $cache->Set(['time' => time(), 'session' => null], "user_session_{$sId}", [], 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aSessions = func_array_sort_by_keys($aSessions, $aUserId);

        return $aSessions;
    }

    /**
     * Получить список сессий по списку айдишников, но используя единый кеш
     *
     * @param array $aUserId Список ID пользователей
     *
     * @return array
     */
    public function GetSessionsByArrayIdSolid($aUserId)
    {
        if (!is_array($aUserId)) {
            $aUserId = [$aUserId];
        }
        $aUserId = array_unique($aUserId);
        $aSessions = [];
        $s = join(',', $aUserId);
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("user_session_id_{$s}"))) {
            $data = $this->oMapper->GetSessionsByArrayId($aUserId);
            foreach ($data as $oSession) {
                $aSessions[$oSession->getUserId()] = $oSession;
            }
            $cache->Set($aSessions, "user_session_id_{$s}", ["user_session_update"], 60 * 60 * 24 * 1);

            return $aSessions;
        }

        return $data;
    }

    /**
     * Получает сессию юзера
     *
     * @param int $sUserId ID пользователя
     *
     * @return \App\Entities\EntityUserSession|null
     */
    public function GetSessionByUserId($sUserId)
    {
        $aSessions = $this->GetSessionsByArrayId($sUserId);
        if (isset($aSessions[$sUserId])) {
            return $aSessions[$sUserId];
        }

        return null;
    }

    /**
     * При завершенни модуля загружаем в шалон объект текущего юзера
     *
     */
    public function Shutdown()
    {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        if ($this->oUserCurrent) {
            $viewer->Assign(
                'iUserCurrentCountTalkNew',
                LS::Make(ModuleTalk::class)->GetCountTalkNew($this->oUserCurrent->getId())
            );
            $viewer->Assign(
                'iUserCurrentCountTopicDraft',
                LS::Make(ModuleTopic::class)->GetCountDraftTopicsByUserId($this->oUserCurrent->getId())
            );
        }
        $viewer->Assign('oUserCurrent', $this->oUserCurrent);
    }

    /**
     * Добавляет юзера
     *
     * @param \App\Entities\EntityUser $oUser Объект пользователя
     *
     * @return \App\Entities\EntityUser|bool
     */
    public function Add(EntityUser $oUser)
    {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if ($sId = $this->oMapper->Add($oUser)) {
            $oUser->setId($sId);
            //чистим зависимые кеши
            $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['user_new']);
            /**
             * Создаем персональный блог
             */
            foreach (Config::Get('autosubscribe') as $BlogForSuscribe) {
                LS::Make(ModuleUserfeed::class)->subscribeUser(
                    $oUser->getId(),
                    ModuleUserfeed::SUBSCRIBE_TYPE_BLOG,
                    $BlogForSuscribe
                );
            }
            $oUser->setSkill(5.000);
            $this->Update($oUser);

            return $oUser;
        }

        return false;
    }

    /**
     * Получить юзера по ключу активации
     *
     * @param string $sKey Ключ активации
     *
     * @return \App\Entities\EntityUser|null
     */
    public function GetUserByActivateKey($sKey)
    {
        $id = $this->oMapper->GetUserByActivateKey($sKey);

        return $this->GetUserById($id);
    }

    /**
     * Получить юзера по ключу сессии
     *
     * @param string $sKey Сессионный ключ
     *
     * @return \App\Entities\EntityUser|null
     */
    public function GetUserBySessionKey($sKey)
    {
        try {
            $payload = LS::Make(ModuleAuth::class)->VerifyKey($sKey, 'msgpack');
        } catch (AuthException $e) {
            return null;
        }

        return $this->GetUserById($payload['sub']);
    }

    /**
     * Получить юзера по мылу
     *
     * @param string $sMail Емайл
     *
     * @return \App\Entities\EntityUser|null
     */
    public function GetUserByMail($sMail)
    {
        $id = $this->oMapper->GetUserByMail($sMail);

        return $this->GetUserById($id);
    }

    /**
     * Получить юзера по логину
     *
     * @param string $sLogin Логин пользователя
     *
     * @return \App\Entities\EntityUser|null
     */
    public function GetUserByLogin($sLogin)
    {
        $s = strtolower($sLogin);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($id = $cache->Get("user_login_{$s}"))) {
            if ($id = $this->oMapper->GetUserByLogin($sLogin)) {
                $cache->Set($id, "user_login_{$s}", [], 60 * 60 * 24 * 1);
            }
        }

        return $this->GetUserById($id);
    }

    /**
     * Получить юзера по айдишнику
     *
     * @param int $sId ID пользователя
     *
     * @return \App\Entities\EntityUser|null
     */
    public function GetUserById($sId)
    {
        if (!is_numeric($sId)) {
            return null;
        }
        $aUsers = $this->GetUsersAdditionalData($sId);
        if (isset($aUsers[$sId])) {
            return $aUsers[$sId];
        }

        return null;
    }

    /**
     * Обновляет юзера
     *
     * @param \App\Entities\EntityUser $oUser Объект пользователя
     *
     * @return bool
     */
    public function Update(EntityUser $oUser)
    {
        //чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['user_update']);
        $cache->Delete("user_{$oUser->getId()}");

//        echo $oUser->getRank();
        return $this->oMapper->Update($oUser);
    }

    /**
     * Авторизовывает юзера
     *
     * @param \App\Entities\EntityUser $oUser     Объект пользователя
     * @param bool                     $bRemember Запоминать пользователя или нет
     * @param string                   $sKey      Ключ авторизации для куков
     *
     * @return bool
     */
    public function Authorization(EntityUser $oUser, $bRemember = true, $sKey = null)
    {
        if (!$oUser->getId() or !$oUser->getActivate()) {
            return false;
        }
        /**
         * Генерим новый ключ авторизаии для куков
         */
        if (is_null($sKey)) {
            $sKey = $this->GenerateUserKey($oUser);
        }
        /**
         * Создаём новую сессию
         */
        if (!$this->CreateSession($oUser)) {
            return false;
        }
        /**
         * Запоминаем в сесси юзера
         */
        LS::Make(ModuleSession::class)->Set('user_id', $oUser->getId());
        $this->oUserCurrent = $oUser;
        /**
         * Ставим куку
         */
        $expires = 0;
        if ($bRemember) {
            $expires = time() + Config::Get('sys.cookie.time');
        }
        setcookie_s(
            'key',
            $sKey,
            $expires,
            Config::Get('sys.cookie.path'),
            Config::Get('sys.cookie.host'),
            Config::Get('sys.cookie.secure'),
            Config::Get('sys.cookie.httponly'),
            "Lax"
        );

        return true;
    }

    /**
     * Автоматическое заллогинивание по ключу из куков
     *
     */
    protected function AutoLogin()
    {
        if ($this->oUserCurrent) {
            if (isset($_COOKIE['key'])) {
                if (!$this->CheckUserKey($_COOKIE['key'], $this->oUserCurrent->getId())) {
                    $this->Logout();
                }
            }

            return;
        }
        if (isset($_COOKIE['key']) and is_string($_COOKIE['key']) and $sKey = $_COOKIE['key']) {
            $oUser = $this->GetUserBySessionKey($sKey);
            if ($oUser == null) {
                $this->Logout();

                return;
            }
            if ($this->CheckUserKey($sKey, $oUser->getId())) {
                $this->Authorization($oUser);
            } else {
                $this->Logout();
            }
        }
    }


    /**
     * Авторизован ли юзер
     *
     * @return bool
     */
    public
    function IsAuthorization()
    {
        if ($this->oUserCurrent) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получить текущего юзера
     *
     * @return \App\Entities\EntityUser|null
     */
    public
    function GetUserCurrent()
    {
        return $this->oUserCurrent;
    }

    /**
     * Разлогинивание
     *
     */
    public
    function Logout()
    {
        $this->oUserCurrent = null;
        $this->oSession = null;
        /**
         * Дропаем из сессии
         */
        LS::Make(ModuleSession::class)->Drop('user_id');
        /**
         * Дропаем куку
         */
        setcookie('key', '', 1, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
        setcookie('wskey', '', 1, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
    }

    /**
     * Обновление данных сессии
     * Важный момент: сессию обновляем в кеше и раз в 10 минут скидываем в БД
     */
    protected
    function UpdateSession()
    {
        $this->oSession->setDateLast(date("Y-m-d H:i:s"));
        $this->oSession->setIpLast(func_getIp());
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("user_session_{$this->oSession->getUserId()}"))) {
            $data = [
                'time'    => time(),
                'session' => $this->oSession
            ];
        } else {
            $data['session'] = $this->oSession;
        }
        if (!Config::Get('sys.cache.use') or $data['time'] < time() - 60 * 10) {
            $data['time'] = time();
            $this->oMapper->UpdateSession($this->oSession);
            $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['user_session_update']);
        }
        $cache->Set($data, "user_session_{$this->oSession->getUserId()}", [], 60 * 60 * 24 * 4);
    }

    /**
     * Создание пользовательской сессии
     *
     * @param \App\Entities\EntityUser $oUser Объект пользователя
     * @param string                   $sKey  Сессионный ключ
     *
     * @return bool
     */
    protected
    function CreateSession(
        EntityUser $oUser
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['user_session_update']);
        $cache->Delete("user_session_{$oUser->getId()}");
        $oSession = new EntityUserSession();
        $oSession->setUserId($oUser->getId());
        $oSession->setIpLast(func_getIp());
        $oSession->setIpCreate(func_getIp());
        $oSession->setDateLast(date("Y-m-d H:i:s"));
        $oSession->setDateCreate(date("Y-m-d H:i:s"));
        if ($this->oMapper->CreateSession($oSession)) {
            $this->oSession = $oSession;

            return true;
        }

        return false;
    }

    /**
     * Получить список юзеров по дате последнего визита
     *
     * @param int $iLimit Количество
     *
     * @return array
     */
    public
    function GetUsersByDateLast(
        $iLimit = 20
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if ($this->IsAuthorization()) {
            $data = $this->oMapper->GetUsersByDateLast($iLimit);
        } elseif (false === ($data = $cache->Get("user_date_last_{$iLimit}"))) {
            $data = $this->oMapper->GetUsersByDateLast($iLimit);
            $cache->Set($data, "user_date_last_{$iLimit}", ["user_session_update"], 60 * 60 * 24 * 2);
        }
        $data = $this->GetUsersAdditionalData($data);

        return $data;
    }

    /**
     * Возвращает список пользователей по фильтру
     *
     * @param array $aFilter    Фильтр
     * @param array $aOrder     Сортировка
     * @param int   $iCurrPage  Номер страницы
     * @param int   $iPerPage   Количество элментов на страницу
     * @param array $aAllowData Список типо данных для подгрузки к пользователям
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public
    function GetUsersByFilter(
        $aFilter,
        $aOrder,
        $iCurrPage,
        $iPerPage,
        $aAllowData = null
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $sKey = "user_filter_".serialize($aFilter).serialize($aOrder)."_{$iCurrPage}_{$iPerPage}";
        if (false === ($data = $cache->Get($sKey))) {
            $data = [
                'collection' => $this->oMapper->GetUsersByFilter($aFilter, $aOrder, $iCount, $iCurrPage, $iPerPage),
                'count'      => $iCount
            ];
            $cache->Set($data, $sKey, ["user_update", "user_new"], 60 * 60 * 24 * 2);
        }
        $data['collection'] = $this->GetUsersAdditionalData($data['collection'], $aAllowData);

        return $data;
    }

    /**
     * Получить список юзеров по дате регистрации
     *
     * @param int $iLimit Количество
     *
     * @return array
     */
    public
    function GetUsersByDateRegister(
        $iLimit = 20
    ) {
        $aResult = $this->GetUsersByFilter(['activate' => 1], ['id' => 'desc'], 1, $iLimit);

        return $aResult['collection'];
    }

    /**
     * Получить статистику по юзерам
     *
     * @return array
     */
    public
    function GetStatUsers()
    {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($aStat = $cache->Get("user_stats"))) {
            $aStat['count_all'] = $this->oMapper->GetCountUsers();
            $sDateWeek = date("Y-m-d H:i:s", time() - 60 * 60 * 24 * 7);
            $sDateDay = date("Y-m-d H:i:s", time() - 60 * 60 * 24);
            $sDateHour = date("Y-m-d H:i:s", time() - 60 * 60);
            $aStat['count_active_week'] = $this->oMapper->GetCountUsersActive($sDateWeek);
            $aStat['count_active_day'] = $this->oMapper->GetCountUsersActive($sDateDay);
            $aStat['count_active_hour'] = $this->oMapper->GetCountUsersActive($sDateHour);
            $aSex = $this->oMapper->GetCountUsersSex();
            $aStat['count_sex_man'] = (isset($aSex['man']) ? $aSex['man']['count'] : 0);
            $aStat['count_sex_woman'] = (isset($aSex['woman']) ? $aSex['woman']['count'] : 0);
            $aStat['count_sex_other'] = (isset($aSex['other']) ? $aSex['other']['count'] : 0);

            $cache->Set($aStat, "user_stats", ["user_update", "user_new"], 60 * 60 * 24 * 4);
        }

        return $aStat;
    }

    /**
     * Получить список юзеров по первым  буквам логина
     *
     * @param string $sUserLogin Логин
     * @param int    $iLimit     Количество
     *
     * @return array
     */
    public
    function GetUsersByLoginLike(
        $sUserLogin,
        $iLimit
    ) {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("user_like_{$sUserLogin}_{$iLimit}"))) {
            $data = $this->oMapper->GetUsersByLoginLike($sUserLogin, $iLimit);
            $cache->Set($data, "user_like_{$sUserLogin}_{$iLimit}", ["user_new"], 60 * 60 * 24 * 2);
        }
        $data = $this->GetUsersAdditionalData($data);

        return $data;
    }

    /**
     * Получить список отношений друзей
     *
     * @param  array $aUserId Список ID пользователей проверяемых на дружбу
     * @param  int   $sUserId ID пользователя у которого проверяем друзей
     *
     * @return array
     */
    public
    function GetFriendsByArray(
        $aUserId,
        $sUserId
    ) {
        if (!$aUserId) {
            return [];
        }
        if (Config::Get('sys.cache.solid')) {
            return $this->GetFriendsByArraySolid($aUserId, $sUserId);
        }
        if (!is_array($aUserId)) {
            $aUserId = [$aUserId];
        }
        $aUserId = array_unique($aUserId);
        $aFriends = [];
        $aUserIdNotNeedQuery = [];
        /**
         * Делаем мульти-запрос к кешу
         */
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $aCacheKeys = func_build_cache_keys($aUserId, 'user_friend_', '_'.$sUserId);
        if (false !== ($data = $cache->Get($aCacheKeys))) {
            /**
             * проверяем что досталось из кеша
             */
            foreach ($aCacheKeys as $sValue => $sKey) {
                if (array_key_exists($sKey, $data)) {
                    if ($data[$sKey]) {
                        $aFriends[$data[$sKey]->getFriendId()] = $data[$sKey];
                    } else {
                        $aUserIdNotNeedQuery[] = $sValue;
                    }
                }
            }
        }
        /**
         * Смотрим каких френдов не было в кеше и делаем запрос в БД
         */
        $aUserIdNeedQuery = array_diff($aUserId, array_keys($aFriends));
        $aUserIdNeedQuery = array_diff($aUserIdNeedQuery, $aUserIdNotNeedQuery);
        $aUserIdNeedStore = $aUserIdNeedQuery;
        if ($data = $this->oMapper->GetFriendsByArrayId($aUserIdNeedQuery, $sUserId)) {
            foreach ($data as $oFriend) {
                /**
                 * Добавляем к результату и сохраняем в кеш
                 */
                $aFriends[$oFriend->getFriendId($sUserId)] = $oFriend;
                /**
                 * Тут кеш нужно будет продумать как-то по другому.
                 * Пока не трогаю, ибо этот код все равно не выполняется.
                 * by Kachaev
                 */
                $cache->Set(
                    $oFriend,
                    "user_friend_{$oFriend->getFriendId()}_{$oFriend->getUserId()}",
                    [],
                    60 * 60 * 24 * 4
                );
                $aUserIdNeedStore = array_diff($aUserIdNeedStore, [$oFriend->getFriendId()]);
            }
        }
        /**
         * Сохраняем в кеш запросы не вернувшие результата
         */
        foreach ($aUserIdNeedStore as $sId) {
            $cache->Set(null, "user_friend_{$sId}_{$sUserId}", [], 60 * 60 * 24 * 4);
        }
        /**
         * Сортируем результат согласно входящему массиву
         */
        $aFriends = func_array_sort_by_keys($aFriends, $aUserId);

        return $aFriends;
    }

    /**
     * Получить список отношений друзей используя единый кеш
     *
     * @param  array $aUserId Список ID пользователей проверяемых на дружбу
     * @param  int   $sUserId ID пользователя у которого проверяем друзей
     *
     * @return array
     */
    public
    function GetFriendsByArraySolid(
        $aUserId,
        $sUserId
    ) {
        if (!is_array($aUserId)) {
            $aUserId = [$aUserId];
        }
        $aUserId = array_unique($aUserId);
        $aFriends = [];
        $s = join(',', $aUserId);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("user_friend_{$sUserId}_id_{$s}"))) {
            $data = $this->oMapper->GetFriendsByArrayId($aUserId, $sUserId);
            foreach ($data as $oFriend) {
                $aFriends[$oFriend->getFriendId($sUserId)] = $oFriend;
            }

            $cache->Set(
                $aFriends,
                "user_friend_{$sUserId}_id_{$s}",
                ["friend_change_user_{$sUserId}"],
                60 * 60 * 24 * 1
            );

            return $aFriends;
        }

        return $data;
    }

    /**
     * Получаем привязку друга к юзеру(есть ли у юзера данный друг)
     *
     * @param  int $sFriendId ID пользователя друга
     * @param  int $sUserId   ID пользователя
     *
     * @return EntityUserFriend|null
     */
    public
    function GetFriend(
        $sFriendId,
        $sUserId
    ) {
        $data = $this->GetFriendsByArray($sFriendId, $sUserId);
        if (isset($data[$sFriendId])) {
            return $data[$sFriendId];
        }

        return null;
    }

    /**
     * Добавляет друга
     *
     * @param  EntityUserFriend $oFriend Объект дружбы(связи пользователей)
     *
     * @return bool
     */
    public
    function AddFriend(
        EntityUserFriend $oFriend
    ) {
        //чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            ["friend_change_user_{$oFriend->getUserFrom()}", "friend_change_user_{$oFriend->getUserTo()}"]
        );
        $cache->Delete("user_friend_{$oFriend->getUserFrom()}_{$oFriend->getUserTo()}");
        $cache->Delete("user_friend_{$oFriend->getUserTo()}_{$oFriend->getUserFrom()}");

        return $this->oMapper->AddFriend($oFriend);
    }

    /**
     * Удаляет друга
     *
     * @param  EntityUserFriend $oFriend Объект дружбы(связи пользователей)
     *
     * @return bool
     */
    public
    function DeleteFriend(
        EntityUserFriend $oFriend
    ) {
        //чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            ["friend_change_user_{$oFriend->getUserFrom()}", "friend_change_user_{$oFriend->getUserTo()}"]
        );
        $cache->Delete("user_friend_{$oFriend->getUserFrom()}_{$oFriend->getUserTo()}");
        $cache->Delete("user_friend_{$oFriend->getUserTo()}_{$oFriend->getUserFrom()}");

        // устанавливаем статус дружбы "удалено"
        $oFriend->setStatusByUserId(ModuleUser::USER_FRIEND_DELETE, $oFriend->getUserId());

        return $this->oMapper->UpdateFriend($oFriend);
    }

    /**
     * Удаляет информацию о дружбе из базы данных
     *
     * @param  \App\Entities\EntityUserFriend $oFriend Объект дружбы(связи пользователей)
     *
     * @return bool
     */
    public
    function EraseFriend(
        EntityUserFriend $oFriend
    ) {
        //чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            ["friend_change_user_{$oFriend->getUserFrom()}", "friend_change_user_{$oFriend->getUserTo()}"]
        );
        $cache->Delete("user_friend_{$oFriend->getUserFrom()}_{$oFriend->getUserTo()}");
        $cache->Delete("user_friend_{$oFriend->getUserTo()}_{$oFriend->getUserFrom()}");

        return $this->oMapper->EraseFriend($oFriend);
    }

    /**
     * Обновляет информацию о друге
     *
     * @param  \App\Entities\EntityUserFriend $oFriend Объект дружбы(связи пользователей)
     *
     * @return bool
     */
    public
    function UpdateFriend(
        EntityUserFriend $oFriend
    ) {
        //чистим зависимые кеши
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            ["friend_change_user_{$oFriend->getUserFrom()}", "friend_change_user_{$oFriend->getUserTo()}"]
        );
        $cache->Delete("user_friend_{$oFriend->getUserFrom()}_{$oFriend->getUserTo()}");
        $cache->Delete("user_friend_{$oFriend->getUserTo()}_{$oFriend->getUserFrom()}");

        return $this->oMapper->UpdateFriend($oFriend);
    }

    /**
     * Получает список друзей
     *
     * @param  int $sUserId  ID пользователя
     * @param  int $iPage    Номер страницы
     * @param  int $iPerPage Количество элементов на страницу
     *
     * @return array
     */
    public
    function GetUsersFriend(
        $sUserId,
        $iPage = 1,
        $iPerPage = 10
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $sKey = "user_friend_{$sUserId}_{$iPage}_{$iPerPage}";
        if (false === ($data = $cache->Get($sKey))) {
            $data = [
                'collection' => $this->oMapper->GetUsersFriend($sUserId, $iCount, $iPage, $iPerPage),
                'count'      => $iCount
            ];
            $cache->Set($data, $sKey, ["friend_change_user_{$sUserId}"], 60 * 60 * 24 * 2);
        }
        $data['collection'] = $this->GetUsersAdditionalData($data['collection']);

        return $data;
    }

    /**
     * Получает количество друзей
     *
     * @param  int $sUserId ID пользователя
     *
     * @return int
     */
    public
    function GetCountUsersFriend(
        $sUserId
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $sKey = "count_user_friend_{$sUserId}";
        if (false === ($data = $cache->Get($sKey))) {
            $data = $this->oMapper->GetCountUsersFriend($sUserId);
            $cache->Set($data, $sKey, ["friend_change_user_{$sUserId}"], 60 * 60 * 24 * 2);
        }

        return $data;
    }

    /**
     * Получает инвайт по его коду
     *
     * @param  string $sCode Код инвайта
     * @param  int    $iUsed Флаг испольщования инвайта
     *
     * @return \App\Entities\EntityUserInvite|null
     * @throws \Exception
     */
    public
    function GetInviteByCode(
        $sCode,
        $iUsed = 0
    ) {
        return $this->oMapper->GetInviteByCode($sCode, $iUsed);
    }

    /**
     * Добавляет новый инвайт
     *
     * @param \App\Entities\EntityUserInvite $oInvite Объект инвайта
     *
     * @return \App\Entities\EntityUserInvite|bool
     */
    public
    function AddInvite(
        EntityUserInvite $oInvite
    ) {
        if ($sId = $this->oMapper->AddInvite($oInvite)) {
            $oInvite->setId($sId);

            return $oInvite;
        }

        return false;
    }

    /**
     * Обновляет инвайт
     *
     * @param \App\Entities\EntityUserInvite $oInvite бъект инвайта
     *
     * @return bool
     */
    public
    function UpdateInvite(
        EntityUserInvite $oInvite
    ) {
        //чистим зависимые кеши
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            ["invate_new_to_{$oInvite->getUserToId()}", "invate_new_from_{$oInvite->getUserFromId()}"]
        );

        return $this->oMapper->UpdateInvite($oInvite);
    }

    /**
     * Генерирует новый инвайт
     *
     * @param \App\Entities\EntityUser $oUser Объект пользователя
     *
     * @return EntityUserInvite|bool
     */
    public
    function GenerateInvite(
        $oUser
    ) {
        $oInvite = new EntityUserInvite();
        $oInvite->setCode(func_generator(32));
        $oInvite->setDateAdd(date("Y-m-d H:i:s"));
        $oInvite->setUserFromId($oUser->getId());

        return $this->AddInvite($oInvite);
    }

    /**
     * Получает число использованых приглашений юзером за определенную дату
     *
     * @param int    $sUserIdFrom ID пользователя
     * @param string $sDate       Дата
     *
     * @return int
     */
    public
    function GetCountInviteUsedByDate(
        $sUserIdFrom,
        $sDate
    ) {
        return $this->oMapper->GetCountInviteUsedByDate($sUserIdFrom, $sDate);
    }

    /**
     * Получает полное число использованных приглашений юзера
     *
     * @param int $sUserIdFrom ID пользователя
     *
     * @return int
     */
    public
    function GetCountInviteUsed(
        $sUserIdFrom
    ) {
        return $this->oMapper->GetCountInviteUsed($sUserIdFrom);
    }

    /**
     * Получаем число доступных приглашений для юзера
     *
     * @param \App\Entities\EntityUser $oUserFrom Объект пользователя
     *
     * @return int
     */
    public
    function GetCountInviteAvailable(
        EntityUser $oUserFrom
    ) {
        $sDay = 7;
        $iCountUsed = $this->GetCountInviteUsedByDate(
            $oUserFrom->getId(),
            date("Y-m-d 00:00:00", mktime(0, 0, 0, date("m"), date("d") - $sDay, date("Y")))
        );
        $iCountAllAvailable = round((float)$oUserFrom->getRating() + (float)$oUserFrom->getSkill());
        $iCountAllAvailable = $iCountAllAvailable < 0 ? 0 : $iCountAllAvailable;
        $iCountAvailable = $iCountAllAvailable - $iCountUsed;
        $iCountAvailable = $iCountAvailable < 0 ? 0 : $iCountAvailable;

        return $iCountAvailable;
    }

    /**
     * Получает список приглашенных юзеров
     *
     * @param int $sUserId ID пользователя
     *
     * @return array
     */
    public
    function GetUsersInvite(
        $sUserId
    ) {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("users_invite_{$sUserId}"))) {
            $data = $this->oMapper->GetUsersInvite($sUserId);
            $cache->Set($data, "users_invite_{$sUserId}", ["invate_new_from_{$sUserId}"], 60 * 60 * 24 * 1);
        }
        $data = $this->GetUsersAdditionalData($data);

        return $data;
    }

    /**
     * Получает юзера который пригласил
     *
     * @param int $sUserIdTo ID пользователя
     *
     * @return \App\Entities\EntityUser|null
     */
    public
    function GetUserInviteFrom(
        $sUserIdTo
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($id = $cache->Get("user_invite_from_{$sUserIdTo}"))) {
            $id = $this->oMapper->GetUserInviteFrom($sUserIdTo);
            $cache->Set($id, "user_invite_from_{$sUserIdTo}", ["invate_new_to_{$sUserIdTo}"], 60 * 60 * 24 * 1);
        }

        return $this->GetUserById($id);
    }

    /**
     * Добавляем воспоминание(восстановление) пароля
     *
     * @param \App\Entities\EntityUserReminder $oReminder Объект восстановления пароля
     *
     * @return bool
     */
    public
    function AddReminder(
        EntityUserReminder $oReminder
    ) {
        return $this->oMapper->AddReminder($oReminder);
    }

    /**
     * Сохраняем воспомнинание(восстановление) пароля
     *
     * @param \App\Entities\EntityUserReminder $oReminder Объект восстановления пароля
     *
     * @return bool
     */
    public
    function UpdateReminder(
        EntityUserReminder $oReminder
    ) {
        return $this->oMapper->UpdateReminder($oReminder);
    }

    /**
     * Получаем запись восстановления пароля по коду
     *
     * @param string $sCode Код восстановления пароля
     *
     * @return \App\Entities\EntityUserReminder|null
     */
    public
    function GetReminderByCode(
        $sCode
    ) {
        return $this->oMapper->GetReminderByCode($sCode);
    }

    /**
     * Загрузка аватара пользователя
     *
     * @param  string                   $sFileTmp Серверный путь до временного аватара
     * @param  \App\Entities\EntityUser $oUser    Объект пользователя
     * @param  array                    $aSize    Размер области из которой нужно вырезать картинку -
     *                                            array('x1'=>0,'y1'=>0,'x2'=>100,'y2'=>100)
     *
     * @return string|bool
     */
    public
    function UploadAvatar(
        $sFileTmp,
        $oUser,
        $aSize = []
    ) {
        if (!file_exists($sFileTmp)) {
            return false;
        }
        /** @var \Engine\Modules\ModuleImage $image */
        $image = LS::Make(ModuleImage::class);
        $sPath = $image->GetIdDir($oUser->getId())."users/".$oUser->getId()."/";
        $aParams = $image->BuildParams('avatar');

        /**
         * Срезаем квадрат
         */
        $oImage = $image->CreateImageObject($sFileTmp);
        /**
         * Если объект изображения не создан,
         * возвращаем ошибку
         */
        if ($sError = $oImage->get_last_error()) {
            // Вывод сообщения об ошибки, произошедшей при создании объекта изображения
            // $this->Message_AddError($sError,LS::Make(ModuleLang::class)->Get('error'));
            @unlink($sFileTmp);

            return false;
        }

        if (!$aSize) {
            $oImage = $image->CropSquare($oImage);
            $oImage->set_jpg_quality($aParams['jpg_quality']);
            $oImage->output(null, $sFileTmp);
        } else {
            $iWSource = $oImage->get_image_params('width');
            $iHSource = $oImage->get_image_params('height');
            /**
             * Достаем переменные x1 и т.п. из $aSize
             */
            list($x1, $x2, $y1, $y2) = [$aSize['x1'], $aSize['x2'], $aSize['y1'], $aSize['y2']];
            if ($x1 > $x2) {
                // меняем значения переменных
                $x1 = $x1 + $x2;
                $x2 = $x1 - $x2;
                $x1 = $x1 - $x2;
            }
            if ($y1 > $y2) {
                $y1 = $y1 + $y2;
                $y2 = $y1 - $y2;
                $y1 = $y1 - $y2;
            }
            if ($x1 < 0) {
                $x1 = 0;
            }
            if ($y1 < 0) {
                $y1 = 0;
            }
            if ($x2 > $iWSource) {
                $x2 = $iWSource;
            }
            if ($y2 > $iHSource) {
                $y2 = $iHSource;
            }

            $iW = $x2 - $x1;
            // Допускаем минимальный клип в 32px (исключая маленькие изображения)
            if ($iW < 32 && $x1 + 32 <= $iWSource) {
                $iW = 32;
            }
            $iH = $iW;
            if ($iH + $y1 > $iHSource) {
                $iH = $iHSource - $y1;
            }
            $oImage->crop($iW, $iH, $x1, $y1);
            $oImage->output(null, $sFileTmp);
        }

        if ($sFileAvatar = $image->Resize(
            $sFileTmp,
            $sPath,
            'avatar_100x100',
            Config::Get('view.img_max_width'),
            Config::Get('view.img_max_height'),
            100,
            100,
            false,
            $aParams
        )
        ) {
            $aSize = Config::Get('module.user.avatar_size');
            foreach ($aSize as $iSize) {
                if ($iSize == 0) {
                    $image->Resize(
                        $sFileTmp,
                        $sPath,
                        'avatar',
                        Config::Get('view.img_max_width'),
                        Config::Get('view.img_max_height'),
                        null,
                        null,
                        false,
                        $aParams
                    );
                } else {
                    $image->Resize(
                        $sFileTmp,
                        $sPath,
                        "avatar_{$iSize}x{$iSize}",
                        Config::Get('view.img_max_width'),
                        Config::Get('view.img_max_height'),
                        $iSize,
                        $iSize,
                        false,
                        $aParams
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
     * Удаляет аватар пользователя
     *
     * @param \App\Entities\EntityUser $oUser Объект пользователя
     */
    public
    function DeleteAvatar(
        $oUser
    ) {
        /**
         * Если аватар есть, удаляем его и его рейсайзы
         */
        if ($oUser->getProfileAvatar()) {
            /** @var ModuleImage $image */
            $image = LS::Make(ModuleImage::class);
            $aSize = array_merge(Config::Get('module.user.avatar_size'), [100]);
            foreach ($aSize as $iSize) {
                $image->RemoveFile($image->GetServerPath($oUser->getProfileAvatarPath($iSize)));
            }
        }
    }

    /**
     * загрузка фотографии пользователя
     *
     * @param  string                   $sFileTmp Серверный путь до временной фотографии
     * @param  \App\Entities\EntityUser $oUser    Объект пользователя
     * @param  array                    $aSize    Размер области из которой нужно вырезать картинку -
     *                                            array('x1'=>0,'y1'=>0,'x2'=>100,'y2'=>100)
     *
     * @return string|bool
     */
    public
    function UploadFoto(
        $sFileTmp,
        $oUser,
        $aSize = []
    ) {
        if (!file_exists($sFileTmp)) {
            return false;
        }
        /** @var \Engine\Modules\ModuleImage $image */
        $image = LS::Make(ModuleImage::class);
        $sDirUpload = $image->GetIdDir($oUser->getId());
        $aParams = $image->BuildParams('foto');


        if ($aSize) {
            $oImage = $image->CreateImageObject($sFileTmp);
            /**
             * Если объект изображения не создан,
             * возвращаем ошибку
             */
            if ($sError = $oImage->get_last_error()) {
                // Вывод сообщения об ошибки, произошедшей при создании объекта изображения
                // $this->Message_AddError($sError,LS::Make(ModuleLang::class)->Get('error'));
                @unlink($sFileTmp);

                return false;
            }

            list($x1, $x2, $y1, $y2) = [$aSize['x1'], $aSize['x2'], $aSize['y1'], $aSize['y2']];

            if (($y1 + 200 * ($x2 / 1340)) > $y2) {
                $y1 = $y2 - 200 * ($x2 / 1340);
            }
            $oImage->crop($x2, 200 * ($x2 / 1340), 0, $y1);
            $oImage->output(null, $sFileTmp);
        }

        if ($sFileFoto = $image->Resize(
            $sFileTmp,
            $sDirUpload,
            func_generator(6),
            Config::Get('view.img_max_width'),
            Config::Get('view.img_max_height'),
            Config::Get('module.user.profile_photo_width'),
            null,
            true,
            $aParams
        )
        ) {
            @unlink($sFileTmp);
            /**
             * удаляем старое фото
             */
            $this->DeleteFoto($oUser);

            return $image->GetWebPath($sFileFoto);
        }
        @unlink($sFileTmp);

        return false;
    }

    /**
     * Удаляет фото пользователя
     *
     * @param \App\Entities\EntityUser $oUser
     */
    public
    function DeleteFoto(
        $oUser
    ) {
        /** @var \Engine\Modules\ModuleImage $image */
        $image = LS::Make(ModuleImage::class);
        $image->RemoveFile($image->GetServerPath($oUser->getProfileFoto()));
    }

    const CHECK_LOGIN_SUCCESS = 0;
    const CHECK_LOGIN_LENGTH = 1;
    const CHECK_LOGIN_MIXED = 2;
    const CHECK_LOGIN_WRONG = 3;

    /**
     * Проверяет логин на корректность
     *
     * @param string $sLogin Логин пользователя
     *
     * @return int
     */
    public
    function CheckLogin(
        $sLogin
    ) {
        if (mb_strlen($sLogin) < Config::Get('module.user.login.min_size')
            || mb_strlen($sLogin) > Config::Get('module.user.login.max_size')
        ) {
            return self::CHECK_LOGIN_LENGTH;
        }
        if (preg_match("/^[0-9a-zа-яё\_\-]+$/iu", $sLogin)) {
            if (preg_match("/^[0-9а-яё\_\-]+$/iu", $sLogin)) {
                return self::CHECK_LOGIN_SUCCESS;
            }
            if (preg_match("/^[0-9a-z\_\-]+$/iu", $sLogin)) {
                return self::CHECK_LOGIN_SUCCESS;
            }

            return self::CHECK_LOGIN_MIXED;
        }

        return self::CHECK_LOGIN_WRONG;
    }

    /**
     * Получить дополнительные поля профиля пользователя
     *
     * @param array|null $aType Типы полей, null - все типы
     *
     * @return array
     */
    public
    function getUserFields(
        $aType = null
    ) {
        return $this->oMapper->getUserFields($aType);
    }

    /**
     * Получить значения дополнительных полей профиля пользователя
     *
     * @param int   $iUserId      ID пользователя
     * @param bool  $bOnlyNoEmpty Загружать только непустые поля
     * @param array $aType        Типы полей, null - все типы
     *
     * @return array
     */
    public
    function getUserFieldsValues(
        $iUserId,
        $bOnlyNoEmpty = true,
        $aType = ['']
    ) {
        return $this->oMapper->getUserFieldsValues($iUserId, $bOnlyNoEmpty, $aType);
    }

    /**
     * Получить по имени поля его значение дял определённого пользователя
     *
     * @param int    $iUserId ID пользователя
     * @param string $sName   Имя поля
     *
     * @return string
     */
    public
    function getUserFieldValueByName(
        $iUserId,
        $sName
    ) {
        return $this->oMapper->getUserFieldValueByName($iUserId, $sName);
    }

    /**
     * Установить значения дополнительных полей профиля пользователя
     *
     * @param int   $iUserId   ID пользователя
     * @param array $aFields   Ассоциативный массив полей id => value
     * @param int   $iCountMax Максимальное количество одинаковых полей
     */
    public
    function setUserFieldsValues(
        $iUserId,
        $aFields,
        $iCountMax = 1
    ) {
        $this->oMapper->setUserFieldsValues($iUserId, $aFields, $iCountMax);
    }

    /**
     * Добавить поле
     *
     * @param \App\Entities\EntityUserField $oField Объект пользовательского поля
     *
     * @return bool
     */
    public
    function addUserField(
        $oField
    ) {
        return $this->oMapper->addUserField($oField);
    }

    /**
     * Изменить поле
     *
     * @param EntityUserField $oField Объект пользовательского поля
     *
     * @return bool
     */
    public
    function updateUserField(
        $oField
    ) {
        return $this->oMapper->updateUserField($oField);
    }

    /**
     * Удалить поле
     *
     * @param int $iId ID пользовательского поля
     *
     * @return bool
     */
    public
    function deleteUserField(
        $iId
    ) {
        return $this->oMapper->deleteUserField($iId);
    }

    /**
     * Проверяет существует ли поле с таким именем
     *
     * @param string   $sName Имя поля
     * @param int|null $iId   ID поля
     *
     * @return bool
     */
    public
    function userFieldExistsByName(
        $sName,
        $iId = null
    ) {
        return $this->oMapper->userFieldExistsByName($sName, $iId);
    }

    /**
     * Проверяет существует ли поле с таким ID
     *
     * @param int $iId ID поля
     *
     * @return bool
     */
    public
    function userFieldExistsById(
        $iId
    ) {
        return $this->oMapper->userFieldExistsById($iId);
    }

    /**
     * Удаляет у пользователя значения полей
     *
     * @param int        $iUserId ID пользователя
     * @param array|null $aType   Список типов для удаления
     *
     * @return bool
     */
    public
    function DeleteUserFieldValues(
        $iUserId,
        $aType = null
    ) {
        return $this->oMapper->DeleteUserFieldValues($iUserId, $aType);
    }

    /**
     * Возвращает список заметок пользователя
     *
     * @param int $iUserId   ID пользователя
     * @param int $iCurrPage Номер страницы
     * @param int $iPerPage  Количество элементов на страницу
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public
    function GetUserNotesByUserId(
        $iUserId,
        $iCurrPage,
        $iPerPage
    ) {
        $aResult = $this->oMapper->GetUserNotesByUserId($iUserId, $iCount, $iCurrPage, $iPerPage);
        /**
         * Цепляем пользователей
         */
        $aUserId = [];
        foreach ($aResult as $oNote) {
            $aUserId[] = $oNote->getTargetUserId();
        }
        $aUsers = $this->GetUsersAdditionalData($aUserId, []);
        foreach ($aResult as $oNote) {
            if (isset($aUsers[$oNote->getTargetUserId()])) {
                $oNote->setTargetUser($aUsers[$oNote->getTargetUserId()]);
            } else {
                $oNote->setTargetUser(
                    new EntityUser()
                ); // пустого пользователя во избеания ошибок, т.к. пользователь всегда должен быть
            }
        }

        return ['collection' => $aResult, 'count' => $iCount];
    }

    /**
     * Возвращает количество заметок у пользователя
     *
     * @param int $iUserId ID пользователя
     *
     * @return int
     */
    public
    function GetCountUserNotesByUserId(
        $iUserId
    ) {
        return $this->oMapper->GetCountUserNotesByUserId($iUserId);
    }

    /**
     * Возвращет заметку по автору и пользователю
     *
     * @param int $iTargetUserId ID пользователя о ком заметка
     * @param int $iUserId       ID пользователя автора заметки
     *
     * @return EntityUserNote
     * @throws \Exception
     */
    public
    function GetUserNote(
        $iTargetUserId,
        $iUserId
    ) {
        return $this->oMapper->GetUserNote($iTargetUserId, $iUserId);
    }

    /**
     * Возвращает заметку по ID
     *
     * @param int $iId ID заметки
     *
     * @return \App\Entities\EntityUserNote
     */
    public
    function GetUserNoteById(
        $iId
    ) {
        return $this->oMapper->GetUserNoteById($iId);
    }

    /**
     * Возвращает список заметок пользователя по ID целевых юзеров
     *
     * @param array $aUserId Список ID целевых пользователей
     * @param int   $sUserId ID пользователя, кто оставлял заметки
     *
     * @return array
     */
    public
    function GetUserNotesByArray(
        $aUserId,
        $sUserId
    ) {
        if (!$aUserId) {
            return [];
        }
        if (!is_array($aUserId)) {
            $aUserId = [$aUserId];
        }
        $aUserId = array_unique($aUserId);
        $aNotes = [];
        $s = join(',', $aUserId);
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("user_notes_{$sUserId}_id_{$s}"))) {
            $data = $this->oMapper->GetUserNotesByArrayUserId($aUserId, $sUserId);
            foreach ($data as $oNote) {
                $aNotes[$oNote->getTargetUserId()] = $oNote;
            }

            $cache->Set(
                $aNotes,
                "user_notes_{$sUserId}_id_{$s}",
                ["user_note_change_by_user_{$sUserId}"],
                60 * 60 * 24 * 1
            );

            return $aNotes;
        }

        return $data;
    }

    /**
     * Удаляет заметку по ID
     *
     * @param int $iId ID заметки
     *
     * @return bool
     */
    public
    function DeleteUserNoteById(
        $iId
    ) {
        if ($oNote = $this->GetUserNoteById($iId)) {
            /** @var ModuleCache $cache */
            $cache = LS::Make(ModuleCache::class);
            $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ["user_note_change_by_user_{$oNote->getUserId()}"]);
        }

        return $this->oMapper->DeleteUserNoteById($iId);
    }

    /**
     * Сохраняет заметку в БД, если ее нет то создает новую
     *
     * @param \App\Entities\EntityUserNote $oNote Объект заметки
     *
     * @return bool|\App\Entities\EntityUserNote
     */
    public
    function SaveNote(
        $oNote
    ) {
        if (!$oNote->getDateAdd()) {
            $oNote->setDateAdd(date("Y-m-d H:i:s"));
        }

        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ["user_note_change_by_user_{$oNote->getUserId()}"]);
        if ($oNoteOld = $this->GetUserNote($oNote->getTargetUserId(), $oNote->getUserId())) {
            $oNoteOld->setText($oNote->getText());
            $this->oMapper->UpdateUserNote($oNoteOld);

            return $oNoteOld;
        } else {
            if ($iId = $this->oMapper->AddUserNote($oNote)) {
                $oNote->setId($iId);

                return $oNote;
            }
        }

        return false;
    }

    /**
     * Возвращает список префиксов логинов пользователей (для алфавитного указателя)
     *
     * @param int $iPrefixLength Длина префикса
     *
     * @return array
     */
    public
    function GetGroupPrefixUser(
        $iPrefixLength = 1
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("group_prefix_user_{$iPrefixLength}"))) {
            $data = $this->oMapper->GetGroupPrefixUser($iPrefixLength);
            $cache->Set($data, "group_prefix_user_{$iPrefixLength}", ["user_new"], 60 * 60 * 24 * 1);
        }

        return $data;
    }

    /**
     * Добавляет запись о смене емайла
     *
     * @param \App\Entities\EntityUserChangemail $oChangemail Объект смены емайла
     *
     * @return bool|\App\Entities\EntityUserChangemail
     */
    public
    function AddUserChangemail(
        $oChangemail
    ) {
        if ($sId = $this->oMapper->AddUserChangemail($oChangemail)) {
            $oChangemail->setId($sId);

            return $oChangemail;
        }

        return false;
    }

    /**
     * Обновляет запись о смене емайла
     *
     * @param EntityUserChangemail $oChangemail Объект смены емайла
     *
     * @return int
     */
    public
    function UpdateUserChangemail(
        $oChangemail
    ) {
        return $this->oMapper->UpdateUserChangemail($oChangemail);
    }

    /**
     * Возвращает объект смены емайла по коду подтверждения
     *
     * @param string $sCode Код подтверждения
     *
     * @return EntityUserChangemail|null
     */
    public
    function GetUserChangemailByCodeFrom(
        $sCode
    ) {
        return $this->oMapper->GetUserChangemailByCodeFrom($sCode);
    }

    /**
     * Возвращает объект смены емайла по коду подтверждения
     *
     * @param string $sCode Код подтверждения
     *
     * @return \App\Entities\EntityUserChangemail|null
     */
    public
    function GetUserChangemailByCodeTo(
        $sCode
    ) {
        return $this->oMapper->GetUserChangemailByCodeTo($sCode);
    }

    /**
     * Формирование процесса смены емайла в профиле пользователя
     *
     * @param \App\Entities\EntityUser $oUser    Объект пользователя
     * @param string                   $sMailNew Новый емайл
     *
     * @return bool|\App\Entities\EntityUserChangemail
     */
    public
    function MakeUserChangemail(
        $oUser,
        $sMailNew
    ) {
        $oChangemail = new EntityUserChangemail();
        $oChangemail->setUserId($oUser->getId());
        $oChangemail->setDateAdd(date("Y-m-d H:i:s"));
        $oChangemail->setDateExpired(date("Y-m-d H:i:s", time() + 3 * 24 * 60 * 60)); // 3 дня для смены емайла
        $oChangemail->setMailFrom($oUser->getMail() ? $oUser->getMail() : '');
        $oChangemail->setMailTo($sMailNew);
        $oChangemail->setCodeFrom(func_generator(32));
        $oChangemail->setCodeTo(func_generator(32));
        if ($this->AddUserChangemail($oChangemail)) {
            /**
             * Если у пользователя раньше не было емайла, то сразу шлем подтверждение на новый емайл
             */
            if (!$oChangemail->getMailFrom()) {
                $oChangemail->setConfirmFrom(1);
                LS::Make(ModuleUser::class)->UpdateUserChangemail($oChangemail);
                /**
                 * Отправляем уведомление на новый емайл
                 */
                LS::Make(ModuleNotify::class)->Send(
                    $oChangemail->getMailTo(),
                    'notify.user_changemail_to.tpl',
                    LS::Make(ModuleLang::class)->Get('notify_subject_user_changemail'),
                    [
                        'oUser'       => $oUser,
                        'oChangemail' => $oChangemail,
                    ]
                );

            } else {
                /**
                 * Отправляем уведомление на старый емайл
                 */
                LS::Make(ModuleNotify::class)->Send(
                    $oUser,
                    'notify.user_changemail_from.tpl',
                    LS::Make(ModuleLang::class)->Get('notify_subject_user_changemail'),
                    [
                        'oUser'       => $oUser,
                        'oChangemail' => $oChangemail,
                    ]
                );
            }

            return $oChangemail;
        }

        return false;
    }

    /**
     * Ignore user
     *
     * @param string $sUserId
     * @param string $sUserIgnoreId
     * @param string $sType
     *
     * @return boolean
     */
    public
    function IgnoreUserByUser(
        $sUserId,
        $sUserIgnoreId,
        $sType
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Delete("user_ignore_{$sUserId}");
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['topic_update', "topic_update_user_{$sUserIgnoreId}"]);
        if ($this->oMapper->IgnoreUserByUser($sUserId, $sUserIgnoreId, $sType) === false) {
            return false;
        }

        return true;
    }

    /**
     * Unignore user
     *
     * @param string $sUserId
     * @param string $sUserIgnoreId
     * @param string $sType
     *
     * @return boolean
     */
    public
    function UnIgnoreUserByUser(
        $sUserId,
        $sUserIgnoreId,
        $sType
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Delete("user_ignore_{$sUserId}");
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['topic_update', "topic_update_user_{$sUserIgnoreId}"]);

        return $this->oMapper->UnIgnoreUserByUser($sUserId, $sUserIgnoreId, $sType);
    }

    /**
     * Is user ignore user
     *
     * @param int    $sUserId
     * @param int    $sUserIgnoredId
     * @param string $sType
     *
     * @return boolean
     */
    public
    function IsUserIgnoredByUser(
        $sUserId,
        $sUserIgnoredId,
        $sType
    ) {
        $aIgnored = $this->GetIgnoredUsersByUser($sUserId, $sType);

        return in_array($sUserIgnoredId, $aIgnored);
    }

    /**
     * Get ignored user ids by user
     *
     * @param string $sUserId
     * @param string $sType
     *
     * @return array
     */
    public
    function GetIgnoredUsersByUser(
        $sUserId,
        $sType = null
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("user_ignore_{$sUserId}"))) {
            if ($data = $this->oMapper->GetIgnoredUsersByUser($sUserId, $sType)) {
                $cache->Set($data, "user_ignore_{$sUserId}", ['users_ignorance'], 60 * 60 * 24 * 1);
            }
        }
        if (!is_null($sType)) {
            $aResult = [];
            foreach ($data as $id => $aTypes) {
                if (array_search($sType, $aTypes) !== false) {
                    array_push($aResult, $id);
                }
            }
            $data = $aResult;
        }

        return $data;
    }

    public
    function GetForbidIgnoredUsers()
    {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        if (false === ($data = $cache->Get("user_forbid_ignore"))) {
            if ($data = $this->oMapper->GetForbidIgnoredUsers()) {
                $cache->Set($data, "user_forbid_ignore", [], 60 * 60 * 24 * 1);
            }
        }

        return $data;
    }

    public
    function AllowIgnoreUser(
        $sUserId
    ) {
        /** @var \Engine\Modules\ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Delete("user_forbid_ignore");
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['users_ignorance']);
        if ($this->oMapper->AllowIgnoreUser($sUserId) === false) {
            return false;
        }

        return true;
    }

    public
    function ForbidIgnoreUser(
        $sUserId
    ) {
        /** @var ModuleCache $cache */
        $cache = LS::Make(ModuleCache::class);
        $cache->Delete("user_forbid_ignore");
        $cache->Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['users_ignorance', 'topic_update']);
        if ($this->oMapper->ForbidIgnoreUser($sUserId) === false) {
            return false;
        }
        $this->oMapper->ClearIgnoranceUser($sUserId);

        return true;
    }

    public
    function isBanned(
        $sUserId
    ) {
        $data = $this->oMapper->GetBan($sUserId);
        if (!$data) {
            return false;
        }

        return (int)$data["banunlim"] || (int)$data["banactive"];
    }

    public
    function GetBan(
        $sUserId
    ) {
        $data = $this->oMapper->GetBan($sUserId);

        return $data;
    }

    public
    function Ban(
        $nUserId,
        $nModerId,
        $dDate,
        $nUnlim,
        $sComment = null
    ) {
        $data = $this->oMapper->SetBan($nUserId, $nModerId, $dDate, $nUnlim, $sComment);

        return $data;
    }

    public
    function Unban(
        $nUserId
    ) {
        $data = $this->oMapper->DelBanUser($nUserId);

        return $data;
    }

    public
    function isRegistrationClosed()
    {
        $data = $this->oMapper->GetInvitesConfig();

        if ($data == "b:1;") {
            return true;
        }

        return false;
    }

    /**
     * Генерирует ключ для куков
     *
     * @param \App\Entities\EntityUser $oUser Объект пользователя
     *
     * @return string
     */
    public
    function GenerateUserKey(
        EntityUser $oUser
    ) {
        $payload = [
            'sub' => $oUser->getId(),
            'exp' => time() + Config::Get('module.user.session_time'),
            'iat' => time()
        ];

        return LS::Make(ModuleAuth::class)->GenerateKey($payload, 'user', false, 'msgpack');
    }

    public function CheckUserKey(string $key, int $uid): bool
    {
        try {
            $payload = LS::Make(ModuleAuth::class)->VerifyKey($key, 'msgpack');

            return $payload['sub'] == $uid;
        } catch (AuthException $e) {
            return false;
        }
    }

    /**
     * Возвращает биты привелегий пользователя
     *
     * @param $iUserId
     *
     * @return integer
     */
    public
    function GetUserPrivileges(
        $iUserId
    ) {
        return $this->oMapper->GetUserPrivileges($iUserId);
    }

    /**
     * Устанавливает биты привелегий пользователя
     *
     * @param $iUserId
     * @param $iPrivs
     */
    public
    function SetUserPrivileges(
        $iUserId,
        $iPrivs
    ) {
        $this->oMapper->SetUserPrivileges($iUserId, $iPrivs);
    }
}
