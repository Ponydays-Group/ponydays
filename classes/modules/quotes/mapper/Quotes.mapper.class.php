<?php
/**
 * Маппер цитатника
 *
 * User: silvman
 * Date: 19.08.17
 * Time: 3:13
 */

class ModuleQuotes_MapperQuotes extends Mapper {

	/**
	 * Добавляет цитату в таблицу
	 *
	 * @param string $data
	 * @return int
	 */
	public function Add (string $data): int {
		$sql = "INSERT INTO " . Config::Get('db.table.quotes') . " 
			(data) 
			VALUES(?)
		";

		if ($iId = $this->oDb->query($sql, $data)) {
			return $iId;
		}

		return 0;
	}

	/**
	 * Удаляет цитату из таблицы
	 *
	 * @param int $id
	 * @return bool
	 */
	public function Delete (int $id): bool {
		$sql = "DELETE FROM " . Config::Get('db.table.quotes') . " WHERE id = ? ";

		if ($this->oDb->query($sql, $id)) {
			return true;
		}

		return false;
	}

	/**
	 * Обновляет содержание цитаты в таблице
	 *
	 * @param int $id
	 * @param string $data
	 * @return bool
	 */
	public function Update (int $id, string $data) {
		$sql = "UPDATE " . Config::Get('db.table.quotes') . " 
			SET data = ? 
			WHERE id = ?d
		";

		if ($this->oDb->query($sql, $data, $id)) {
			return true;
		}

		return false;
	}

	/**
	 * Возвращает массив всех цитат в таблице
	 *
	 * @return array
	 */
	public function GetArray (): array {
		$sql = "SELECT * FROM " . Config::Get('db.table.quotes');
		if ($aQuotes = $this->oDb->select($sql)) {
			return $aQuotes;
		}

		return [];
	}

	/**
	 * Возвращает цитату по ID, если существует. Возвращает пустую строку, если нет.
	 *
	 * @param int $id
	 * @return string
	 */
	public function GetById (int $id): string {
		$sql = "SELECT data FROM " . Config::Get('db.table.quotes') . "
			WHERE id = ?
		";

		if ($aRows = $this->oDb->query($sql, $id)) {
			return $aRows[0]['data'];
		}

		return "";
	}

}