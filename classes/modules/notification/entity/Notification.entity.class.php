<?php
/**
 * Created by PhpStorm.
 * User: frumscepend
 * Date: 7/30/18
 * Time: 1:52 AM
 */

/**
 * Объект сущности уведомлений
 *
 * @package modules.notification
 */
class ModuleNotification_EntityNotification extends Entity {

	/**
	 * Возвращает ID уведомления
	 *
	 * @return int|null
	 */
	public function getId() {
		return $this->_getDataOne('notification_id');
	}

	/**
	 * Возвращает ID пользователя
	 *
	 * @return int|null
	 */
	public function getUserId() {
		return $this->_getDataOne('user_id');
	}

	/**
	 * Возвращает дату уведомления
	 *
	 * @return string|null
	 */
	public function getDate() {
		return $this->_getDataOne('date');
	}

	/**
	 * Возвращает текст уведомления
	 *
	 * @return string|null
	 */
	public function getText() {
		return $this->_getDataOne('text');
	}

	/**
	 * Возвращает заголовок уведомления
	 *
	 * @return string|null
	 */
	public function getTitle() {
		return $this->_getDataOne('title');
	}

	/**
	 * Возвращает ссылку уведомления
	 *
	 * @return string|null
	 */
	public function getLink() {
		return $this->_getDataOne('link');
	}

	/**
	 * Возвращает оценку
	 *
	 * @return int|null
	 */
	public function getRating() {
		return $this->_getDataOne('rating');
	}

	/**
	 * Возвращает тип уведомления
	 *
	 * @return int|null
	 */
	public function getType() {
		return $this->_getDataOne('notification_type');
	}

	/**
	 * Возвращает тип таргета
	 *
	 * @return string|null
	 */
	public function getTargetType() {
		return $this->_getDataOne('target_type');
	}

	/**
	 * Возвращает id таргета
	 *
	 * @return int|null
	 */
	public function getTargetId() {
		return $this->_getDataOne('target_id');
	}

	/**
	 * Устанавливает ID уведомления
	 *
	 * @param int $data
	 */
	public function setId($data) {
		$this->_aData['notification_id']=$data;
	}

	/**
	 * Устанавливает ID пользователя
	 *
	 * @param int $data
	 */
	public function setUserId($data) {
		$this->_aData['user_id']=$data;
	}

	/**
	 * Устанавливает дату уведомления
	 *
	 * @param string $data
	 */
	public function setDate($data) {
		$this->_aData['date']=$data;
	}

	/**
	 * Устанавливает текст уведомления
	 *
	 * @param string $data
	 */
	public function setText($data) {
		$this->_aData['text']=$data;
	}

	/**
	 * Устанавливает заголовок уведомления
	 *
	 * @param string $data
	 */
	public function setTitle($data) {
		$this->_aData['title']=$data;
	}

	/**
	 * Устанавливает ссылку уведомления
	 *
	 * @param string $data
	 */
	public function setLink($data) {
		$this->_aData['link']=$data;
	}

	/**
	 * Устанавливает оценку пользователя
	 *
	 * @param int $data
	 */
	public function setRating($data) {
		$this->_aData['rating']=$data;
	}

	/**
	 * Устанавливает тип уведомления
	 *
	 * @param int $data
	 */
	public function setType($data) {
		$this->_aData['notification_type']=$data;
	}

	/**
	 * Устанавливает тип таргета
	 *
	 * @param string $data
	 */
	public function setTargetType($data) {
		$this->_aData['target_type']=$data;
	}

	/**
	 * Устанавливает id таргета
	 *
	 * @param int $data
	 */
	public function setTargetId($data) {
		$this->_aData['target_id']=$data;
	}
}
?>