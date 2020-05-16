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

use App\Entities\EntitySubscribe;
use App\Entities\EntityUser;
use App\Mappers\MapperSubscribe;
use Engine\Engine;
use Engine\LS;
use Engine\Module;

/**
 * Модуль Subscribe - подписки пользователей
 *
 * @package modules.subscribe
 * @since   1.0
 */
class ModuleSubscribe extends Module
{
    /**
     * Объект маппера
     *
     * @var \App\Mappers\MapperSubscribe
     */
    protected $oMapper;
    /**
     * Объект текущего пользователя
     *
     * @var EntityUser|null
     */
    protected $oUserCurrent;
    /**
     * Список доступных объектов подписок с параметрами
     * На данный момент допустим параметр allow_for_guest=>1 - указывает на возможность создавать подписку для гостя
     *
     * @var array
     */
    protected $aTargetTypes = [
        'topic_new_comment' => [],
    ];

    /**
     * Инициализация
     *
     */
    public function Init()
    {
        $this->oMapper = Engine::MakeMapper(MapperSubscribe::class);
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
     * @param string $sTargetType Тип
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
     * Проверяет разрешен ли данный тип в подписке
     *
     * @param string $sTargetType Тип
     *
     * @return bool
     */
    public function IsAllowTargetType($sTargetType)
    {
        return in_array($sTargetType, array_keys($this->aTargetTypes));
    }

    /**
     * Проверка объекта подписки
     *
     * @param string $sTargetType Тип
     * @param int    $iTargetId   ID владельца
     * @param int    $iStatus     Статус подписки
     *
     * @return bool
     */
    public function CheckTarget($sTargetType, $iTargetId, $iStatus = null)
    {
        $sMethod = 'CheckTarget'.func_camelize($sTargetType);
        if (method_exists($this, $sMethod)) {
            return $this->$sMethod($iTargetId, $iStatus);
        }

        return false;
    }

    /**
     * Возвращает URL страницы с объектом подписки
     * Актуально при переходе по ссылки с отпиской от рассылки и последующим редиректом
     *
     * @param string $sTargetType Тип
     * @param int    $iTargetId   ID владельца
     *
     * @return bool
     */
    public function GetUrlTarget($sTargetType, $iTargetId)
    {
        $sMethod = 'GetUrlTarget'.func_camelize($sTargetType);
        if (method_exists($this, $sMethod)) {
            return $this->$sMethod($iTargetId);
        }

        return false;
    }

    /**
     * Проверка на подписку для гостей
     *
     * @param string $sTargetType Тип
     *
     * @return bool
     */
    public function IsAllowTargetForGuest($sTargetType)
    {
        if ($this->IsAllowTargetType($sTargetType)) {
            if (isset($this->aTargetTypes[$sTargetType]['allow_for_guest'])
                and $this->aTargetTypes[$sTargetType]['allow_for_guest']
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Добавляет подписку в БД
     *
     * @param \App\Entities\EntitySubscribe $oSubscribe Объект подписки
     *
     * @return \App\Entities\EntitySubscribe|bool
     */
    public function AddSubscribe($oSubscribe)
    {
        if ($sId = $this->oMapper->AddSubscribe($oSubscribe)) {
            $oSubscribe->setId($sId);

            return $oSubscribe;
        }

        return false;
    }

    /**
     * Создает подписку, если уже есть, то возвращает существующую
     *
     * @param string $sTargetType Тип
     * @param string $sTargetId   ID владельца
     * @param string $sMail       Емайл
     *
     * @return EntitySubscribe|bool
     */
    public function AddSubscribeSimple($sTargetType, $sTargetId, $sMail)
    {
        if (!$sMail) {
            return false;
        }
        /** @var ModuleSubscribe $sub */
        $sub = LS::Make(ModuleSubscribe::class);
        if (!($oSubscribe = $sub->GetSubscribeByTargetAndMail($sTargetType, $sTargetId, $sMail))) {
            $oSubscribe = new EntitySubscribe();
            $oSubscribe->setTargetType($sTargetType);
            $oSubscribe->setTargetId($sTargetId);
            $oSubscribe->setMail($sMail);
            $oSubscribe->setDateAdd(date("Y-m-d H:i:s"));
            $oSubscribe->setKey(func_generator(32));
            $oSubscribe->setIp(func_getIp());
            $oSubscribe->setStatus(1);
            $sub->AddSubscribe($oSubscribe);
        }

        return $oSubscribe;
    }

    /**
     * Обновление подписки
     *
     * @param \App\Entities\EntitySubscribe $oSubscribe Объект подписки
     *
     * @return int
     */
    public function UpdateSubscribe($oSubscribe)
    {
        return $this->oMapper->UpdateSubscribe($oSubscribe);
    }

    /**
     * Возвращает список подписок по фильтру
     *
     * @param array $aFilter   Фильтр
     * @param array $aOrder    Сортировка
     * @param int   $iCurrPage Номер страницы
     * @param int   $iPerPage  Количество элементов на страницу
     *
     * @return array('collection'=>array,'count'=>int)
     */
    public function GetSubscribes($aFilter, $aOrder, $iCurrPage, $iPerPage)
    {
        return [
            'collection' => $this->oMapper->GetSubscribes($aFilter, $aOrder, $iCount, $iCurrPage, $iPerPage),
            'count'      => $iCount
        ];
    }

    /**
     * Возвращает подписку по объекту подписки и емайлу
     *
     * @param string $sTargetType Тип
     * @param int    $iTargetId   ID владельца
     * @param string $sMail       Емайл
     *
     * @return \App\Entities\EntitySubscribe|null
     */
    public function GetSubscribeByTargetAndMail($sTargetType, $iTargetId, $sMail)
    {
        $aRes = $this->GetSubscribes(
            ['target_type' => $sTargetType, 'target_id' => $iTargetId, 'mail' => $sMail],
            [],
            1,
            1
        );
        if (isset($aRes['collection'][0])) {
            return $aRes['collection'][0];
        }

        return null;
    }

    /**
     * Возвращает подписку по ключу
     *
     * @param string $sKey Ключ
     *
     * @return EntitySubscribe|null
     */
    public function GetSubscribeByKey($sKey)
    {
        $aRes = $this->GetSubscribes(['key' => $sKey], [], 1, 1);
        if (isset($aRes['collection'][0])) {
            return $aRes['collection'][0];
        }

        return null;
    }

    /**
     * Производит отправку писем по подписчикам подписки
     *
     * @param int    $sTargetType  Тип объекта подписки
     * @param int    $iTargetId    ID объекта подписки
     * @param string $sTemplate    Имя шаблона письма, например, notify.mail.tpl
     * @param string $sTitle       Заголовок письма
     * @param array  $aParams      Параметра для передачи в шаблон письма
     * @param array  $aExcludeMail Список емайлов на которые НЕ нужно отправлять
     * @param string $sPluginName  Название или класс плагина для корректной отправки
     */
    public function Send(
        $sTargetType,
        $iTargetId,
        $sTemplate,
        $sTitle,
        $aParams = [],
        $aExcludeMail = [],
        $sPluginName = null
    ) {
        $iPage = 1;
        /** @var ModuleSubscribe $sub */
        $sub = LS::Make(ModuleSubscribe::class);
        $aSubscribes = $sub->GetSubscribes(
            ['target_type' => $sTargetType, 'target_id' => $iTargetId, 'status' => 1, 'exclude_mail' => $aExcludeMail],
            [],
            $iPage,
            20
        );
        while ($aSubscribes['collection']) {
            $iPage++;
            foreach ($aSubscribes['collection'] as $oSubscribe) {
                $aParams['sSubscribeKey'] = $oSubscribe->getKey();
                LS::Make(ModuleNotify::class)->Send(
                    $oSubscribe->getMail(),
                    $sTemplate,
                    $sTitle,
                    $aParams,
                    $sPluginName
                );
            }
            $aSubscribes = $sub->GetSubscribes(
                ['target_type' => $sTargetType, 'target_id' => $iTargetId, 'status' => 1],
                [],
                $iPage,
                20
            );
        }
    }

    /**
     * Проверка объекта подписки с типом "topic_new_comment"
     * Название метода формируется автоматически
     *
     * @param int $iTargetId ID владельца
     * @param int $iStatus   Статус
     *
     * @return bool
     */
    public function CheckTargetTopicNewComment($iTargetId, $iStatus)
    {
        if ($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($iTargetId)) {
            /**
             * Топик может быть в закрытом блоге, поэтому необходимо разрешить подписку только если пользователь в нем состоит, или является автором блога
             * Отписываться разрешаем с любого топика
             */
            if ($iStatus == 1 and $oTopic->getBlog()->getType() == 'close') {
                if (!$this->oUserCurrent or !($oTopic->getBlog()->getOwnerId() == $this->oUserCurrent->getId()
                        or LS::Make(ModuleBlog::class)->GetBlogUserByBlogIdAndUserId(
                            $oTopic->getBlogId(),
                            $this->oUserCurrent->getId()
                        ))
                ) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Возвращает URL на страницы объекта подписки с типом "topic_new_comment"
     * Название метода формируется автоматически
     *
     * @param int $iTargetId ID топика
     *
     * @return string|bool
     */
    public function GetUrlTargetTopicNewComment($iTargetId)
    {
        if ($oTopic = LS::Make(ModuleTopic::class)->GetTopicById($iTargetId) and $oTopic->getPublish()) {
            return $oTopic->getUrl();
        }

        return false;
    }
}
