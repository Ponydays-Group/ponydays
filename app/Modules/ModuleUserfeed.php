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

use App\Mappers\MapperUserfeed;
use Engine\Config;
use Engine\Engine;
use Engine\LS;
use Engine\Module;

/**
 * Модуль пользовательских лент контента (топиков)
 *
 * @package modules.userfeed
 * @since   1.0
 */
class ModuleUserfeed extends Module
{
    /**
     * Подписки на топики по блогу
     */
    const SUBSCRIBE_TYPE_BLOG = 1;
    /**
     * Подписки на топики по юзеру
     */
    const SUBSCRIBE_TYPE_USER = 2;
    /**
     * Объект маппера
     *
     * @var \App\Mappers\MapperUserfeed|null
     */
    protected $oMapper = null;

    /**
     * Инициализация модуля
     */
    public function Init()
    {
        $this->oMapper = Engine::MakeMapper(MapperUserfeed::class);
    }

    /**
     * Подписать пользователя
     *
     * @param int $iUserId        ID подписываемого пользователя
     * @param int $iSubscribeType Тип подписки (см. константы класса)
     * @param int $iTargetId      ID цели подписки
     *
     * @return bool
     */
    public function subscribeUser($iUserId, $iSubscribeType, $iTargetId)
    {
        return $this->oMapper->subscribeUser($iUserId, $iSubscribeType, $iTargetId);
    }

    /**
     * Отписать пользователя
     *
     * @param int $iUserId        ID подписываемого пользователя
     * @param int $iSubscribeType Тип подписки (см. константы класса)
     * @param int $iTargetId      ID цели подписки
     *
     * @return bool
     */
    public function unsubscribeUser($iUserId, $iSubscribeType, $iTargetId)
    {
        return $this->oMapper->unsubscribeUser($iUserId, $iSubscribeType, $iTargetId);
    }

    /**
     * Получить ленту топиков по подписке
     *
     * @param int $iUserId ID пользователя, для которого получаем ленту
     * @param int $iCount  Число получаемых записей (если null, из конфига)
     * @param int $iFromId Получить записи, начиная с указанной
     *
     * @return array
     */
    public function read($iUserId, $iCount = null, $iFromId = null)
    {
        if (!$iCount) {
            $iCount = Config::Get('module.userfeed.count_default');
        }
        $aUserSubscribes = $this->oMapper->getUserSubscribes($iUserId);
        $aInaccessible =
            LS::Make(ModuleBlog::class)->GetInaccessibleBlogsByUser(LS::Make(ModuleUser::class)->GetUserById($iUserId));
        if (LS::Make(ModuleUser::class)->GetUserById($iUserId)->isAdministrator()) {
            $aInaccessible = [0];
        }
        $aTopicsIds = $this->oMapper->readFeed($aUserSubscribes, $iCount, $iFromId, $aInaccessible);

        return LS::Make(ModuleTopic::class)->getTopicsAdditionalData($aTopicsIds);
    }

    /**
     * Получить список подписок пользователя
     *
     * @param int $iUserId ID пользователя, для которого загружаются подписки
     *
     * @return array
     */
    public function getUserSubscribes($iUserId)
    {
        $aUserSubscribes = $this->oMapper->getUserSubscribes($iUserId);
        $aResult = ['blogs' => [], 'users' => []];
        if (count($aUserSubscribes['blogs'])) {
            $aBlogs = LS::Make(ModuleBlog::class)->getBlogsByArrayId($aUserSubscribes['blogs']);
            foreach ($aBlogs as $oBlog) {
                $aResult['blogs'][$oBlog->getId()] = $oBlog;
            }
        }
        if (count($aUserSubscribes['users'])) {
            $aUsers = LS::Make(ModuleUser::class)->getUsersByArrayId($aUserSubscribes['users']);
            foreach ($aUsers as $oUser) {
                $aResult['users'][$oUser->getId()] = $oUser;
            }
        }

        return $aResult;
    }
}
