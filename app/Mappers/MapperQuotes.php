<?php

namespace App\Mappers;

use Engine\Config;
use Engine\Mapper;

/**
 * Маппер цитатника
 *
 * User: silvman
 * Date: 19.08.17
 * Time: 3:13
 */

class MapperQuotes extends Mapper {

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
	 * Помечает цитату удалённой
	 *
	 * @param int $id
	 * @return bool
	 */
	public function Delete (int $id): bool {
		$sql = "UPDATE " . Config::Get('db.table.quotes') . " 
			SET deleted = 1 
			WHERE id = ?d
		";

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
	public function Update (int $id, string $data) : bool {
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
	 * Убирает флаг удаления
	 *
	 * @param int $id
	 * @return bool
	 */
	public function Restore (int $id): bool {
		$sql = "UPDATE " . Config::Get('db.table.quotes') . " 
			SET deleted = 0 
			WHERE id = ?d
		";

		if ($this->oDb->query($sql, $id)) {
			return true;
		}

		return false;
	}

	/**
	 * Возвращает массив цитат в таблице
	 *
	 * @param bool
	 * @return array
	 */
	public function GetArray (bool $bDeleted = false): array {
		if($bDeleted)
			$sql = "SELECT * FROM " . Config::Get('db.table.quotes') . " WHERE deleted = 1";
		else
			$sql = "SELECT * FROM " . Config::Get('db.table.quotes') . " WHERE deleted = 0";

		$aQuotes = [];

		if ($aRows = $this->oDb->select($sql)) {
			foreach ($aRows as $aRow) {
				$aQuotes[$aRow['id']] = $aRow['data'];
			}
		}

		return $aQuotes;
	}

	/**
	 * @param int $iCurrPage
	 * @param int $iPerPage
	 * @return array
	 */
	public function GetArrayForPage (int $iCurrPage, int $iPerPage): array {
		$sql ="SELECT
					*
				FROM
					".Config::Get('db.table.quotes')." 
				WHERE deleted = 0
				ORDER by id asc
				LIMIT ?d, ?d";

		$aQuotes = [];
		if($aRows = $this->oDb->select($sql, ($iCurrPage-1)*$iPerPage, $iPerPage)) {
			foreach ($aRows as $aRow) {
				$aQuotes[$aRow['id']] = $aRow['data'];
			}
		}

		return $aQuotes;
	}

	/**
	 * Возвращает цитату по ID, если существует. Возвращает пустую строку, если нет.
	 *
	 * @param int $id
	 * @return string
	 */
	public function GetById (int $id): string {
		$sql = "SELECT data FROM " . Config::Get('db.table.quotes') . "
			WHERE id = ? AND deleted = 0
		";

		if ($aRows = $this->oDb->query($sql, $id)) {
			return $aRows[0]['data'];
		}

		return "";
	}

	public function GetRandom() {
        $sql = "SELECT * FROM " . Config::Get('db.table.quotes') . " WHERE deleted = 0 ORDER BY RAND() LIMIT 1;";
        if ($aRows = $this->oDb->query($sql)) {
            return $aRows[0];
        }

        return array();
    }

	/**
	 * Возвращает количества элементов в таблице
	 *
	 * @param bool $bDeleted
	 * @return int
	 */
	public function GetCount (bool $bDeleted = false) : int {
		$val = ($bDeleted) ? '1' : '0';
		$sql = "SELECT COUNT(*) FROM " . Config::Get('db.table.quotes') . " WHERE deleted = $val";

		return (int)($this->oDb->query($sql))[0]['COUNT(*)'];
	}

	/**
	 * Возвращает массив айдишников. Используется для выбора случайного
	 *
	 * @return array
	 */
	public function GetIds () : array {
		$sql = "SELECT id FROM " . Config::Get('db.table.quotes') . " WHERE deleted = 0";

		$aIds = [];
		if ($aRows = $this->oDb->query($sql)) {
			foreach ($aRows as $aRow) {
				$aIds[] = $aRow['id'];
			}
		}

		return $aIds;
	}
}
