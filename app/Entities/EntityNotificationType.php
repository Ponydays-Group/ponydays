<?php

namespace App\Entities;

use Engine\Entity;

/**
 * Объект сущности типа уведомлений
 *
 * @package modules.notification
 */
class EntityNotificationType extends Entity
{

    /**
     * Возвращает ID типа уведомления
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getDataOne('notification_type_id');
    }

    /**
     * Возвращает имя типа уведомления
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->_getDataOne('name');
    }

    /**
     * Устанавливает ID типа уведомления
     *
     * @param int $data
     */
    public function setId($data)
    {
        $this->_aData['notification_type_id'] = $data;
    }

    /**
     * Устанавливает имя типа уведомления
     *
     * @param string $data
     */
    public function setTitle($data)
    {
        $this->_aData['name'] = $data;
    }
}
